<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MutasiMaterial;
use App\Models\WorkOrder; // <--- TAMBAHAN BARU: Memanggil Model WorkOrder
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WarehouseController extends Controller
{
    public function dashboard()
    {
        // 1. Hitung Total Item Material di Gudang
        $totalItem = DB::table('warehouse_stocks')->count();

        // 2. Hitung Mutasi yang Menunggu Approval (Tugas aktif)
        $pendingApproval = MutasiMaterial::whereIn('ke_dept', ['Warehouse', 'WH', 'Supplier'])
                            ->where('status_approval', 'Pending')
                            ->count();

        // 3. Hitung Total Kedatangan (Incoming) Hari Ini
        $incomingHariIni = DB::table('incoming_materials')
                            ->whereDate('created_at', Carbon::today())
                            ->count();

        // 4. Ambil 5 Riwayat Mutasi Terbaru (Keluar & Masuk)
        $mutasiTerbaru = MutasiMaterial::whereIn('ke_dept', ['Warehouse', 'WH', 'Supplier'])
                            ->orWhereIn('dari_dept', ['Warehouse', 'WH'])
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();

        // 5. FITUR BARU: Ambil 5 Barang dengan Stok Paling Rendah (Stok Kritis)
        $stokKritis = DB::table('warehouse_stocks')
                            ->select('no_mm', DB::raw('SUM(qty) as total_qty'))
                            ->groupBy('no_mm')
                            ->orderBy('total_qty', 'asc')
                            ->limit(5)
                            ->get();

        return view('warehouse.dashboard', compact('totalItem', 'pendingApproval', 'incomingHariIni', 'mutasiTerbaru', 'stokKritis'));
    }

    // ==========================================================
    // FUNGSI BARU: HALAMAN INCOMING MATERIAL (LENGKAP)
    // ==========================================================
    public function incomingIndex(Request $request)
    {
        // 1. Data Dropdown Form
        $materials = DB::table('master_materials')->get();
        $vendors = DB::table('master_vendors')->get();

        // 2. Data Tabel Incoming Aktif
        $queryAktif = DB::table('incoming_materials')->whereNull('deleted_at');
        if ($request->filled('search')) {
            $queryAktif->where(function($q) use ($request) {
                $q->where('mm', 'like', '%' . $request->search . '%')
                  ->orWhere('stpb_number', 'like', '%' . $request->search . '%')
                  ->orWhere('item_name', 'like', '%' . $request->search . '%');
            });
        }
        $incomingList = $queryAktif->orderBy('id', 'DESC')->get();

        // 3. Data Tabel Riwayat Hapus
        $deletedIncoming = DB::table('incoming_materials')
                            ->whereNotNull('deleted_at')
                            ->orderBy('deleted_at', 'DESC')
                            ->get();

        // 4. Data Filter Riwayat Kedatangan (Yang baru dipindah dari Rekap)
        $queryRiwayat = DB::table('incoming_materials')
            ->leftJoin('master_materials', 'incoming_materials.mm', '=', 'master_materials.no_mm')
            ->select('incoming_materials.*', 'master_materials.kategori')
            ->whereNull('incoming_materials.deleted_at');

        // Terapkan Filter Kedatangan
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $queryRiwayat->whereBetween(DB::raw('DATE(incoming_materials.created_at)'), [$request->start_date, $request->end_date]);
        }
        if ($request->filled('in_kategori')) {
            $queryRiwayat->where('master_materials.kategori', 'like', '%' . $request->in_kategori . '%');
        }
        if ($request->filled('in_search')) {
            $queryRiwayat->where(function($q) use ($request) {
                $q->where('incoming_materials.mm', 'like', '%' . $request->in_search . '%')
                  ->orWhere('incoming_materials.item_name', 'like', '%' . $request->in_search . '%');
            });
        }
        if ($request->filled('in_po')) {
            $queryRiwayat->where('incoming_materials.po_kpdt_number', 'like', '%' . $request->in_po . '%');
        }
        $riwayatKedatangan = $queryRiwayat->orderBy('incoming_materials.created_at', 'DESC')->get();

        return view('warehouse.incoming', compact('materials', 'vendors', 'incomingList', 'deletedIncoming', 'riwayatKedatangan', 'request'));
    }

    // ==========================================================
    // FUNGSI UNTUK MENU STOK (REKAPAN ALL MATERIAL)
    // ==========================================================
    public function rekapStok(Request $request)
    {
        $query = DB::table('incoming_materials')
            ->select(
                'mm', 
                'item_name', 
                'lokasi_stok',
                DB::raw('SUM(quantity) as total_qty')
            )
            ->whereNull('deleted_at');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('mm', 'like', '%' . $request->search . '%')
                  ->orWhere('item_name', 'like', '%' . $request->search . '%');
            });
        }

        $rekap_stok = $query->groupBy('mm', 'item_name', 'lokasi_stok')
                            ->orderBy('mm', 'ASC')
                            ->get();

        return view('warehouse.stok.rekap', compact('rekap_stok', 'request'));
    }

    // ==========================================================
    // 1. STOK RAW MATERIAL
    // ==========================================================
    public function rawMaterial(Request $request)
    {
        // A. Stok Fisik
        $query = DB::table('incoming_materials')
            ->join('master_materials', 'incoming_materials.mm', '=', 'master_materials.no_mm')
            ->select('incoming_materials.*', 'master_materials.kategori', 'master_materials.nama_material as master_nama')
            ->where('incoming_materials.status_qc', 'Pass')
            ->where('incoming_materials.lokasi_stok', 'Warehouse')
            ->where('master_materials.kategori', 'LIKE', '%Raw Material%') 
            ->whereNull('incoming_materials.deleted_at');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('incoming_materials.mm', 'like', '%' . $request->search . '%')
                  ->orWhere('incoming_materials.item_name', 'like', '%' . $request->search . '%');
            });
        }

        $materials = $query->orderBy('incoming_materials.id', 'DESC')->get();
        $search = $request->search;

        // B. Riwayat Mutasi
        $mutasiQuery = DB::table('mutasi_materials')
            ->join('master_materials', 'mutasi_materials.mm', '=', 'master_materials.no_mm')
            ->leftJoin('users as pembuat', 'mutasi_materials.pic_id', '=', 'pembuat.id')
            ->leftJoin('users as penyetuju', 'mutasi_materials.approved_by', '=', 'penyetuju.id')
            ->select('mutasi_materials.*', 'pembuat.name as nama_pembuat', 'penyetuju.name as nama_penyetuju', 'master_materials.kategori')
            ->where('master_materials.kategori', 'LIKE', '%Raw Material%') 
            ->whereNull('mutasi_materials.deleted_at');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $mutasiQuery->whereBetween('mutasi_materials.tanggal', [$request->start_date, $request->end_date]);
        }
        
        if ($request->filled('search')) {
            $mutasiQuery->where(function($q) use ($request) {
                $q->where('mutasi_materials.mm', 'like', '%' . $request->search . '%')
                  ->orWhere('mutasi_materials.item_name', 'like', '%' . $request->search . '%')
                  ->orWhere('mutasi_materials.batch', 'like', '%' . $request->search . '%');
            });
        }

        $riwayat_mutasi = $mutasiQuery->orderBy('mutasi_materials.id', 'DESC')->get();

        return view('warehouse.stok.raw-material', compact('materials', 'search', 'riwayat_mutasi'));
    }

    // ==========================================================
    // 2. STOK FINISH GOODS
    // ==========================================================
    public function finishGoods(Request $request)
    {
        // A. Stok Fisik
        $query = DB::table('incoming_materials')
            ->join('master_materials', 'incoming_materials.mm', '=', 'master_materials.no_mm')
            ->select('incoming_materials.*', 'master_materials.kategori', 'master_materials.nama_material as master_nama')
            ->where('incoming_materials.status_qc', 'Pass')
            ->where('incoming_materials.lokasi_stok', 'Warehouse')
            ->where('master_materials.kategori', 'LIKE', '%Finish Good%') 
            ->whereNull('incoming_materials.deleted_at');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('incoming_materials.mm', 'like', '%' . $request->search . '%')
                  ->orWhere('incoming_materials.item_name', 'like', '%' . $request->search . '%');
            });
        }

        $materials = $query->orderBy('incoming_materials.id', 'DESC')->get();
        $search = $request->search;

        // B. Riwayat Mutasi
        $mutasiQuery = DB::table('mutasi_materials')
            ->join('master_materials', 'mutasi_materials.mm', '=', 'master_materials.no_mm')
            ->leftJoin('users as pembuat', 'mutasi_materials.pic_id', '=', 'pembuat.id')
            ->leftJoin('users as penyetuju', 'mutasi_materials.approved_by', '=', 'penyetuju.id')
            ->select('mutasi_materials.*', 'pembuat.name as nama_pembuat', 'penyetuju.name as nama_penyetuju', 'master_materials.kategori')
            ->where('master_materials.kategori', 'LIKE', '%Finish Good%') 
            ->whereNull('mutasi_materials.deleted_at');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $mutasiQuery->whereBetween('mutasi_materials.tanggal', [$request->start_date, $request->end_date]);
        }
        
        if ($request->filled('search')) {
            $mutasiQuery->where(function($q) use ($request) {
                $q->where('mutasi_materials.mm', 'like', '%' . $request->search . '%')
                  ->orWhere('mutasi_materials.item_name', 'like', '%' . $request->search . '%')
                  ->orWhere('mutasi_materials.batch', 'like', '%' . $request->search . '%');
            });
        }

        $riwayat_mutasi = $mutasiQuery->orderBy('mutasi_materials.id', 'DESC')->get();

        return view('warehouse.stok.finish-goods', compact('materials', 'search', 'riwayat_mutasi'));
    }

    // ==========================================================
    // 3. STOK KARANTINA
    // ==========================================================
    public function karantina(Request $request)
    {
        $query = DB::table('incoming_materials')
            ->whereIn('lokasi_stok', ['Karantina', 'Warehouse']) 
            ->where('status_qc', '!=', 'Pass') 
            ->whereNull('deleted_at');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('mm', 'like', '%' . $request->search . '%')
                  ->orWhere('item_name', 'like', '%' . $request->search . '%');
            });
        }

        $materials = $query->orderBy('id', 'DESC')->get();
        $search = $request->search;

        return view('warehouse.stok.karantina', compact('materials', 'search'));
    }

    public function store(Request $request)
    {
        // Logic store lama
    }

    public function storeMutasi(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'shift' => 'required',
            'ke_dept' => 'required',
            'mm' => 'required',
            'qty' => 'required|numeric|min:0.1'
        ]);

        \App\Models\MutasiMaterial::create([
            'tanggal' => $request->tanggal,
            'shift' => $request->shift,
            'dari_dept' => 'Warehouse',
            'ke_dept' => $request->ke_dept,
            'mm' => $request->mm,
            'item_name' => $request->item_name,
            'batch' => $request->batch,
            'qty' => $request->qty,
            'uom' => $request->uom ?? 'Pcs',
            'pic_id' => \Illuminate\Support\Facades\Auth::id(),
            'status_approval' => 'Pending',
            'catatan' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Berhasil! Mutasi material telah dibuat dan sedang menunggu approval dari departemen ' . $request->ke_dept);
    }

    public function updateMutasi(Request $request, $id)
    {
        $mutasi = MutasiMaterial::findOrFail($id);

        if ($mutasi->status_approval != 'Pending') {
            return redirect()->back()->with('error', 'Gagal! Mutasi yang sudah disetujui tidak dapat diedit.');
        }

        $request->validate([
            'tanggal' => 'required|date',
            'shift' => 'required',
            'ke_dept' => 'required',
            'mm' => 'required',
            'qty' => 'required|numeric|min:0.1'
        ]);

        $mutasi->update([
            'tanggal' => $request->tanggal,
            'shift' => $request->shift,
            'ke_dept' => $request->ke_dept,
            'mm' => $request->mm,
            'item_name' => $request->item_name,
            'batch' => $request->batch,
            'qty' => $request->qty,
            'uom' => $request->uom ?? 'Pcs',
            'catatan' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Data mutasi berhasil diperbarui!');
    }

    public function approveMutasi($id)
    {
        $mutasi = MutasiMaterial::findOrFail($id);
        
        $mutasi->update([
            'status_approval' => 'Approved',
            'approved_by' => Auth::id(),
            'approved_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Mutasi berhasil disetujui! Status stok telah diperbarui.');
    }

    public function getItemByMm(Request $request)
    {
        $item = DB::table('incoming_materials')
            ->where('mm', $request->mm)
            ->whereNotNull('item_name')
            ->first();

        if ($item) {
            return response()->json(['success' => true, 'item_name' => $item->item_name]);
        }

        return response()->json(['success' => false]);
    }

    // --- FUNGSI UNTUK CETAK MASAL (BATCH PRINT) RAW MATERIAL ---
    public function printRawMaterial(Request $request)
    {
        $mutasiQuery = DB::table('mutasi_materials')
            ->join('materials', 'mutasi_materials.mm', '=', 'materials.kode_material')
            ->leftJoin('users as pembuat', 'mutasi_materials.pic_id', '=', 'pembuat.id')
            ->leftJoin('users as penyetuju', 'mutasi_materials.approved_by', '=', 'penyetuju.id')
            ->select('mutasi_materials.*', 'pembuat.name as nama_pembuat', 'penyetuju.name as nama_penyetuju', 'materials.kategori')
            ->where('materials.kategori', 'Raw Material') 
            ->whereNull('mutasi_materials.deleted_at')
            ->where('mutasi_materials.status_approval', 'Approved');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $mutasiQuery->whereBetween('mutasi_materials.tanggal', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('search')) {
            $mutasiQuery->where(function($q) use ($request) {
                $q->where('mutasi_materials.mm', 'like', '%' . $request->search . '%')
                  ->orWhere('mutasi_materials.item_name', 'like', '%' . $request->search . '%')
                  ->orWhere('mutasi_materials.batch', 'like', '%' . $request->search . '%');
            });
        }

        $data_print = $mutasiQuery->orderBy('mutasi_materials.tanggal', 'ASC')->get();

        return view('warehouse.stok.print-raw-material', compact('data_print', 'request'));
    }

    // ==========================================================
    // FUNGSI UNTUK HALAMAN APPROVAL MUTASI MASUK (DI WAREHOUSE)
    // ==========================================================
    public function approvalMutasiIndex(Request $request)
    {
        $query = MutasiMaterial::with(['pembuat', 'penyetuju'])
                    ->where('ke_dept', 'Warehouse'); 

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('mm', 'like', '%' . $request->search . '%')
                  ->orWhere('item_name', 'like', '%' . $request->search . '%')
                  ->orWhere('batch', 'like', '%' . $request->search . '%');
            });
        }

        $riwayat_mutasi = $query->orderBy('id', 'DESC')->get();
        return view('warehouse.stok.approval-mutasi', compact('riwayat_mutasi'));
    }

    public function printApprovalMutasi(Request $request)
    {
        $query = MutasiMaterial::with(['pembuat', 'penyetuju'])
                    ->where('ke_dept', 'Warehouse')
                    ->where('status_approval', 'Approved'); 

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('mm', 'like', '%' . $request->search . '%')
                  ->orWhere('item_name', 'like', '%' . $request->search . '%')
                  ->orWhere('batch', 'like', '%' . $request->search . '%');
            });
        }

        $data_print = $query->orderBy('tanggal', 'ASC')->get();
        return view('warehouse.stok.print-approval', compact('data_print', 'request'));
    }

    // ==========================================================
    // FUNGSI BARU: MONITOR & EKSEKUSI PERMINTAAN MATERIAL PPIC
    // ==========================================================
    
    // 1. Menampilkan Daftar Antrean Permintaan Material dari PPIC
    public function indexMaterial()
    {
        // Hanya ambil WO yang statusnya "Menunggu Material"
        $workOrders = WorkOrder::with('salesOrder')
            ->where('status', 'Menunggu Material')
            ->orderBy('updated_at', 'asc') // Yang minta paling duluan ada di atas (FIFO)
            ->get();

        return view('warehouse.material-request', compact('workOrders'));
    }

    // 2. Mengeksekusi Pengeluaran Barang (Issue Material)
    public function processMaterial($id)
    {
        $wo = WorkOrder::findOrFail($id);
        
        // --- DI SINI NANTI KITA BISA TAMBAHKAN KODE POTONG STOK OTOMATIS ---
        // (Untuk sementara, kita fokus memindahkan status WO-nya dulu ke Produksi)

        // Ubah status WO menjadi 'On Progress' (Sedang Diproses Produksi)
        $wo->status = 'On Progress';
        $wo->save();

        return redirect()->back()->with('success', 'Luar Biasa! Material untuk WO-' . $wo->no_wo . ' telah dikeluarkan dan diteruskan ke Produksi.');
    }
}