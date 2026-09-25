<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
// HAPUS USE PDF DISINI - KITA PAKAI CARA LANGSUNG

class IncomingMaterialController extends Controller
{
    public function index(Request $request)
    {
        // =========================================================================
        // 1. TABEL 1: DATA INCOMING AKTIF (OPERASIONAL / PENDING / APPROVAL)
        // =========================================================================
        $queryAktif = DB::table('incoming_materials')
            ->leftJoin('users', 'incoming_materials.user_id', '=', 'users.id')
            ->select('incoming_materials.*', 'users.name as input_by')
            ->whereNull('incoming_materials.deleted_at');

        if ($request->filled('search')) {
            $queryAktif->where(function($q) use ($request) {
                $q->where('incoming_materials.mm', 'like', '%' . $request->search . '%')
                  ->orWhere('incoming_materials.stpb_number', 'like', '%' . $request->search . '%');
            });
        }

        $incomingList = $queryAktif->orderBy('incoming_materials.id', 'DESC')->get();

        // =========================================================================
        // 2. TABEL 2: REKAPAN & FILTER KEDATANGAN BARANG (BESERTA EKSPOR PDF)
        // =========================================================================
        $queryRiwayat = DB::table('incoming_materials')
            ->leftJoin('master_materials', 'incoming_materials.mm', '=', 'master_materials.no_mm')
            ->select('incoming_materials.*', 'master_materials.kategori')
            ->whereNull('incoming_materials.deleted_at');

        // Filter Dari Tanggal
        if ($request->filled('start_date')) {
            $queryRiwayat->whereDate('incoming_materials.updated_at', '>=', $request->start_date);
        }

        // Filter Sampai Tanggal
        if ($request->filled('end_date')) {
            $queryRiwayat->whereDate('incoming_materials.updated_at', '<=', $request->end_date);
        }

        // Filter Kategori Produk
        if ($request->filled('in_kategori')) {
            $queryRiwayat->where('master_materials.kategori', $request->in_kategori);
        }

        // Filter No PO
        if ($request->filled('in_po')) {
            $queryRiwayat->where('incoming_materials.po_kpdt_number', 'like', '%' . $request->in_po . '%');
        }

        // Filter Pencarian Text (MM / Nama Barang)
        if ($request->filled('in_search')) {
            $queryRiwayat->where(function($q) use ($request) {
                $q->where('incoming_materials.mm', 'like', '%' . $request->in_search . '%')
                  ->orWhere('incoming_materials.item_name', 'like', '%' . $request->in_search . '%');
            });
        }

        $riwayatKedatangan = $queryRiwayat->orderBy('incoming_materials.updated_at', 'DESC')->get();

        // --- FITUR CETAK PDF UNTUK BAGIAN 2 ---
        if ($request->input('export_pdf') == '1') {
            
            // <--- KITA GUNAKAN app('dompdf.wrapper') AGAR BEBAS DARI ERROR CLASS NOT FOUND
            $pdf = app('dompdf.wrapper')->loadView('warehouse.incoming-pdf', [
                'riwayatKedatangan' => $riwayatKedatangan,
                'request' => $request
            ]);
            
            // Kertas A4 Landscape agar 9 kolom tercetak rapi
            $pdf->setPaper('A4', 'landscape'); 
            
            return $pdf->stream('Laporan_Kedatangan_Barang_' . date('Ymd') . '.pdf');
        }

        // =========================================================================
        // 3. TABEL 3: DATA RIWAYAT YANG SUDAH DIHAPUS
        // =========================================================================
        $deletedIncoming = DB::table('incoming_materials')
            ->leftJoin('users', 'incoming_materials.user_id', '=', 'users.id')
            ->select('incoming_materials.*', 'users.name as input_by')
            ->whereNotNull('incoming_materials.deleted_at')
            ->orderBy('incoming_materials.deleted_at', 'DESC')
            ->get();

        $materials = DB::table('master_materials')->get();
        $vendors = DB::table('master_vendors')->get();
        $search = $request->search;

        return view('warehouse.incoming', compact(
            'incomingList', 
            'riwayatKedatangan', 
            'deletedIncoming', 
            'materials', 
            'vendors', 
            'search'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'mm' => 'required',
            'item_name' => 'required',
            'quantity' => 'required|numeric',
            'uom' => 'required',
            'stpb_number' => 'required',
            'po_kpdt_number' => 'required',
            'sj_supplier_number' => 'required',
            'vendor_code' => 'required',
            'vendor_name' => 'required',
            'address' => 'required',
            'remarks' => 'nullable',
        ]);

        // =========================================================================
        // [BARU] VALIDASI KECOCOKAN NO PO DENGAN NO MATERIAL (MM)
        // =========================================================================
        $cekPO = DB::table('purchase_orders')
            ->leftJoin('purchase_requests', 'purchase_orders.pr_id', '=', 'purchase_requests.id')
            ->whereRaw("CONCAT('PO-', DATE_FORMAT(purchase_orders.tanggal_po, '%Y%m%d'), '-', purchase_orders.id) = ?", [$request->po_kpdt_number])
            ->where(function($q) use ($request) {
                // Mengecek apakah MM ada di PO Direct atau di PR (Jika PO dari PR)
                $q->where('purchase_orders.no_mm', $request->mm)
                  ->orWhere('purchase_requests.no_mm', $request->mm);
            })
            ->first();

        // Jika data tidak ditemukan, tolak dan kembalikan dengan pesan error!
        if (!$cekPO) {
            return redirect()->back()
                ->withInput() // Mengembalikan isian form agar staf tidak perlu mengetik ulang semuanya
                ->with('error', 'GAGAL DISIMPAN! Nomor PO (' . $request->po_kpdt_number . ') tidak ditemukan atau tidak sesuai dengan Material (' . $request->mm . '). Silakan periksa kembali!');
        }
        // =========================================================================

        DB::table('incoming_materials')->insert([
            'date' => $request->date,
            'mm' => $request->mm,
            'item_name' => $request->item_name,
            'quantity' => $request->quantity,
            'uom' => $request->uom,
            'stpb_number' => $request->stpb_number,
            'po_kpdt_number' => $request->po_kpdt_number,
            'sj_supplier_number' => $request->sj_supplier_number,
            'vendor_code' => $request->vendor_code,
            'vendor_name' => $request->vendor_name,
            'address' => $request->address,
            'remarks' => $request->remarks,
            'user_id' => Auth::id(), 
            'status_qc' => 'Pending',
            'lokasi_stok' => 'Karantina',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->syncPurchaseOrderBalance($request->po_kpdt_number);

        return redirect()->back()->with('success', 'Data Incoming Material berhasil disimpan dengan status Pending. Menunggu proses Approval dari Quality QC!');
    }

    public function edit($id)
    {
        $incoming = DB::table('incoming_materials')->where('id', $id)->first();
        
        if ($incoming->status_qc !== 'Pending') {
            return redirect()->route('warehouse.incoming.index')->with('error', 'Data tidak bisa diedit karena sudah diproses oleh Quality!');
        }

        $materials = DB::table('master_materials')->get();
        $vendors = DB::table('master_vendors')->get();

        return view('warehouse.incoming-edit', compact('incoming', 'materials', 'vendors'));
    }

    public function update(Request $request, $id)
    {
        $incoming = DB::table('incoming_materials')->where('id', $id)->first();
        
        if ($incoming->status_qc !== 'Pending') {
            return redirect()->route('warehouse.incoming.index')->with('error', 'Data tidak bisa diupdate karena sudah diproses oleh Quality!');
        }

        $request->validate([
            'date' => 'required|date',
            'mm' => 'required',
            'item_name' => 'required',
            'quantity' => 'required|numeric',
            'uom' => 'required',
            'stpb_number' => 'required',
            'po_kpdt_number' => 'required',
            'sj_supplier_number' => 'required',
            'vendor_code' => 'required',
            'vendor_name' => 'required',
            'address' => 'required',
            'remarks' => 'nullable',
        ]);

        // =========================================================================
        // [BARU] VALIDASI KECOCOKAN NO PO DENGAN NO MATERIAL (MM) SAAT UPDATE
        // =========================================================================
        $cekPO = DB::table('purchase_orders')
            ->leftJoin('purchase_requests', 'purchase_orders.pr_id', '=', 'purchase_requests.id')
            ->whereRaw("CONCAT('PO-', DATE_FORMAT(purchase_orders.tanggal_po, '%Y%m%d'), '-', purchase_orders.id) = ?", [$request->po_kpdt_number])
            ->where(function($q) use ($request) {
                $q->where('purchase_orders.no_mm', $request->mm)
                  ->orWhere('purchase_requests.no_mm', $request->mm);
            })
            ->first();

        if (!$cekPO) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'GAGAL DIUPDATE! Nomor PO (' . $request->po_kpdt_number . ') tidak ditemukan atau tidak sesuai dengan Material (' . $request->mm . ').');
        }
        // =========================================================================

        $oldPoNumber = $incoming->po_kpdt_number;

        DB::table('incoming_materials')->where('id', $id)->update([
            'date' => $request->date,
            'mm' => $request->mm,
            'item_name' => $request->item_name,
            'quantity' => $request->quantity,
            'uom' => $request->uom,
            'stpb_number' => $request->stpb_number,
            'po_kpdt_number' => $request->po_kpdt_number,
            'sj_supplier_number' => $request->sj_supplier_number,
            'vendor_code' => $request->vendor_code,
            'vendor_name' => $request->vendor_name,
            'address' => $request->address,
            'remarks' => $request->remarks,
            'user_id' => Auth::id(), 
            'updated_at' => now(),
        ]);

        $this->syncPurchaseOrderBalance($request->po_kpdt_number);
        
        if ($oldPoNumber !== $request->po_kpdt_number) {
            $this->syncPurchaseOrderBalance($oldPoNumber);
        }

        return redirect()->route('warehouse.incoming.index')->with('success', 'Data Incoming Material berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $incoming = DB::table('incoming_materials')->where('id', $id)->first();
        
        if ($incoming && $incoming->status_qc !== 'Pending') {
            return redirect()->back()->with('error', 'Data tidak bisa dihapus karena sudah masuk ke stok Warehouse/Quality!');
        }

        $poNumber = $incoming->po_kpdt_number;

        DB::table('incoming_materials')->where('id', $id)->update([
            'deleted_at' => now(),
            'deleted_by' => Auth::user()->name ?? 'System'
        ]);

        $this->syncPurchaseOrderBalance($poNumber);

        return redirect()->back()->with('success', 'Data incoming berhasil dihapus dan dipindahkan ke riwayat!');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        if ($ids && count($ids) > 0) {
            $validItems = DB::table('incoming_materials')
                ->whereIn('id', $ids)
                ->where('status_qc', 'Pending')
                ->get();

            $validIds = $validItems->pluck('id')->toArray();
            $poNumbers = $validItems->pluck('po_kpdt_number')->unique();

            if (count($validIds) > 0) {
                DB::table('incoming_materials')->whereIn('id', $validIds)->update([
                    'deleted_at' => now(),
                    'deleted_by' => Auth::user()->name ?? 'System'
                ]);
                
                foreach ($poNumbers as $poNum) {
                    $this->syncPurchaseOrderBalance($poNum);
                }

                $skipped = count($ids) - count($validIds);
                $pesan = count($validIds) . ' data incoming berhasil dihapus.';
                if ($skipped > 0) {
                    $pesan .= ' (' . $skipped . ' data dilewati karena sudah diproses QC).';
                }

                return redirect()->back()->with('success', $pesan);
            } else {
                return redirect()->back()->with('error', 'Semua data yang dipilih sudah diproses QC, tidak bisa dihapus.');
            }
        }

        return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk dihapus.');
    }

    // =================================================================================
    // FUNGSI HELPER: TRIGGER OTOMATIS KE PURCHASING
    // =================================================================================
    private function syncPurchaseOrderBalance($poNumber)
    {
        if (empty($poNumber)) return;

        DB::statement("
            UPDATE purchase_orders po
            JOIN purchase_requests pr ON po.pr_id = pr.id
            LEFT JOIN (
                SELECT po_kpdt_number, mm, SUM(quantity) as total_aktual
                FROM incoming_materials 
                WHERE po_kpdt_number = :po_num1
                AND deleted_at IS NULL
                AND status_qc != 'NG'
                GROUP BY po_kpdt_number, mm
            ) gudang ON gudang.po_kpdt_number = CONCAT('PO-', DATE_FORMAT(po.tanggal_po, '%Y%m%d'), '-', po.id)
                     AND gudang.mm = pr.no_mm
            SET 
                po.qty_diterima = IFNULL(gudang.total_aktual, 0),
                po.status_barang = CASE 
                    WHEN IFNULL(gudang.total_aktual, 0) >= pr.qty THEN 'Sudah Diterima'
                    WHEN IFNULL(gudang.total_aktual, 0) > 0 THEN 'Parsial'
                    ELSE 'Belum Datang'
                END
            WHERE CONCAT('PO-', DATE_FORMAT(po.tanggal_po, '%Y%m%d'), '-', po.id) = :po_num2
        ", [
            'po_num1' => $poNumber, 
            'po_num2' => $poNumber
        ]);
    }
}