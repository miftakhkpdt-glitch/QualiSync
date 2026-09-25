<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MutasiMaterial;
use Illuminate\Support\Facades\Auth;

class KarantinaController extends Controller
{
    // Tampilkan Stok Karantina & Riwayat Mutasi Keluarnya
    public function index(Request $request)
    {
        // 1. Ambil data Stok Karantina
        $query = DB::table('karantina_stocks')
            ->leftJoin('materials', 'karantina_stocks.no_mm', '=', 'materials.kode_material')
            ->select('karantina_stocks.*', 'materials.nama_material')
            ->where('karantina_stocks.qty', '>', 0);

        if ($request->filled('search')) {
            $query->where('karantina_stocks.no_mm', 'like', '%' . $request->search . '%')
                  ->orWhere('materials.nama_material', 'like', '%' . $request->search . '%');
        }

        $stok_karantina = $query->orderBy('karantina_stocks.updated_at', 'DESC')->get();

        // 2. Ambil data Riwayat Mutasi dari Purchasing ke Dept Lain
        $riwayat_mutasi = MutasiMaterial::where('dari_dept', 'Purchasing')
                            ->orderBy('id', 'DESC')
                            ->get();

        return view('purchasing.karantina.index', compact('stok_karantina', 'riwayat_mutasi', 'request'));
    }

    // Proses Pengiriman Mutasi dari Karantina ke Dept Lain
    public function mutasiKeluar(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'ke_dept' => 'required',
            'mm'      => 'required',
            'status_karantina' => 'required', // Wajib tahu statusnya apa
            'qty'     => 'required|numeric|min:0.1'
        ]);

        $stokKarantina = DB::table('karantina_stocks')
            ->where('no_mm', $request->mm)
            ->where('status_karantina', $request->status_karantina)
            ->first();
            
        if (!$stokKarantina || $stokKarantina->qty < $request->qty) {
            return redirect()->back()->with('error', 'Gagal! Saldo stok Karantina tidak mencukupi.');
        }

        $material = DB::table('materials')->where('kode_material', $request->mm)->first();
        $itemName = $material ? $material->nama_material : '-';

        MutasiMaterial::create([
            'tanggal'         => $request->tanggal,
            'shift'           => '1',
            'dari_dept'       => 'Purchasing',
            'ke_dept'         => $request->ke_dept,
            'mm'              => $request->mm,
            'item_name'       => $itemName,
            'batch'           => $request->status_karantina, // Kita titipkan status (Hold/NG) di kolom batch
            'qty'             => $request->qty,
            'uom'             => 'Pcs',
            'pic_id'          => Auth::id(),
            'status_approval' => 'Pending',
            'catatan'         => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Berhasil! Mutasi dari Karantina telah dibuat.');
    }
}