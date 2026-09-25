<?php

namespace App\Http\Controllers\Purchasing;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use Illuminate\Http\Request;

class PurchasingController extends Controller
{
    // ====================================================================
    // 1. DASHBOARD UTAMA PURCHASING
    // ====================================================================
    public function dashboard()
    {
        // 1. Hitung PR Pending (Gunakan patokan status PR agar sinkron dengan sidebar)
        $prPending = DB::table('purchase_requests')
            ->whereIn('status', ['Pending', 'Diproses']) // Hitung semua yang belum 'Selesai'
            ->count();

        // 2. Hitung PO Terbit Bulan Ini
        $poBulanIni = DB::table('purchase_orders')
            ->whereMonth('tanggal_po', date('m'))
            ->whereYear('tanggal_po', date('Y'))
            ->count();

        // 3. Hitung Outstanding PO (Barang belum datang full)
        $poOutstanding = DB::table('purchase_orders')
            ->where('status_barang', '!=', 'Sudah Diterima')
            ->count();

        // 4. Ambil 5 PO Terbaru untuk tabel di bawah (Ditambah Join ke Material)
        $poTerbaru = DB::table('purchase_orders')
            ->join('master_vendors', 'purchase_orders.supplier_id', '=', 'master_vendors.id')
            ->leftJoin('purchase_requests', 'purchase_orders.pr_id', '=', 'purchase_requests.id')
            ->leftJoin('master_materials', function($join) {
                $join->on('purchase_orders.no_mm', '=', 'master_materials.no_mm')
                     ->orOn('purchase_requests.no_mm', '=', 'master_materials.no_mm');
            })
            ->select(
                'purchase_orders.*', 
                'master_vendors.vendor_name',
                DB::raw('IFNULL(purchase_orders.no_mm, purchase_requests.no_mm) as no_mm'),
                'master_materials.nama_material'
            )
            ->orderBy('purchase_orders.created_at', 'desc')
            ->limit(5)
            ->get();

        return view('purchasing.dashboard-purchasing', compact(
            'prPending', 
            'poBulanIni', 
            'poOutstanding', 
            'poTerbaru'
        ));
    }

    public function index()
    {
        $waitingList = WorkOrder::where('status', 'Waiting Material')->get();
        return view('purchasing.dashboard-purchasing', compact('waitingList'));
    }

    public function markAsPurchased($id)
    {
        $wo = WorkOrder::findOrFail($id);
        $wo->update(['status' => 'In Production']); 
        
        return redirect()->route('purchasing.index')->with('success', 'Material untuk ' . $wo->no_wo . ' sudah dipesan!');
    }

