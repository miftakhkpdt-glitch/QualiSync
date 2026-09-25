<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MutasiMaterial;
use App\Models\Material; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon;

class MutasiStokController extends Controller
{
    // ==========================================================
    // FUNGSI UNTUK HALAMAN APPROVAL (MENERIMA BARANG/RETUR)
    // ==========================================================
    public function index(Request $request)
    {
        // REVISI: Tambahkan 'Supplier' agar mutasi retur muncul di halaman Approval Warehouse
        $query = MutasiMaterial::with(['pembuat', 'penyetuju'])
                    ->whereIn('ke_dept', ['Warehouse', 'WH', 'Supplier']); 

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

    public function approve($id)
    {
        $mutasi = MutasiMaterial::findOrFail($id);
        
        // PENGAMAN: Mencegah klik ganda
        if ($mutasi->status_approval !== 'Pending') {
            return redirect()->back()->with('error', 'Hanya mutasi berstatus Pending yang dapat diproses.');
        }

        DB::beginTransaction();

        try {
            // A. TENTUKAN TABEL DEPARTEMEN ASAL (PENGIRIM)
            $tabelAsal = '';
            if ($mutasi->dari_dept == 'Warehouse' || $mutasi->dari_dept == 'WH') {
                $tabelAsal = 'warehouse_stocks';
            } elseif ($mutasi->dari_dept == 'QC' || $mutasi->dari_dept == 'Quality') {
                $tabelAsal = 'quality_stocks';
            } elseif ($mutasi->dari_dept == 'Produksi') {
                $tabelAsal = 'produksi_stocks';
            } elseif ($mutasi->dari_dept == 'Purchasing' || $mutasi->dari_dept == 'Karantina') {
                $tabelAsal = 'karantina_stocks'; 
            } else {
                throw new \Exception("Sistem belum mendukung pemotongan stok untuk departemen asal: $mutasi->dari_dept");
            }

            // B. VALIDASI & POTONG STOK DEPARTEMEN ASAL
            $queryAsal = DB::table($tabelAsal)->where('no_mm', $mutasi->mm);
            
            if ($tabelAsal == 'karantina_stocks') {
                $queryAsal->where('status_karantina', $mutasi->status_asal);
            } else {
                $queryAsal->where('batch', $mutasi->batch);
            }
            
            $stokAsal = $queryAsal->first();
            if (!$stokAsal || $stokAsal->qty < $mutasi->qty) {
                throw new \Exception("Gagal! Stok fisik di departemen asal tidak mencukupi untuk mutasi ini.");
            }
            
            // Eksekusi potong stok
            if ($tabelAsal == 'karantina_stocks') {
                DB::table($tabelAsal)->where('no_mm', $mutasi->mm)
                                     ->where('status_karantina', $mutasi->status_asal)
                                     ->decrement('qty', $mutasi->qty);
            } else {
                DB::table($tabelAsal)->where('no_mm', $mutasi->mm)
                                     ->where('batch', $mutasi->batch)
                                     ->decrement('qty', $mutasi->qty);
            }

            // =========================================================
            // C. LOGIKA BERCABANG: KE WAREHOUSE VS RETUR SUPPLIER
            // =========================================================
            if ($mutasi->ke_dept == 'Supplier') {
                // LOGIKA KHUSUS RETUR KE SUPPLIER
                // 1. Cari data incoming_materials yang terkait
                $incoming = DB::table('incoming_materials')
                                ->where('mm', $mutasi->mm)
                                ->whereIn('status_qc', ['Hold', 'NG'])
                                ->whereNull('deleted_at')
                                ->orderBy('id', 'desc')
                                ->first();

                if ($incoming) {
                    // 2. Pastikan statusnya menjadi NG dan ubah lokasinya
                    DB::table('incoming_materials')->where('id', $incoming->id)->update([
                        'status_qc' => 'NG',
                        'lokasi_stok' => 'Retur Keluar (Supplier)',
                        'updated_at' => Carbon::now()
                    ]);

                    // 3. TRIGGER SINKRONISASI PURCHASING (PO Terbuka Kembali)
                    $this->syncPurchaseOrderBalance($incoming->po_kpdt_number);
                }
                
                $pesanSukses = 'Retur ke Supplier disetujui! Barang keluar pabrik dan Outstanding PO telah diperbarui.';

            } else {
                // LOGIKA STANDAR: KEMBALIKAN STOK KE WAREHOUSE STOCKS
                $stokPenerima = DB::table('warehouse_stocks')
                                    ->where('no_mm', $mutasi->mm)
                                    ->where('batch', $mutasi->batch)
                                    ->first();
                                    
                if ($stokPenerima) {
                    DB::table('warehouse_stocks')
                        ->where('no_mm', $mutasi->mm)
                        ->where('batch', $mutasi->batch)
                        ->increment('qty', $mutasi->qty);
                } else {
                    DB::table('warehouse_stocks')->insert([
                        'no_mm'      => $mutasi->mm,
                        'batch'      => $mutasi->batch, 
                        'qty'        => $mutasi->qty,
                        'status'     => 'Aktif',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
                
                $pesanSukses = 'Mutasi disetujui! Stok pengirim dipotong dan otomatis masuk ke Stok Warehouse.';
            }

            // D. UPDATE STATUS MUTASI
            $mutasi->update([
                'status_approval' => 'Approved',
                'approved_by' => Auth::id(),
                'approved_at' => Carbon::now(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', $pesanSukses);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject($id)
    {
        $mutasi = MutasiMaterial::findOrFail($id);
        
        if ($mutasi->status_approval !== 'Pending') {
            return redirect()->back()->with('error', 'Hanya mutasi berstatus Pending yang dapat ditolak.');
        }

        $mutasi->update([
            'status_approval' => 'Rejected',
            'approved_by' => Auth::id(),
            'approved_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Mutasi retur berhasil ditolak.');
    }

    // ==========================================================
    // FUNGSI UNTUK HALAMAN MUTASI KELUAR (WH MENGIRIM KE DEPT LAIN)
    // ==========================================================
    public function mutasiKeluarIndex(Request $request)
    {
        $query = MutasiMaterial::with(['pembuat', 'penyetuju'])
                    ->whereIn('dari_dept', ['Warehouse', 'WH']); 

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
        return view('warehouse.mutasi.mutasi-stok', compact('riwayat_mutasi'));
    }

    public function storeMutasiKeluar(Request $request)
    {
        // 1. Validasi Array (Keranjang Multi-Item)
        $request->validate([
            'tanggal' => 'required|date',
            'shift' => 'required',
            'ke_dept' => 'required',
            'mm' => 'required|array',
            'mm.*' => 'required',
            'qty' => 'required|array', 
            'qty.*' => 'required|numeric|min:0.1' 
        ]);

        // 2. Looping untuk menyimpan setiap material yang ada di keranjang
        foreach ($request->mm as $index => $mm) {
            // Abaikan baris jika tidak sengaja kosong
            if(empty($mm) || empty($request->qty[$index])) continue;

            \App\Models\MutasiMaterial::create([
                'tanggal' => $request->tanggal,
                'shift' => $request->shift,
                'dari_dept' => 'Warehouse',
                'ke_dept' => $request->ke_dept,
                'mm' => $mm,
                'item_name' => $request->item_name[$index] ?? null,
                'batch' => $request->batch[$index] ?? null,
                'qty' => $request->qty[$index],
                'uom' => $request->uom[$index] ?? 'Pcs',
                'pic_id' => \Illuminate\Support\Facades\Auth::id(),
                'status_approval' => 'Pending',
                
                // Menyimpan jejak bahwa ini adalah mutasi untuk WO tertentu (opsional)
                'catatan' => $request->fg_mm ? 'Mutasi Produksi FG: ' . $request->fg_mm : null, 
            ]);
        }

        $totalMaterial = count($request->mm);

        return redirect()->back()->with('success', 'Luar biasa! ' . $totalMaterial . ' Material telah berhasil dimutasi sekaligus ke ' . $request->ke_dept);
    }

    public function getItemByMm(Request $request)
    {
        $item = DB::table('incoming_materials')
            ->where('mm', $request->mm)->whereNotNull('item_name')->first();

        return $item ? response()->json(['success' => true, 'item_name' => $item->item_name]) : response()->json(['success' => false]);
    }

    // ==========================================================
    // FUNGSI PRINT & RIWAYAT GABUNGAN
    // ==========================================================
    public function print(Request $request)
    {
        // REVISI: Tambahkan Supplier agar tercetak di laporan
        $query = MutasiMaterial::with(['pembuat', 'penyetuju'])
                    ->whereIn('ke_dept', ['Warehouse', 'WH', 'Supplier'])
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
        return view('warehouse.mutasi.print', compact('data_print', 'request'));
    }

    public function riwayatMutasiGabungan(Request $request)
    {
        $deptAktif = ['Warehouse', 'WH', 'Supplier']; // REVISI

        $query = MutasiMaterial::with(['pembuat', 'penyetuju'])
                    ->where(function($q) use ($deptAktif) {
                        $q->whereIn('ke_dept', $deptAktif)
                          ->orWhereIn('dari_dept', $deptAktif);
                    })
                    ->where('status_approval', 'Approved');

        if ($request->filled('jenis')) {
            if ($request->jenis == 'masuk') {
                $query->whereIn('ke_dept', $deptAktif);
            } elseif ($request->jenis == 'keluar') {
                $query->whereIn('dari_dept', $deptAktif);
            }
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai]);
        }
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

        $riwayat = $query->orderBy('tanggal', 'DESC')->get();

        return view('warehouse.mutasi.riwayat-gabungan', compact('riwayat', 'request'));
    }

    // =================================================================================
    // FUNGSI HELPER: SINKRONISASI KE PURCHASING (UNTUK RETUR SUPPLIER)
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