<?php

namespace App\Http\Controllers\Development;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\DevelopmentProject;

class DevelopmentController extends Controller
{
    public function index()
    {
        return $this->dashboard();
    }

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
        $validated = $request->validate([
            'judul_riset' => 'required|string|max:255',
            'kode_material' => 'required|string|max:50',
            'target_suhu' => 'nullable|numeric',
            'status' => 'required|in:Planning,On Progress,Evaluation,Completed',
            'keterangan' => 'nullable|string',
        ]);

        DevelopmentProject::create([
            ...$validated,
            'dibuat_oleh' => auth()->user()->name ?? 'Development Team',
        ]);

        return redirect('/development/dashboard')->with('success', 'Data riset development berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'judul_riset' => 'required|string|max:255',
            'kode_material' => 'required|string|max:50',
            'target_suhu' => 'nullable|numeric',
            'status' => 'required|in:Planning,On Progress,Evaluation,Completed',
            'keterangan' => 'nullable|string',
        ]);

        $project = DevelopmentProject::findOrFail($id);
        $project->update($validated);

        return redirect('/development/dashboard')->with('success', 'Data riset berhasil diperbarui!');
    }

    // Menghapus data riset
    public function destroy($id)
    {
        $project = DevelopmentProject::findOrFail($id);
        $project->delete();

        return redirect('/development/dashboard')->with('success', 'Data riset berhasil dihapus.');
    }
}