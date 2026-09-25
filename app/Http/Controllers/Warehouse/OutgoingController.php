<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesOrder;
use Illuminate\Support\Facades\DB;

class OutgoingController extends Controller
{
    public function index()
    {
        // 1. Tarik data PO yang OSPO-nya masih ada (lebih dari 0)
        $pos = \Illuminate\Support\Facades\DB::table('sales_orders')
            ->leftJoin('master_customers', 'sales_orders.nama_customer', '=', 'master_customers.nama_customer')
            ->select('sales_orders.*', 'master_customers.alamat')
            ->where('sales_orders.ospo', '>', 0)
            ->orderBy('sales_orders.tanggal_po', 'DESC')
            ->get();
        
        // Tarik riwayat Surat Jalan dan JOIN untuk mengambil nama produk
        $outgoings = DB::table('warehouse_outgoings')
            ->leftJoin('sales_orders', 'warehouse_outgoings.no_po', '=', 'sales_orders.no_po')
            ->select('warehouse_outgoings.*', 'sales_orders.nama_produk')
            ->orderBy('warehouse_outgoings.created_at', 'DESC')
            ->get();

        return view('warehouse.outgoing', compact('pos', 'outgoings'));
    }
    public function approvalPage()
    {
        // 1. Tarik data Pending dan JOIN dengan sales_orders untuk mendapatkan nama_produk
        $pending = \Illuminate\Support\Facades\DB::table('warehouse_outgoings')
            ->leftJoin('sales_orders', 'warehouse_outgoings.no_po', '=', 'sales_orders.no_po')
            ->where('warehouse_outgoings.status', 'Pending')
            ->select('warehouse_outgoings.*', 'sales_orders.nama_produk', 'sales_orders.nama_customer')
            ->orderBy('warehouse_outgoings.created_at', 'DESC')
            ->get();

        // 2. Tarik data Riwayat (Approved) dan JOIN juga
        $approved = \Illuminate\Support\Facades\DB::table('warehouse_outgoings')
            ->leftJoin('sales_orders', 'warehouse_outgoings.no_po', '=', 'sales_orders.no_po')
            ->where('warehouse_outgoings.status', 'Approved')
            ->select('warehouse_outgoings.*', 'sales_orders.nama_produk', 'sales_orders.nama_customer')
            ->orderBy('warehouse_outgoings.updated_at', 'DESC')
            ->get();

        return view('warehouse.approval-outgoing', compact('pending', 'approved'));
    }

