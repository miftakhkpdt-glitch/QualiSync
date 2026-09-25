<?php

namespace App\Http\Controllers\Fat;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OperationalExpenseController extends Controller
{
    // Menampilkan daftar biaya
    public function index()
    {
        $expenses = DB::table('operational_expenses')
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('fat.operational_expenses.index', compact('expenses'));
    }

    // Menampilkan form tambah biaya
    public function create()
    {
        return view('fat.operational_expenses.create');
    }

    // Menyimpan data ke database
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'    => 'required|date',
            'nama_biaya' => 'required|string|max:255',
            'kategori'   => 'required|string|max:100',
            'nominal'    => 'required|numeric|min:0',
            'keterangan' => 'nullable|string'
        ]);

        DB::table('operational_expenses')->insert([
            'tanggal'    => $request->tanggal,
            'nama_biaya' => $request->nama_biaya,
            'kategori'   => $request->kategori,
            'nominal'    => $request->nominal,
            'keterangan' => $request->keterangan,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return redirect()->route('fat.operational_expenses.index')
                         ->with('success', 'Biaya operasional berhasil dicatat!');
    }
}