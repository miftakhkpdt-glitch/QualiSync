<?php

namespace App\Http\Controllers\Development;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BomController extends Controller
{
    // Menampilkan halaman daftar BOM & Form Tambah
    public function index(Request $request)
    {
        $query = DB::table('product_boms');

        // Jika ada filter FG yang dipilih
        if ($request->filled('filter_fg')) {
            $query->where('fg_mm', $request->filter_fg);
        }

        $boms = $query->orderBy('id', 'DESC')->get();

        // Ambil data master material untuk pemetaan nama
        $materialsMap = DB::table('master_materials')->get()->keyBy('no_mm');

        foreach ($boms as $row) {
            $row->fg_name = $materialsMap[$row->fg_mm]->nama_material ?? '-';
            $row->comp_name = $materialsMap[$row->component_mm]->nama_material ?? '-';
        }

        $finishGoods = DB::table('master_materials')->where('kategori', 'Finish Good')->get();
        $materials = DB::table('master_materials')->get();

        return view('development.bom', compact('boms', 'finishGoods', 'materials'));
    }

    // Menyimpan resep BOM baru
    public function store(Request $request)
    {
        $request->validate([
            'fg_mm' => 'required',
            'components' => 'required|array|min:1',
            'components.*.component_mm' => 'required',
            'components.*.qty_usage' => 'required|numeric',
            'components.*.satuan' => 'required',
        ]);

        // Loop dan simpan setiap baris komponen yang diinput beserta satuannya
        foreach ($request->components as $comp) {
            DB::table('product_boms')->insert([
                'fg_mm' => $request->fg_mm,
                'component_mm' => $comp['component_mm'],
                'qty_usage' => $comp['qty_usage'],
                'satuan' => $comp['satuan'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Resep BOM multi-komponen berhasil disimpan.');
    }

    // Menghapus resep BOM
    public function destroy($id)
    {
        DB::table('product_boms')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Resep BOM berhasil dihapus.');
    }
    public function update(Request $request, $id)
    {
        // Menyimpan perubahan Qty dan Satuan ke tabel product_boms
        DB::table('product_boms')->where('id', $id)->update([
            'qty_usage' => $request->qty_usage,
            'satuan'    => $request->satuan,
            'updated_at'=> now()
        ]);

        return redirect()->back()->with('success', 'Data resep BOM berhasil diperbarui!');
    }
}