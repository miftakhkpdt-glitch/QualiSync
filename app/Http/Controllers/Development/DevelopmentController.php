<?php

namespace App\Http\Controllers\Development;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\DevelopmentProject;

class DevelopmentController extends Controller
{
    // Menampilkan halaman dashboard development & daftar riset
    public function dashboard() // <-- Saya sesuaikan namanya menjadi dashboard
    {
        $projects = DevelopmentProject::latest()->get();
        
        // Menarik angka total material untuk KPI Dashboard
        $totalMaterial = DB::table('master_materials')->count();

        // Memanggil view yang baru: development/dashboard.blade.php
        return view('development.dashboard', compact('projects', 'totalMaterial'));
    }

    // Menyimpan data proyek riset baru
    public function store(Request $request)
    {
        $request->validate([
            'judul_riset' => 'required|string|max:255',
            'kode_material' => 'required|string|max:50',
            'target_suhu' => 'nullable|numeric',
            'status' => 'required',
        ]);

        DevelopmentProject::create([
            'judul_riset' => $request->judul_riset,
            'kode_material' => $request->kode_material,
            'target_suhu' => $request->target_suhu,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'dibuat_oleh' => auth()->user()->name ?? 'Development Team',
        ]);

        return redirect('/development/dashboard')->with('success', 'Data riset development berhasil ditambahkan!');
    }

    // Menghapus data riset
    public function destroy($id)
    {
        $project = DevelopmentProject::findOrFail($id);
        $project->delete();

        return redirect('/development/dashboard')->with('success', 'Data riset berhasil dihapus.');
    }
}