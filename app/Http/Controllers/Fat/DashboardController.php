<?php

namespace App\Http\Controllers\Fat;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // === 1. DATA PIUTANG (AR) ===
        $totalOmzet = DB::table('sales_invoices')->where('status_pembayaran', 'Paid')->sum('total_tagihan');
        $totalPiutang = DB::table('sales_invoices')->whereIn('status_pembayaran', ['Unpaid', 'Partial'])->sum('total_tagihan');
        $invoicePending = DB::table('sales_invoices')->whereIn('status_pembayaran', ['Unpaid', 'Partial'])->count();

        // === 2. DATA HUTANG (AP) ===
        $totalHutangDibayar = DB::table('purchase_invoices')->where('status_pembayaran', 'Paid')->sum('total_tagihan');
        $totalHutangPending = DB::table('purchase_invoices')->whereIn('status_pembayaran', ['Unpaid', 'Partial'])->sum('total_tagihan');
        $apPending = DB::table('purchase_invoices')->whereIn('status_pembayaran', ['Unpaid', 'Partial'])->count();

        // === 3. DATA KAS & BANK (TREASURY) ===
        $totalMasuk = DB::table('cash_bank_ledgers')->where('tipe', 'Masuk')->sum('nominal');
        $totalKeluar = DB::table('cash_bank_ledgers')->where('tipe', 'Keluar')->sum('nominal');
        $saldoKasBank = $totalMasuk - $totalKeluar;

        // === 4. PERFORMA BULAN INI (LABA RUGI) ===
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');

        $pendapatanBulanIni = DB::table('sales_invoices')->whereBetween('tanggal_invoice', [$startOfMonth, $endOfMonth])->sum('subtotal');
        $pembelianBulanIni = DB::table('purchase_invoices')->whereBetween('tanggal_invoice', [$startOfMonth, $endOfMonth])->sum('subtotal');
        $opexBulanIni = DB::table('operational_expenses')->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->sum('nominal');
        
        $labaBersihBulanIni = $pendapatanBulanIni - $pembelianBulanIni - $opexBulanIni;
        
        // Nama bulan untuk ditampilkan di layar (contoh: "Agustus 2026")
        $namaBulanIni = Carbon::now()->isoFormat('MMMM Y');

        return view('fat.dashboard', compact(
            'totalOmzet', 'totalPiutang', 'invoicePending', 
            'totalHutangDibayar', 'totalHutangPending', 'apPending',
            'saldoKasBank', 'opexBulanIni', 'labaBersihBulanIni', 'namaBulanIni'
        ));
    }
}