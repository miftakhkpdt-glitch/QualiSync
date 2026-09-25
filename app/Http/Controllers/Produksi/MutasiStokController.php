<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MutasiMaterial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon;

class MutasiStokController extends Controller
{
    // 1. Tampilkan Halaman Approval (Produksi Menerima Barang)
    public function index(Request $request)
    {
        $query = MutasiMaterial::with(['pembuat', 'penyetuju'])
                    ->where('ke_dept', 'Produksi'); // Produksi sebagai penerima

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

        return view('produksi.mutasi.index', compact('riwayat_mutasi'));
    }

    // 2. FUNGSI APPROVE (POTONG STOK PENGIRIM & TAMBAH STOK PRODUKSI)
    public function approve($id)
    {
        $mutasi = MutasiMaterial::findOrFail($id);
        
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
                // Trik menyamakan pencarian Batch Kosong
                if (empty($mutasi->batch) || $mutasi->batch == '-') {
                    $queryAsal->where(function($q) {
                        $q->whereNull('batch')
                          ->orWhere('batch', '')
                          ->orWhere('batch', '-');
                    });
                } else {
                    $queryAsal->where('batch', $mutasi->batch);
                }
            }
            
            $stokAsal = $queryAsal->first();
            
            if (!$stokAsal || $stokAsal->qty < $mutasi->qty) {
                $stokTersedia = $stokAsal ? $stokAsal->qty : 0;
                throw new \Exception("Gagal! Stok fisik di $mutasi->dari_dept hanya tercatat $stokTersedia, sedangkan mutasi meminta $mutasi->qty.");
            }
            
            // Eksekusi potong stok MENGGUNAKAN ID baris yang pasti
            DB::table($tabelAsal)->where('id', $stokAsal->id)->decrement('qty', $mutasi->qty);

            // C. TAMBAHKAN STOK KE DEPARTEMEN PENERIMA (PRODUKSI STOCKS)
            // (Hanya mencari berdasarkan no_mm karena tabel produksi tidak mencatat batch)
            $stokPenerima = DB::table('produksi_stocks')
                                ->where('no_mm', $mutasi->mm)
                                ->first();
                                
            if ($stokPenerima) {
                DB::table('produksi_stocks')->where('id', $stokPenerima->id)->increment('qty', $mutasi->qty);
            } else {
                DB::table('produksi_stocks')->insert([
                    'no_mm'      => $mutasi->mm,
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

            return redirect()->back()->with('success', 'Mutasi disetujui! Stok pengirim berhasil dipotong dan otomatis masuk ke Stok Produksi.');

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

    // ==============================================================
    // BAGIAN MUTASI KELUAR (PRODUKSI MENGIRIM KE DEPT LAIN)
    // ==============================================================
    
    public function mutasiKeluarIndex(Request $request)
    {
        $query = MutasiMaterial::with(['pembuat', 'penyetuju'])
                    ->where('dari_dept', 'Produksi');

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
        return view('produksi.mutasi.mutasi-stok', compact('riwayat_mutasi'));
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

        // PENGAMAN: Cek saldo di Produksi
        $stokProduksi = DB::table('produksi_stocks')
                    ->where('no_mm', $request->mm)
                    ->where('batch', $request->batch ?? '-') 
                    ->first();
                    
        if (!$stokProduksi || $stokProduksi->qty < $request->qty) {
            return redirect()->back()->with('error', 'Gagal! Saldo Stok Produksi untuk batch tersebut tidak mencukupi untuk melakukan pengeluaran ini.');
        }

        MutasiMaterial::create([
            'tanggal' => $request->tanggal,
            'shift' => $request->shift,
            'dari_dept' => 'Produksi',
            'ke_dept' => $request->ke_dept,
            'mm' => $request->mm,
            'item_name' => $request->item_name,
            'batch' => $request->batch ?? '-', 
            'status_asal' => 'Pass', // Asumsi hasil produksi / barang yang dikembalikan statusnya Pass
            'qty' => $request->qty,
            'uom' => $request->uom ?? 'Pcs',
            'pic_id' => Auth::id(),
            'status_approval' => 'Pending', // Menunggu departemen tujuan (misal WH) klik Approve
            'catatan' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Berhasil! Mutasi keluar telah dibuat dan sedang menunggu Approval dari tujuan.');
    }
}