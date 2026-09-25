<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MutasiMaterial;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon;

class MutasiStokController extends Controller
{
    // 1. Tampilkan Halaman Approval beserta Filter
    public function index(Request $request)
    {
        $query = MutasiMaterial::with(['pembuat', 'penyetuju'])
                    ->where('ke_dept', 'QC'); // QC sebagai penerima barang

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

        return view('quality.mutasi.index', compact('riwayat_mutasi'));
    }

    // 2. FUNGSI APPROVE (POTONG STOK PENGIRIM & TAMBAH STOK QC)
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
            
            // PERUBAHAN: Khusus Karantina cari pakai status_asal, selain itu pakai batch
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

            // C. TAMBAHKAN STOK KE DEPARTEMEN PENERIMA (QUALITY STOCKS)
            // PERUBAHAN: Pastikan mengecek dan memasukkan batch
            $stokPenerima = DB::table('quality_stocks')
                                ->where('no_mm', $mutasi->mm)
                                ->where('batch', $mutasi->batch)
                                ->first();
                                
            if ($stokPenerima) {
                DB::table('quality_stocks')
                    ->where('no_mm', $mutasi->mm)
                    ->where('batch', $mutasi->batch)
                    ->increment('qty', $mutasi->qty);
            } else {
                DB::table('quality_stocks')->insert([
                    'no_mm'      => $mutasi->mm,
                    'batch'      => $mutasi->batch, // <-- Masukkan batch (bisa berisi '-' atau kode batch sesungguhnya)
                    'qty'        => $mutasi->qty,
                    'status'     => 'Aktif',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            // D. UPDATE STATUS MUTASI
            $mutasi->update([
                'status_approval' => 'Approved',
                'approved_by' => Auth::id(),
                'approved_at' => Carbon::now(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Mutasi disetujui! Stok pengirim berhasil dipotong dan otomatis masuk ke Stok Quality.');

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

        return redirect()->back()->with('success', 'Mutasi berhasil ditolak.');
    }

    public function print(Request $request)
    {
        $query = MutasiMaterial::with(['pembuat', 'penyetuju'])
                    ->where('ke_dept', 'QC')
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

        return view('quality.mutasi.print', compact('data_print', 'request'));
    }
    
    // ==============================================================
    // BAGIAN MUTASI KELUAR (QUALITY MENGIRIM KE DEPT LAIN)
    // ==============================================================
    
    public function mutasiKeluarIndex(Request $request)
    {
        $query = MutasiMaterial::with(['pembuat', 'penyetuju'])
                    ->where('dari_dept', 'QC');

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
        return view('quality.mutasi.mutasi-stok', compact('riwayat_mutasi'));
    }

    public function storeMutasiKeluar(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'shift' => 'required',
            'ke_dept' => 'required',
            'mm' => 'required',
            'qty' => 'required|numeric|min:0.1'
        ]);

        // PENGAMAN (DIREVISI): Cek apakah stok di Quality mencukupi berdasarkan MM & BATCH-nya!
        $stokQc = DB::table('quality_stocks')
                    ->where('no_mm', $request->mm)
                    ->where('batch', $request->batch ?? '-') // <-- Kini mencari berdasarkan batch juga
                    ->first();
                    
        if (!$stokQc || $stokQc->qty < $request->qty) {
            return redirect()->back()->with('error', 'Gagal! Saldo Stok Quality untuk batch tersebut tidak mencukupi.');
        }

        MutasiMaterial::create([
            'tanggal' => $request->tanggal,
            'shift' => $request->shift,
            'dari_dept' => 'QC',
            'ke_dept' => $request->ke_dept,
            'mm' => $request->mm,
            'item_name' => $request->item_name,
            'batch' => $request->batch ?? '-', // <-- Memastikan tidak null
            'status_asal' => 'Pass', // Standar default karena QC mengirim barang Pass/OK
            'qty' => $request->qty,
            'uom' => $request->uom ?? 'Pcs',
            'pic_id' => Auth::id(),
            'status_approval' => 'Pending',
            'catatan' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Berhasil! Mutasi keluar telah dibuat dan sedang menunggu Approval dari tujuan.');
    }

    public function getItemByMm(Request $request)
    {
        $item = DB::table('incoming_materials')
            ->where('mm', $request->mm)->whereNotNull('item_name')->first();

        return $item ? response()->json(['success' => true, 'item_name' => $item->item_name]) : response()->json(['success' => false]);
    }
}