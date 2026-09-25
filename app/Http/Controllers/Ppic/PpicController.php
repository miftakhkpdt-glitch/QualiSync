<?php

namespace App\Http\Controllers\Ppic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseRequest; // <-- TAMBAHAN PENTING UNTUK MEMANGGIL TABEL PR

class PpicController extends Controller
{
    // Menampilkan Daftar PO Masuk dari Sales untuk PPIC
    public function indexPo()
    {
        $pos = \App\Models\SalesOrder::whereNull('deleted_at')
                                     ->orderBy('tanggal_po', 'desc')
                                     ->get();

        foreach ($pos as $po) {
            $master = DB::table('master_materials')->where('no_mm', $po->no_mm)->first();
            $po->nama_material = $master ? $master->nama_material : '-';
        }

        return view('ppic.dashboard-po', compact('pos'));
    }

    // Kalkulasi MRP (Material Requirements Planning) berdasarkan PO Sales
    public function calculateMrp($id)
    {
        $po = DB::table('sales_orders')->where('id', $id)->first();
        if (!$po) return redirect()->back()->with('error', 'PO tidak ditemukan.');

        $boms = DB::table('product_boms')->where('fg_mm', $po->no_mm)->get();

        // Ambil stok dari semua departemen
        $warehouseStocks = DB::table('warehouse_stocks')->select('no_mm', DB::raw('SUM(qty) as total_qty'))->groupBy('no_mm')->get()->keyBy('no_mm');
        $karantinaStocks = DB::table('karantina_stocks')->select('no_mm', DB::raw('SUM(qty) as total_qty'))->groupBy('no_mm')->get()->keyBy('no_mm');
        $qualityStocks = DB::table('quality_stocks')->select('no_mm', DB::raw('SUM(qty) as total_qty'))->groupBy('no_mm')->get()->keyBy('no_mm');
        $produksiStocks = DB::table('produksi_stocks')->select('no_mm', DB::raw('SUM(qty) as total_qty'))->groupBy('no_mm')->get()->keyBy('no_mm');
        $materialsMap = DB::table('master_materials')->get()->keyBy('no_mm');

        // FUNGSI BARU (ANTI-LEMOT): Cek PR mana yang sedang "Pending" dalam 1 kali tarikan data saja
        $komponenMms = $boms->pluck('component_mm')->toArray();
        $pendingPrs = DB::table('purchase_requests')
                        ->whereIn('no_mm', $komponenMms)
                        ->where('status', 'Pending')
                        ->pluck('no_mm')->toArray();

        $mrpResults = [];
        foreach ($boms as $bom) {
            $grossRequirement = $po->qty * $bom->qty_usage;
            
            $stockReady = isset($warehouseStocks[$bom->component_mm]) ? $warehouseStocks[$bom->component_mm]->total_qty : 0;
            $stockKarantina = isset($karantinaStocks[$bom->component_mm]) ? $karantinaStocks[$bom->component_mm]->total_qty : 0;
            $stockQc = isset($qualityStocks[$bom->component_mm]) ? $qualityStocks[$bom->component_mm]->total_qty : 0; 
            $stockProduksi = isset($produksiStocks[$bom->component_mm]) ? $produksiStocks[$bom->component_mm]->total_qty : 0; 

            // Hitung kekurangan (HANYA dikurangi Stok Ready)
            $netRequirement = $grossRequirement - $stockReady;
            if ($netRequirement < 0) $netRequirement = 0;

            // Cek apakah material ini masuk dalam daftar PR yang sedang Pending
            $hasPendingPr = in_array($bom->component_mm, $pendingPrs);

            $mrpResults[] = [
                'component_mm' => $bom->component_mm,
                'component_name' => $materialsMap[$bom->component_mm]->nama_material ?? '-',
                'satuan' => $bom->satuan ?? 'Pcs',
                'qty_usage' => $bom->qty_usage,
                'gross_requirement' => $grossRequirement,
                'stock_ready' => $stockReady,
                'stock_qc' => $stockQc, 
                'stock_produksi' => $stockProduksi,
                'stock_karantina' => $stockKarantina, 
                'net_requirement' => $netRequirement,
                'has_pending_pr' => $hasPendingPr, // <-- Variabel Penanda PR dikirim ke Blade
            ];
        }

        $fgInfo = DB::table('master_materials')->where('no_mm', $po->no_mm)->first();
        $po->nama_material = $fgInfo ? $fgInfo->nama_material : '-';

        return view('ppic.mrp-result', compact('po', 'mrpResults'));
    }