    // ====================================================================
    // 2. DAFTAR PR MASUK
    // ====================================================================
    // ====================================================================
    // 2. DAFTAR PR MASUK (DENGAN FILTER LENGKAP)
    // ====================================================================
    public function prIndex(Request $request)
    {
        // 1. Tangkap parameter filter dari URL
        $deptFilter = $request->query('dept'); 
        $search = $request->query('search');
        $statusFilter = $request->query('status');

        // 2. Mulai susun query dasar
        $query = \App\Models\PurchaseRequest::leftJoin('master_materials', 'purchase_requests.no_mm', '=', 'master_materials.no_mm')
            ->select('purchase_requests.*', 'master_materials.nama_material', 'master_materials.satuan');

        // 3. LOGIKA FILTER DEPARTEMEN
        if ($deptFilter) {
            $query->where('purchase_requests.departemen', 'like', '%' . $deptFilter . '%');
        }

        // 4. LOGIKA FILTER STATUS (Pending / Diproses / Selesai)
        if ($statusFilter) {
            $query->where('purchase_requests.status', $statusFilter);
        }

        // 5. LOGIKA FILTER PENCARIAN KETIK (No PR, No MM, Nama Material)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('purchase_requests.no_pr', 'like', '%' . $search . '%')
                  ->orWhere('purchase_requests.no_mm', 'like', '%' . $search . '%')
                  ->orWhere('master_materials.nama_material', 'like', '%' . $search . '%');
            });
        }

        // 6. Ambil datanya (urutkan yang terbaru di atas)
        $prs = $query->orderBy('purchase_requests.created_at', 'desc')->get();

        // 7. Kirim data ke tampilan
        return view('purchasing.pr-list', compact('prs', 'deptFilter', 'search', 'statusFilter'));
    }

    // ====================================================================
    // 3. PROSES PR 
    // ====================================================================
    public function processPr($id, Request $request)
    {
        try {
            DB::beginTransaction();
            $pr = DB::table('purchase_requests')->where('id', $id)->lockForUpdate()->first();

            if (!$pr) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Akses Ditolak: Dokumen PR tidak ditemukan.');
            }

            if ($pr->status !== 'Pending') {
                DB::rollBack();
                return redirect()->back()->with('error', 'Dokumen PR ini sudah diproses sebelumnya oleh staf lain.');
            }

            DB::table('purchase_requests')->where('id', $id)->update([
                'status' => 'Diproses',
                'updated_at' => now()
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Berhasil! Dokumen PR dikunci dan statusnya berubah menjadi "Diproses".');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem. (Error: ' . $e->getMessage() . ')');
        }
    }

    // ====================================================================
    // 4. MEMBUKA FORM PO DARI PR LAMA
    // ====================================================================
    public function createPo($id)
    {
        $pr = DB::table('purchase_requests')
            ->leftJoin('master_materials', 'purchase_requests.no_mm', '=', 'master_materials.no_mm')
            ->leftJoin('users', 'purchase_requests.pemohon_id', '=', 'users.id')
            ->select(
                'purchase_requests.*', 
                'master_materials.nama_material', 
                'master_materials.satuan',
                'users.name as nama_pemohon'
            )
            ->where('purchase_requests.id', $id)
            ->first();

        if (!$pr) abort(404, 'Data PR tidak ditemukan');

        $vendors = DB::table('master_vendors')->orderBy('vendor_name', 'asc')->get();
        return view('purchasing.create_po', compact('pr', 'vendors'));
    }

    // ====================================================================
    // 5. SIMPAN PO DARI PR LAMA
    // ====================================================================
    public function storePo(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // ---- [BARU] LOGIKA KALKULASI TOTAL NILAI PO ----
            $qty = $request->qty_pesan ?? 0;
            $harga = $request->harga_satuan ?? 0;
            $subtotal = $qty * $harga;
            
            $diskon = $request->diskon ?? 0;
            $nominal_diskon = $subtotal * ($diskon / 100);
            $dpp = $subtotal - $nominal_diskon;
            
            $ppn = $request->ppn ?? 0;
            $nominal_ppn = $dpp * ($ppn / 100);
            
            $pph = $request->pph ?? 0;
            $nominal_pph = $dpp * ($pph / 100);
            
            $additional = $request->additional_charge ?? 0;
            
            $grand_total = $dpp + $nominal_ppn - $nominal_pph + $additional;
            // -------------------------------------------------

            // Tentukan apakah butuh ACC Presdir (> 50 Juta)
            $status_presdir = ($grand_total >= 50000000) ? 'Menunggu' : 'Tidak Perlu';

            DB::table('purchase_orders')->insert([
                'pr_id' => $id,
                'supplier_id' => $request->supplier_id,
                'tanggal_po' => $request->tanggal_po,
                'harga_satuan' => $harga,
                'eta' => $request->eta,
                'keterangan' => $request->keterangan,
                'qty_pesanan' => $qty,
                'diskon' => $diskon,
                'ppn' => $ppn,
                'pph' => $pph,
                'additional_charge' => $additional,
                
                // Menyimpan status approval awal & total nilai
                'total_nilai' => $grand_total,
                'approval_manager_plan' => 'Menunggu',
                'approval_direktur' => 'Menunggu',
                'approval_presdir' => $status_presdir,

                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('purchase_requests')->where('id', $id)->update([
                'status' => 'Selesai',
                'updated_at' => now()
            ]);

            DB::commit();
            return redirect()->route('purchasing.pr.index')->with('success', 'PO telah diterbitkan. ' . ($status_presdir == 'Menunggu' ? '(Membutuhkan Approval Presdir)' : '(Tanpa Approval Presdir)'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan PO: ' . $e->getMessage());
        }
    }

    // ====================================================================
    // 6. BUKA FORM PO DIRECT (DARI MRP)
    // ====================================================================
    public function create(Request $request)
    {
        $vendors = DB::table('master_vendors')->get();
        return view('purchasing.create_po', compact('vendors'));
    }

    // ====================================================================
    // 7. SIMPAN PO DIRECT (DARI MRP)
    // ====================================================================
    public function store(Request $request)
    {
        // ---- [BARU] LOGIKA KALKULASI TOTAL NILAI PO ----
        $qty = $request->qty_pesan ?? 0;
        $harga = $request->harga_satuan ?? 0;
        $subtotal = $qty * $harga;
        
        $diskon = $request->diskon ?? 0;
        $nominal_diskon = $subtotal * ($diskon / 100);
        $dpp = $subtotal - $nominal_diskon;
        
        $ppn = $request->ppn ?? 0;
        $nominal_ppn = $dpp * ($ppn / 100);
        
        $pph = $request->pph ?? 0;
        $nominal_pph = $dpp * ($pph / 100);
        
        $additional = $request->additional_charge ?? 0;
        
        $grand_total = $dpp + $nominal_ppn - $nominal_pph + $additional;
        // -------------------------------------------------

        // Tentukan apakah butuh ACC Presdir (> 50 Juta)
        $status_presdir = ($grand_total >= 50000000) ? 'Menunggu' : 'Tidak Perlu';

        DB::table('purchase_orders')->insert([
            'no_mm'         => $request->no_mm,
            'supplier_id'   => $request->supplier_id,
            'qty_pesanan'   => $qty,
            'tanggal_po'    => $request->tanggal_po ?? now()->toDateString(), 
            'harga_satuan'  => $harga,
            'eta'           => $request->eta,
            'keterangan'    => $request->keterangan,
            'diskon'        => $diskon,
            'ppn'           => $ppn,
            'pph'           => $pph,
            'additional_charge' => $additional,
            
            // Menyimpan status approval awal & total nilai
            'total_nilai' => $grand_total,
            'approval_manager_plan' => 'Menunggu',
            'approval_direktur' => 'Menunggu',
            'approval_presdir' => $status_presdir,

            'status_barang' => 'Outstanding', 
            'qty_diterima'  => 0, 
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return redirect('/purchasing/outstanding-po')->with('success', 'Purchase Order Direct berhasil diterbitkan!');
    }

    // ====================================================================
    // 8. RIWAYAT PO
    // ====================================================================
    public function riwayatPo(Request $request)
    {
        $query = DB::table('purchase_orders')
            ->leftJoin('purchase_requests', 'purchase_orders.pr_id', '=', 'purchase_requests.id')
            ->join('master_vendors', 'purchase_orders.supplier_id', '=', 'master_vendors.id')
            ->leftJoin('master_materials', function($join) {
                $join->on('purchase_orders.no_mm', '=', 'master_materials.no_mm')
                     ->orOn('purchase_requests.no_mm', '=', 'master_materials.no_mm');
            })
            ->select(
                'purchase_orders.*',
                'purchase_requests.no_pr',
                DB::raw('IFNULL(purchase_orders.no_mm, purchase_requests.no_mm) as no_mm'),
                DB::raw('IFNULL(purchase_orders.qty_pesanan, purchase_requests.qty) as qty'),
                'master_materials.satuan',
                'master_vendors.vendor_name',
                'master_materials.nama_material'
            );

        if ($request->has('tanggal_po') && $request->tanggal_po != '') {
            $query->whereDate('purchase_orders.tanggal_po', $request->tanggal_po);
        }

        if ($request->has('vendor_name') && $request->vendor_name != '') {
            $query->where('master_vendors.vendor_name', 'like', '%' . $request->vendor_name . '%');
        }

        if ($request->has('no_mm') && $request->no_mm != '') {
            $query->where(function($q) use ($request) {
                $q->where('purchase_orders.no_mm', 'like', '%' . $request->no_mm . '%')
                  ->orWhere('purchase_requests.no_mm', 'like', '%' . $request->no_mm . '%');
            });
        }

        $pos = $query->orderBy('purchase_orders.created_at', 'desc')->get();
        return view('purchasing.riwayat_po', compact('pos'));
    }

    // ====================================================================
    // 9. PRINT PO 
    // ====================================================================
    public function printPo($id)
    {
        $po = DB::table('purchase_orders')
            ->leftJoin('purchase_requests', 'purchase_orders.pr_id', '=', 'purchase_requests.id')
            ->join('master_vendors', 'purchase_orders.supplier_id', '=', 'master_vendors.id')
            ->leftJoin('master_materials', function($join) {
                $join->on('purchase_orders.no_mm', '=', 'master_materials.no_mm')
                     ->orOn('purchase_requests.no_mm', '=', 'master_materials.no_mm');
            })
            ->select(
                'purchase_orders.*',
                'purchase_requests.no_pr',
                DB::raw('IFNULL(purchase_orders.no_mm, purchase_requests.no_mm) as no_mm'),
                DB::raw('IFNULL(purchase_orders.qty_pesanan, purchase_requests.qty) as qty'),
                'master_materials.satuan',
                'master_vendors.vendor_name',
                'master_vendors.address',
                'master_vendors.no_telp',
                'master_materials.nama_material'
            )
            ->where('purchase_orders.id', $id)
            ->first();

        if (!$po) abort(404, 'Data PO tidak ditemukan');
        return view('purchasing.print_po', compact('po'));
    }

    // ====================================================================
    // 10. OUTSTANDING PO
    // ====================================================================
    public function outstandingPo()
    {
        $pos = DB::table('purchase_orders')
            ->leftJoin('purchase_requests', 'purchase_orders.pr_id', '=', 'purchase_requests.id')
            ->join('master_vendors', 'purchase_orders.supplier_id', '=', 'master_vendors.id')
            ->leftJoin('master_materials', function($join) {
                $join->on('purchase_orders.no_mm', '=', 'master_materials.no_mm')
                     ->orOn('purchase_requests.no_mm', '=', 'master_materials.no_mm');
            })
            ->select(
                'purchase_orders.*',
                'purchase_requests.no_pr',
                DB::raw('IFNULL(purchase_orders.no_mm, purchase_requests.no_mm) as no_mm'),
                DB::raw('IFNULL(purchase_orders.qty_pesanan, purchase_requests.qty) as qty'),
                'master_materials.satuan',
                'master_vendors.vendor_name',
                'master_materials.nama_material'
            )
            // =======================================================
            // [BARU] FILTER HANYA TAMPILKAN PO YANG SUDAH FULL-APPROVED
            // =======================================================
            ->where('purchase_orders.approval_manager_plan', 'Approved')
            ->where('purchase_orders.approval_direktur', 'Approved')
            ->whereIn('purchase_orders.approval_presdir', ['Approved', 'Tidak Perlu'])
            // =======================================================
            
            // KITA MATIKAN FILTER INI AGAR YANG BALANCE 0 TETAP MUNCUL DI SINI
            // ->where('purchase_orders.status_barang', '!=', 'Sudah Diterima')
            ->orderBy('purchase_orders.eta', 'asc') 
            ->get();

        return view('purchasing.outstanding_po', compact('pos'));
    }
    
    // ====================================================================
    // 11. APPROVE PR
    // ====================================================================
    public function approvePr($id)
    {
        DB::table('purchase_requests')->where('id', $id)->update([
            'status_approval' => 'Approved',
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Dokumen PR berhasil disetujui (Approved)! Tim Purchasing sekarang bisa memprosesnya menjadi PO.');
    }
    // ====================================================================
    // 12. HALAMAN DAFTAR APPROVAL PO (Untuk Atasan)
    // ====================================================================
    public function approvalPoIndex()
    {
        $role = auth()->user()->role;
        $query = DB::table('purchase_orders')
            ->join('master_vendors', 'purchase_orders.supplier_id', '=', 'master_vendors.id')
            ->select('purchase_orders.*', 'master_vendors.vendor_name');

        // Logika Antrean (Siapa melihat apa)
        if ($role == 'manager_plan') {
            // Manager melihat semua PO yang belum dia ACC
            $query->where('approval_manager_plan', 'Menunggu');
        } elseif ($role == 'direktur') {
            // Direktur HANYA melihat PO yang SUDAH di-ACC Manager
            $query->where('approval_manager_plan', 'Approved')
                  ->where('approval_direktur', 'Menunggu');
        } elseif ($role == 'presiden_direktur') {
            // Presdir HANYA melihat PO yang SUDAH di-ACC Direktur & butuh ACC-nya
            $query->where('approval_direktur', 'Approved')
                  ->where('approval_presdir', 'Menunggu');
        } else {
            // Jika Super Admin yang buka, tampilkan semua yang masih nyangkut di atasan
            $query->where(function($q) {
                $q->where('approval_manager_plan', 'Menunggu')
                  ->orWhere('approval_direktur', 'Menunggu')
                  ->orWhere('approval_presdir', 'Menunggu');
            });
        }

        $pos = $query->orderBy('created_at', 'desc')->get();
        return view('purchasing.approval_po', compact('pos', 'role'));
    }

    // ====================================================================
    // 13. EKSEKUSI TOMBOL APPROVE PO
    // ====================================================================
    public function approvePo($id, Request $request)
    {
        $role = auth()->user()->role;
        $po = DB::table('purchase_orders')->where('id', $id)->first();

        if (!$po) return redirect()->back()->with('error', 'Data PO tidak ditemukan');

        // Update status sesuai jabatan yang menekan tombol
        if ($role == 'manager_plan') {
            DB::table('purchase_orders')->where('id', $id)->update(['approval_manager_plan' => 'Approved']);
        } elseif ($role == 'direktur') {
            DB::table('purchase_orders')->where('id', $id)->update(['approval_direktur' => 'Approved']);
        } elseif ($role == 'presiden_direktur') {
            DB::table('purchase_orders')->where('id', $id)->update(['approval_presdir' => 'Approved']);
        }

        return redirect()->back()->with('success', 'Berhasil! Purchase Order telah Anda setujui.');
    }
    
}