    public function store(Request $request)
    {
        // 1. Validasi inputan wajib (tambahkan no_mm)
        $request->validate([
            'tanggal_pengiriman' => 'required|date',
            'no_po' => 'required',
            'no_mm' => 'required', // Pastikan sistem menuntut adanya no_mm
            'batch_number' => 'required',
            'qty_kirim' => 'required|numeric|min:1'
        ]);

        // 2. Simpan data ke tabel warehouse_outgoings
        \Illuminate\Support\Facades\DB::table('warehouse_outgoings')->insert([
            'tanggal_pengiriman' => $request->tanggal_pengiriman,
            'no_po' => $request->no_po,
            
            'no_mm' => $request->no_mm, // <--- INI SOLUSINYA! Baris ini kita aktifkan.
            
            'batch_number' => $request->batch_number,
            'qty_kirim' => $request->qty_kirim,
            'status' => 'Pending', // Status Pending menunggu Approve untuk memotong OSPO
            
            // --- DATA LOGISTIK OPSIONAL ---
            'no_shipment' => $request->no_shipment,
            'fwd_agent' => $request->fwd_agent,
            'no_polisi' => $request->no_polisi,
            'nama_supir' => $request->nama_supir,
            'keterangan' => $request->keterangan,
            
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('warehouse.outgoing.index')->with('success', 'Draft Pengiriman berhasil disimpan! Menunggu Approval.');
    }
    public function approveSuratJalan($id)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        
        try {
            $sj = \Illuminate\Support\Facades\DB::table('warehouse_outgoings')->where('id', $id)->first();

            if (!$sj || $sj->status == 'Approved') {
                return redirect()->route('warehouse.outgoing.approvalPage', ['error_msg' => 'Data tidak valid atau sudah pernah di-Approve!']);
            }

            $noMm = $sj->no_mm;
            
            if (!$noMm) {
                $sales = \Illuminate\Support\Facades\DB::table('sales_orders')->where('no_po', $sj->no_po)->first();
                $noMm = $sales->no_mm ?? null;
            }

            if (!$noMm) {
                return redirect()->route('warehouse.outgoing.approvalPage', ['error_msg' => 'Gagal! Nomor MM tidak ditemukan pada Surat Jalan ini.']);
            }

            // Cek OSPO
            $salesOrder = \Illuminate\Support\Facades\DB::table('sales_orders')->where('no_po', $sj->no_po)->first();
            if ($salesOrder && $salesOrder->ospo < $sj->qty_kirim) {
                return redirect()->route('warehouse.outgoing.approvalPage', ['error_msg' => 'Gagal! Qty pengiriman melebihi sisa OSPO saat ini.']);
            }

            // Cek ketersediaan Stok Gudang
            $stock = \Illuminate\Support\Facades\DB::table('warehouse_stocks')
                        ->where('no_mm', $noMm)
                        ->where('batch', $sj->batch_number)
                        ->first();

            // ========================================================
            // INI PESAN ERROR BATCH YANG AKAN MUNCUL SEBAGAI KOTAK MERAH
            // ========================================================
            if (!$stock) {
                $availableStocks = \Illuminate\Support\Facades\DB::table('warehouse_stocks')->where('no_mm', $noMm)->pluck('batch')->toArray();
                $batchTersedia = implode(', ', $availableStocks);
                
                return redirect()->route('warehouse.outgoing.approvalPage', [
                    'error_msg' => 'Gagal! No. Batch "' . $sj->batch_number . '" tidak terdaftar di gudang! Batch yang tersedia saat ini: ' . ($batchTersedia ?: 'Kosong')
                ]);
            }

            if ($stock->qty < $sj->qty_kirim) {
                return redirect()->route('warehouse.outgoing.approvalPage', ['error_msg' => 'Gagal! Stok Fisik tidak cukup. Stok tersedia: ' . $stock->qty . ', diminta kirim: ' . $sj->qty_kirim]);
            }

            // ==========================================
            // EKSEKUSI PEMOTONGAN JIKA SEMUA AMAN
            // ==========================================
            
            if ($salesOrder) {
                \Illuminate\Support\Facades\DB::table('sales_orders')
                    ->where('no_po', $sj->no_po)
                    ->decrement('ospo', $sj->qty_kirim);
            }

            \Illuminate\Support\Facades\DB::table('warehouse_stocks')
                ->where('no_mm', $noMm)
                ->where('batch', $sj->batch_number)
                ->decrement('qty', $sj->qty_kirim);

            \Illuminate\Support\Facades\DB::table('warehouse_outgoings')
                ->where('id', $id)
                ->update([
                    'status' => 'Approved',
                    'updated_at' => now(),
                    'approved_by' => auth()->user()->name ?? 'Admin'
                ]);

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('warehouse.outgoing.approvalPage')->with('success', 'Berhasil! Surat Jalan telah di-Approve, OSPO & Stok berkurang.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->route('warehouse.outgoing.approvalPage', ['error_msg' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }
    public function destroy($id)
    {
        try {
            $sj = \Illuminate\Support\Facades\DB::table('warehouse_outgoings')->where('id', $id)->first();

            // Cegah hapus jika data tidak ada
            if (!$sj) {
                return redirect()->route('warehouse.outgoing.approvalPage', ['error_msg' => 'Data tidak ditemukan!']);
            }

            // Cegah hapus jika sudah berstatus Approved (karena stok sudah terpotong)
            if ($sj->status == 'Approved') {
                return redirect()->route('warehouse.outgoing.approvalPage', ['error_msg' => 'Gagal! Surat Jalan yang sudah di-Approve tidak bisa dihapus.']);
            }

            // Eksekusi hapus data
            \Illuminate\Support\Facades\DB::table('warehouse_outgoings')->where('id', $id)->delete();

            return redirect()->route('warehouse.outgoing.approvalPage')->with('success', 'Draft Surat Jalan berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->route('warehouse.outgoing.approvalPage', ['error_msg' => 'Terjadi kesalahan sistem saat menghapus data: ' . $e->getMessage()]);
        }
    }
    public function print($id)
    {
        $sj = \Illuminate\Support\Facades\DB::table('warehouse_outgoings')
            ->leftJoin('sales_orders', 'warehouse_outgoings.no_po', '=', 'sales_orders.no_po')
            ->leftJoin('master_customers', 'sales_orders.nama_customer', '=', 'master_customers.nama_customer')
            ->select(
                'warehouse_outgoings.*', 
                'sales_orders.nama_produk', 
                'sales_orders.nama_customer',
                'master_customers.alamat'
            )
            ->where('warehouse_outgoings.id', $id)
            ->first();

        if (!$sj) {
            return back()->with('error_msg', 'Data Surat Jalan tidak ditemukan!');
        }

        return view('warehouse.print-sj', compact('sj'));
    }
}