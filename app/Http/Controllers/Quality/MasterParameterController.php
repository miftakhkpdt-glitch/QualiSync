<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterParameterController extends Controller
{
    // Menampilkan daftar parameter dan form tambah
    public function index()
    {
        $parameters = DB::table('master_parameters')->orderBy('id', 'desc')->get();
        return view('quality.master-parameter.index', compact('parameters'));
    }

    // Menyimpan parameter baru yang diinput oleh Admin QC
    public function store(Request $request)
    {
        $request->validate([
            'nama_parameter' => 'required|string|max:255',
            'tipe_input'     => 'required|in:Angka,Teks',
            'satuan'         => 'nullable|string|max:50',
        ]);

        DB::table('master_parameters')->insert([
            'nama_parameter' => $request->nama_parameter,
            'tipe_input'     => $request->tipe_input,
            'satuan'         => $request->satuan,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return redirect()->back()->with('success', 'Parameter baru berhasil ditambahkan!');
    }

    // Menghapus parameter
    public function destroy($id)
    {
        DB::table('master_parameters')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Parameter berhasil dihapus!');
    }
}