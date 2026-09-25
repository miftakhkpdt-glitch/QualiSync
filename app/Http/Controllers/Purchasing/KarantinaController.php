<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MutasiMaterial;
use Illuminate\Support\Facades\Auth;

class KarantinaController extends Controller
{
    // Tampilkan Stok Karantina & 2 Riwayat Transaksinya
    public function index(Request $request)
    {
        // =========================================================
        // 1. STOK KARANTINA (Akumulasi Total)
        // =========================================================
        $query = DB::table('karantina_stocks')->where('qty', '>', 0);

        if ($request->filled('search')) {
            $query->where('no_mm', 'like', '%' . $request->search . '%');
        }

        $stokKarantina = $query->orderBy('updated_at', 'DESC')->get();

        // SISTEM FALLBACK: Cari nama material satu per satu agar kebal error
        foreach ($stokKarantina as $item) {
            // Mengacu ke tabel master_materials (sesuai struktur Single Source of Truth)
            $master = DB::table('master_materials')->where('no_mm', $item->no_mm)->first();
            
            if ($master && !empty($master->nama_material)) {
                $item->nama_material = $master->nama_material;
            } else {
                // Jika di master kosong, pinjam nama dari riwayat Incoming
                $incoming = DB::table('incoming_materials')->where('mm', $item->no_mm)->first();
                $item->nama_material = $incoming ? $incoming->item_name : '-';
            }
        }

        // =========================================================
        // 2. RIWAYAT MASUK KE KARANTINA (Dari QC / Incoming)
        // =========================================================
        $riwayatMasuk = DB::table('incoming_materials')
            ->select(
                'updated_at as tanggal', 
                DB::raw("'QC / Incoming' as dari_dept"), 
                'mm', 
                'item_name as nama_material', 
                'quantity as qty', 
                'status_qc as status', 
                'keterangan_qc as keterangan'
            )
            ->whereIn('status_qc', ['Hold', 'NG'])
            ->where('lokasi_stok', 'Karantina')
            ->orderBy('updated_at', 'DESC')
            ->get();

        // =========================================================
        // 3. RIWAYAT MUTASI KELUAR (Dari Purchasing ke Dept Lain)
        // =========================================================
        $riwayatKeluar = MutasiMaterial::where('dari_dept', 'Purchasing')
                            ->orderBy('id', 'DESC')
                            ->get();

        // Kirimkan 3 variabel tersebut ke View
        return view('purchasing.karantina.index', compact('stokKarantina', 'riwayatMasuk', 'riwayatKeluar', 'request'));
    }

    // Proses Pengiriman Mutasi dari Karantina ke Dept Lain
    public function mutasiKeluar(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'ke_dept' => 'required',
            'mm'      => 'required',
            'status_karantina' => 'required',
            'qty'     => 'required|numeric|min:0.1'
        ]);

        // Cek ketersediaan stok
        $stok_karantina = DB::table('karantina_stocks')
            ->where('no_mm', $request->mm)
            ->where('status_karantina', $request->status_karantina)
            ->first();
            
        if (!$stok_karantina || $stok_karantina->qty < $request->qty) {
            return redirect()->back()->with('error', 'Gagal! Saldo stok Karantina tidak mencukupi.');
        }

        // SISTEM FALLBACK untuk nama material di riwayat mutasi
        $itemName = '-';
        // Diubah ke master_materials
        $master = DB::table('master_materials')->where('no_mm', $request->mm)->first(); 
        
        if ($master && !empty($master->nama_material)) {
            $itemName = $master->nama_material;
        } else {
            $incoming = DB::table('incoming_materials')->where('mm', $request->mm)->first();
            if ($incoming) {
                $itemName = $incoming->item_name;
            }
        }

        DB::beginTransaction();

        try {
            // 1. Simpan Mutasi
            MutasiMaterial::create([
                'tanggal'         => $request->tanggal,
                'shift'           => '1',
                'dari_dept'       => 'Purchasing',
                'ke_dept'         => $request->ke_dept,
                'mm'              => $request->mm,
                'item_name'       => $itemName,
                'batch'           => '-', // Sesuai ide Anda: Batch dikosongkan (strip)
                'status_asal'     => $request->status_karantina, // Menyimpan Hold/NG secara rahasia
                'qty'             => $request->qty,
                'uom'             => 'Pcs',
                'pic_id'          => Auth::id(),
                'status_approval' => 'Pending',
                'catatan'         => $request->catatan,
            ]);

            // ===============================================
            // KODE DI BAWAH DIMATIKAN AGAR TIDAK DOUBLE DEDUCT
            // (Stok akan otomatis terpotong saat Warehouse klik Approve)
            // ===============================================
            /*
            DB::table('karantina_stocks')
                ->where('no_mm', $request->mm)
                ->where('status_karantina', $request->status_karantina)
                ->decrement('qty', $request->qty);
            */

            DB::commit();
            return redirect()->back()->with('success', 'Berhasil! Mutasi dari Karantina telah dibuat (Menunggu Approval Warehouse).');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}