    // ====================================================================
    // FUNGSI 1: BUKA HALAMAN FORM PR DARI LAYAR MRP
    // ====================================================================
    public function createPrForm(Request $request)
    {
        $no_mm = $request->no_mm;
        $qty = $request->qty;

        $material = DB::table('master_materials')->where('no_mm', $no_mm)->first();
        $nama_material = $material ? $material->nama_material : 'Nama Material Tidak Ditemukan';
        
        // --- BARIS BARU: Ambil satuan dari master_materials ---
        $satuan = $material ? $material->satuan : 'Unit'; 

        // Pastikan $satuan ikut dipanggil di compact()
        return view('ppic.create-pr', compact('no_mm', 'qty', 'nama_material', 'satuan'));
    }

    // ====================================================================
    // FUNGSI 2: SIMPAN DATA PR KE DATABASE SETELAH KLIK "KIRIM"
    // ====================================================================
    public function storePr(Request $request)
    {
        $request->validate([
            'no_mm' => 'required|string|max:50',
            'qty' => 'required|numeric|min:0.1',
            'estimasi_tiba' => 'required|date',
            'kategori' => 'required|string',
            'catatan' => 'nullable|string' 
        ]);

        try {
            DB::beginTransaction();

            $cekPr = PurchaseRequest::where('no_mm', $request->no_mm)
                                    ->where('status', 'Pending')
                                    ->lockForUpdate()
                                    ->first();

            if ($cekPr) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Dokumen PR untuk material ini sudah ada dan sedang menunggu diproses oleh Purchasing.');
            }

            // ==============================================================
            // GENERATOR NOMOR PR KPDT OTOMATIS
            // ==============================================================
            $bulan = date('m'); // Misal: 09
            $tahun = date('Y'); // Misal: 2026
            $departemen = 'PPPL'; 
            $kategori = $request->kategori; 

            // Cari PR terakhir di departemen PPPL, bulan, dan tahun ini
            $lastPr = PurchaseRequest::where('no_pr', 'like', '%/KPDT/' . $departemen . '/REQP/%/' . $bulan . '/' . $tahun)
                                     ->orderBy('id', 'desc')
                                     ->first();

            if ($lastPr) {
                // Ambil 4 digit pertama (urutan), lalu tambah 1
                $urutTerakhir = (int) explode('/', $lastPr->no_pr)[0];
                $urutBaru = str_pad($urutTerakhir + 1, 4, '0', STR_PAD_LEFT);
            } else {
                // Jika belum ada dokumen di bulan ini, mulai dari 0001
                $urutBaru = '0001';
            }

            // Format Akhir: {URUTAN}/KPDT/PPPL/REQP/{KATEGORI}/{BULAN}/{TAHUN}
            $noPr = sprintf('%s/KPDT/%s/REQP/%s/%s/%s', $urutBaru, $departemen, $kategori, $bulan, $tahun);
            // ==============================================================

            PurchaseRequest::create([
                'no_pr' => $noPr,
                'tanggal' => date('Y-m-d'),
                'no_mm' => $request->no_mm,
                'qty' => $request->qty,
                'estimasi_tiba' => $request->estimasi_tiba,
                'kategori' => $request->kategori, 
                'status' => 'Pending',
                'status_approval' => 'Pending', 
                'pemohon_id' => \Illuminate\Support\Facades\Auth::id() ?? 1,
                'catatan' => $request->catatan ?? 'Hasil hitung otomatis sistem MRP', 
                'departemen' => 'PPIC', 
            ]);

            DB::commit();
            return redirect('/ppic/mrp-dashboard')->with('success', 'Luar Biasa! Dokumen Purchase Request (PR) bernomor ' . $noPr . ' telah terkirim ke departemen Purchasing.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem. Error: ' . $e->getMessage());
        }
    }

    // ====================================================================
    // RIWAYAT PR PPIC (DENGAN FILTER PENCARIAN)
    // ====================================================================
    public function riwayatPr(Request $request)
    {
        // 1. Tangkap inputan pencarian
        $search = $request->query('search');
        $statusFilter = $request->query('status');

        // 2. Query Dasar
        $query = \Illuminate\Support\Facades\DB::table('purchase_requests')
            ->leftJoin('master_materials', 'purchase_requests.no_mm', '=', 'master_materials.no_mm')
            ->select('purchase_requests.*', 'master_materials.nama_material', 'master_materials.satuan');

        // 3. Logika Filter Status
        if ($statusFilter) {
            $query->where('purchase_requests.status', $statusFilter);
        }

        // 4. Logika Filter Pencarian Ketik (No PR, No MM, Nama Material)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('purchase_requests.no_pr', 'like', '%' . $search . '%')
                  ->orWhere('purchase_requests.no_mm', 'like', '%' . $search . '%')
                  ->orWhere('master_materials.nama_material', 'like', '%' . $search . '%');
            });
        }

        // 5. Eksekusi Query
        $riwayatPr = $query->orderBy('purchase_requests.created_at', 'desc')->get();

        return view('ppic.riwayat-pr', compact('riwayatPr', 'search', 'statusFilter'));
    }
}