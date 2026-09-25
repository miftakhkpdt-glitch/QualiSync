<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MasterDefect;

class MasterDefectController extends Controller
{
    public function index()
{
    // Ambil data defect dari database
    $defects = MasterDefect::all(); // atau Model yang Anda gunakan

    // Ubah dari compact('masterDefects') menjadi compact('defects')
    return view('quality.master-defect.index', compact('defects'));
}

    public function store(Request $request)
    {
        $request->validate([
            'nama_defect' => 'required|string|max:255',
        ]);

        DB::table('master_defects')->insert([
            'nama_defect' => $request->nama_defect,
            'kategori'    => $request->kategori,
            'deskripsi'   => $request->deskripsi,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return redirect()->back()->with('success', 'Jenis defect berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        DB::table('master_defects')->where('id', $id)->update([
            'nama_defect' => $request->nama_defect,
            'kategori'    => $request->kategori,
            'deskripsi'   => $request->deskripsi,
            'updated_at'  => now(),
        ]);

        return redirect()->back()->with('success', 'Jenis defect berhasil diperbarui!');
    }

    public function destroy($id)
    {
        DB::table('master_defects')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Jenis defect berhasil dihapus!');
    }
}