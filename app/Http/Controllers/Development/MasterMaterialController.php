<?php

namespace App\Http\Controllers\Development;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class MasterMaterialController extends Controller
{
    public function index()
    {
        $materials = DB::table('master_materials')->get();
        return view('development.master-material', compact('materials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_mm' => 'required|unique:master_materials,no_mm',
            'nama_material' => 'required',
            'kategori' => 'required',
            'satuan' => 'required',
        ]);

        DB::table('master_materials')->insert([
            'no_mm' => $request->no_mm,
            'nama_material' => $request->nama_material,
            'kategori' => $request->kategori,
            'satuan' => $request->satuan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('development.master-material.index')->with('success', 'Master Material berhasil ditambahkan.');
    }
    
    // =================================================================
    // [BARU] Fungsi Hapus Data dengan Proteksi Transaksi Aktif
    // =================================================================
    public function destroy($id)
    {
        try {
            // Mengeksekusi penghapusan berdasarkan ID
            $deleted = DB::table('master_materials')->where('id', $id)->delete();

            if ($deleted) {
                return redirect()->back()->with('success', 'Data Master Material berhasil dihapus secara permanen.');
            }

            return redirect()->back()->with('error', 'Data tidak ditemukan atau sudah terhapus.');

        } catch (QueryException $e) {
            // Error Code 23000 adalah kode MySQL untuk Foreign Key Constraint Violation (Data sedang dipakai di tabel lain)
            if ($e->getCode() == "23000") {
                return redirect()->back()->with('error', 'GAGAL! Material ini tidak bisa dihapus karena sudah terhubung/digunakan dalam transaksi (Stok, PR, WO, dll).');
            }
            
            // Tangkap error sistem lainnya
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}