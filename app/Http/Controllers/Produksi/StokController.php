<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $kategori = $request->kategori;

        // Mengambil data stok Produksi dan melakukan JOIN dengan master_materials
        $query = DB::table('produksi_stocks')
            ->leftJoin('master_materials', 'produksi_stocks.no_mm', '=', 'master_materials.no_mm')
            ->select('produksi_stocks.*', 'master_materials.nama_material', 'master_materials.kategori')
            ->where('produksi_stocks.qty', '>', 0);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('produksi_stocks.no_mm', 'like', "%{$search}%")
                  ->orWhere('produksi_stocks.batch', 'like', "%{$search}%")
                  ->orWhere('master_materials.nama_material', 'like', "%{$search}%");
            });
        }

        if ($kategori) {
            $query->where('master_materials.kategori', $kategori);
        }

        $stokList = $query->orderBy('produksi_stocks.updated_at', 'DESC')->get();

        return view('produksi.stok.index', compact('stokList', 'request'));
    }
    public function tambahStok(Request $request)
    {
        $request->validate([
            'no_mm' => 'required',
            'qty' => 'required|numeric|min:0.1',
            'batch' => 'nullable'
        ]);

        $batch = $request->batch ?? '-';

        // 1. Tambah stok fisik di Produksi
        $stok = DB::table('produksi_stocks')->where('no_mm', $request->no_mm)->where('batch', $batch)->first();
        
        if ($stok) {
            DB::table('produksi_stocks')->where('id', $stok->id)->increment('qty', $request->qty);
        } else {
            DB::table('produksi_stocks')->insert([
                'no_mm' => $request->no_mm,
                'batch' => $batch,
                'qty' => $request->qty,
                'status' => 'Aktif',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);
        }

        // 2. Catat di riwayat mutasi sebagai "Hasil Produksi" agar traceable
        \App\Models\MutasiMaterial::create([
            'tanggal' => date('Y-m-d'),
            'shift' => '1',
            'dari_dept' => 'Mesin Produksi', // Sumber dari mesin
            'ke_dept' => 'Produksi',       // Masuk ke ruang produksi
            'mm' => $request->no_mm,
            'item_name' => '-', // Bisa ditarik dari master jika perlu
            'batch' => $batch,
            'status_asal' => 'Pass',
            'qty' => $request->qty,
            'uom' => 'Pcs',
            'pic_id' => \Illuminate\Support\Facades\Auth::id(),
            'status_approval' => 'Approved', // Langsung approved karena milik sendiri
            'catatan' => 'Input Hasil Produksi Manual',
        ]);

        return redirect()->back()->with('success', 'Stok Hasil Produksi berhasil ditambahkan!');
    }
}