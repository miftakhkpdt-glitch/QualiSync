<?php

namespace App\Http\Controllers\Fat;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CashBankController extends Controller
{
    public function index()
    {
        // 1. Tarik riwayat transaksi kas/bank
        $ledgers = DB::table('cash_bank_ledgers')
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Hitung total uang masuk, keluar, dan saldo akhir
        $totalMasuk = DB::table('cash_bank_ledgers')->where('tipe', 'Masuk')->sum('nominal');
        $totalKeluar = DB::table('cash_bank_ledgers')->where('tipe', 'Keluar')->sum('nominal');
        $saldoAkhir = $totalMasuk - $totalKeluar;

        return view('fat.cash_banks.index', compact('ledgers', 'totalMasuk', 'totalKeluar', 'saldoAkhir'));
    }

    public function store(Request $request)
    {
        // Fungsi untuk mencatat transaksi kas manual (misal: setor tunai, tarik tunai, transfer)
        $request->validate([
            'tanggal'    => 'required|date',
            'keterangan' => 'required|string|max:255',
            'tipe'       => 'required|in:Masuk,Keluar',
            'nominal'    => 'required|numeric|min:1',
        ]);

        DB::table('cash_bank_ledgers')->insert([
            'tanggal'    => $request->tanggal,
            'keterangan' => $request->keterangan,
            'tipe'       => $request->tipe,
            'nominal'    => $request->nominal,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return redirect()->route('fat.cash_banks.index')->with('success', 'Transaksi Kas & Bank berhasil dicatat!');
    }
}