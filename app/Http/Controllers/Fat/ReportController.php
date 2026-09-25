<?php

namespace App\Http\Controllers\Fat;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function profitLoss(Request $request)
    {
        $startDate = $request->input('start_date', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->input('end_date', \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d'));

        // 1. PENDAPATAN
        $totalPendapatan = DB::table('sales_invoices')
            ->whereBetween('tanggal_invoice', [$startDate, $endDate])
            ->sum('subtotal');

        // 2. PEMBELIAN / COGS
        $totalPembelian = DB::table('purchase_invoices')
            ->whereBetween('tanggal_invoice', [$startDate, $endDate])
            ->sum('subtotal');

        // 3. LABA KOTOR
        $labaKotor = $totalPendapatan - $totalPembelian;

        // 4. BIAYA OPERASIONAL (Baru ditambahkan)
        $totalBiayaOperasional = DB::table('operational_expenses')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('nominal');

        // 5. LABA BERSIH (Net Profit)
        $labaBersih = $labaKotor - $totalBiayaOperasional;

        return view('fat.reports.profit_loss', compact(
            'totalPendapatan', 'totalPembelian', 'labaKotor', 
            'totalBiayaOperasional', 'labaBersih', 
            'startDate', 'endDate'
        ));
    }
    public function exportProfitLoss(Request $request)
    {
        $startDate = $request->input('start_date', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->input('end_date', \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d'));

        // 1. Tarik Data (Sama seperti fungsi view)
        $totalPendapatan = DB::table('sales_invoices')->whereBetween('tanggal_invoice', [$startDate, $endDate])->sum('subtotal');
        $totalPembelian = DB::table('purchase_invoices')->whereBetween('tanggal_invoice', [$startDate, $endDate])->sum('subtotal');
        $labaKotor = $totalPendapatan - $totalPembelian;
        $totalBiayaOperasional = DB::table('operational_expenses')->whereBetween('tanggal', [$startDate, $endDate])->sum('nominal');
        $labaBersih = $labaKotor - $totalBiayaOperasional;

        // 2. Siapkan Nama File CSV/Excel
        $fileName = 'Laporan_Laba_Rugi_' . $startDate . '_sd_' . $endDate . '.csv';

        // 3. Siapkan Header agar browser mendownloadnya sebagai file (bukan menampilkannya)
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        // 4. Proses merakit data baris per baris
        $callback = function() use($totalPendapatan, $totalPembelian, $labaKotor, $totalBiayaOperasional, $labaBersih, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            // Trik khusus agar tulisan terbaca sempurna di Microsoft Excel (BOM UTF-8)
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); 

            // Header Laporan
            fputcsv($file, array('PT KIMPAI DYNA TUBES'));
            fputcsv($file, array('LAPORAN LABA RUGI'));
            fputcsv($file, array('Periode:', $startDate . ' s/d ' . $endDate));
            fputcsv($file, array('')); // Baris kosong
            fputcsv($file, array('KATEGORI', 'DESKRIPSI', 'NOMINAL (Rp)')); // Judul Kolom

            // PENDAPATAN
            fputcsv($file, array('PENDAPATAN', 'Pendapatan Penjualan Barang', $totalPendapatan));
            fputcsv($file, array('', 'TOTAL PENDAPATAN', $totalPendapatan));
            fputcsv($file, array('')); // Baris kosong

            // BEBAN
            fputcsv($file, array('BEBAN POKOK', 'Pembelian Bahan Baku (Vendor)', $totalPembelian));
            fputcsv($file, array('', 'TOTAL HARGA POKOK PENJUALAN', $totalPembelian));
            fputcsv($file, array('')); 

            // LABA KOTOR
            fputcsv($file, array('LABA KOTOR', 'GROSS PROFIT', $labaKotor));
            fputcsv($file, array('')); 

            // BIAYA OPERASIONAL
            fputcsv($file, array('OPEX', 'Total Biaya Operasional', $totalBiayaOperasional));
            fputcsv($file, array('')); 

            // LABA BERSIH
            fputcsv($file, array('LABA BERSIH', 'NET PROFIT', $labaBersih));

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function generalLedger(\Illuminate\Http\Request $request)
    {
        // 1. Ambil daftar COA untuk dropdown filter
        $coas = \Illuminate\Support\Facades\DB::table('coas')->orderBy('kode_akun', 'asc')->get();
        
        // 2. Tangkap parameter filter (Default: Awal bulan s/d Akhir bulan ini)
        $coa_id = $request->input('coa_id');
        $start_date = $request->input('start_date', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'));
        $end_date = $request->input('end_date', \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $mutasi = collect();
        $selectedCoa = null;
        $saldoAwal = 0;

        if ($coa_id) {
            $selectedCoa = \Illuminate\Support\Facades\DB::table('coas')->where('id', $coa_id)->first();
            
            // 3. Hitung Saldo Awal (Mutasi sebelum start_date)
            $sebelumStart = \Illuminate\Support\Facades\DB::table('journal_details')
                ->join('journals', 'journal_details.journal_id', '=', 'journals.id')
                ->where('journal_details.coa_id', $coa_id)
                ->where('journals.tanggal', '<', $start_date)
                ->select(\Illuminate\Support\Facades\DB::raw('SUM(debit) as total_debit, SUM(kredit) as total_kredit'))
                ->first();
            
            $totalDebitAwal = $sebelumStart->total_debit ?? 0;
            $totalKreditAwal = $sebelumStart->total_kredit ?? 0;
            
            // Logika Akuntansi: Harta & Beban bertambah di Debit. Sisanya bertambah di Kredit.
            if ($selectedCoa->saldo_normal == 'Debit') {
                $saldoAwal = $totalDebitAwal - $totalKreditAwal;
            } else {
                $saldoAwal = $totalKreditAwal - $totalDebitAwal;
            }

            // 4. Tarik mutasi pada rentang waktu yang difilter
            $mutasi = \Illuminate\Support\Facades\DB::table('journal_details')
                ->join('journals', 'journal_details.journal_id', '=', 'journals.id')
                ->where('journal_details.coa_id', $coa_id)
                ->whereBetween('journals.tanggal', [$start_date, $end_date])
                ->orderBy('journals.tanggal', 'asc')
                ->orderBy('journals.id', 'asc')
                ->select('journals.tanggal', 'journals.nomor_jurnal', 'journals.keterangan', 'journal_details.debit', 'journal_details.kredit')
                ->get();
        }

        return view('fat.reports.general_ledger', compact('coas', 'coa_id', 'start_date', 'end_date', 'mutasi', 'selectedCoa', 'saldoAwal'));
    }
    public function balanceSheet(\Illuminate\Http\Request $request)
    {
        // Neraca ditarik berdasarkan posisi "Per Tanggal" tertentu
        $endDate = $request->input('end_date', \Carbon\Carbon::now()->format('Y-m-d'));

        // Tarik semua akumulasi saldo jurnal dari awal perusahaan berdiri sampai tanggal filter
        $balances = \Illuminate\Support\Facades\DB::table('journal_details')
            ->join('journals', 'journal_details.journal_id', '=', 'journals.id')
            ->join('coas', 'journal_details.coa_id', '=', 'coas.id')
            ->where('journals.tanggal', '<=', $endDate)
            ->select('coas.kategori', 'coas.kode_akun', 'coas.nama_akun', 'coas.saldo_normal',
                \Illuminate\Support\Facades\DB::raw('SUM(journal_details.debit) as total_debit'),
                \Illuminate\Support\Facades\DB::raw('SUM(journal_details.kredit) as total_kredit')
            )
            ->groupBy('coas.id', 'coas.kategori', 'coas.kode_akun', 'coas.nama_akun', 'coas.saldo_normal')
            ->orderBy('coas.kode_akun', 'asc')
            ->get();

        $harta = [];
        $kewajiban = [];
        $modal = [];
        $totalPendapatan = 0;
        $totalBeban = 0;

        $totalHarta = 0;
        $totalKewajiban = 0;
        $totalModal = 0;

        foreach ($balances as $b) {
            // Hitung nilai akhir berdasarkan Saldo Normal
            $saldo = ($b->saldo_normal == 'Debit') 
                ? ($b->total_debit - $b->total_kredit) 
                : ($b->total_kredit - $b->total_debit);

            if ($saldo != 0) {
                if ($b->kategori == 'Harta') {
                    $harta[] = (object) ['kode' => $b->kode_akun, 'nama' => $b->nama_akun, 'saldo' => $saldo];
                    $totalHarta += $saldo;
                } elseif ($b->kategori == 'Kewajiban') {
                    $kewajiban[] = (object) ['kode' => $b->kode_akun, 'nama' => $b->nama_akun, 'saldo' => $saldo];
                    $totalKewajiban += $saldo;
                } elseif ($b->kategori == 'Modal') {
                    $modal[] = (object) ['kode' => $b->kode_akun, 'nama' => $b->nama_akun, 'saldo' => $saldo];
                    $totalModal += $saldo;
                } elseif ($b->kategori == 'Pendapatan') {
                    $totalPendapatan += $saldo;
                } elseif ($b->kategori == 'Beban') {
                    $totalBeban += $saldo;
                }
            }
        }

        // Hitung Laba Berjalan (Pendapatan - Beban) lalu masukkan ke kelompok Modal
        $labaBerjalan = $totalPendapatan - $totalBeban;
        $modal[] = (object) ['kode' => '3999', 'nama' => 'Laba Berjalan (Tahun Ini)', 'saldo' => $labaBerjalan];
        $totalModal += $labaBerjalan;

        $totalPasiva = $totalKewajiban + $totalModal;

        return view('fat.reports.balance_sheet', compact(
            'endDate', 'harta', 'kewajiban', 'modal', 
            'totalHarta', 'totalKewajiban', 'totalModal', 'totalPasiva'
        ));
    }
    
}