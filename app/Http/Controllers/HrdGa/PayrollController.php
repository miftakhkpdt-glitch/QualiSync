<?php

namespace App\Http\Controllers\HrdGa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index()
    {
        // Tarik data slip gaji beserta nama karyawannya
        $payrolls = DB::table('payrolls')
            ->join('users', 'payrolls.user_id', '=', 'users.id')
            ->select('payrolls.*', 'users.name as nama_karyawan', 'users.role')
            ->orderBy('payrolls.created_at', 'desc')
            ->get();

        // Tarik daftar karyawan untuk pilihan di form pembuatan slip gaji
        $karyawan = DB::table('users')->get();

        // Pastikan folder view-nya disesuaikan. Di sini saya asumsikan resources/views/hrd/payrolls/index.blade.php
        return view('hrd-ga.payrolls.index', compact('payrolls', 'karyawan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'periode' => 'required',
            'gaji_pokok' => 'required|numeric'
        ]);

        $gaji_pokok = $request->gaji_pokok;
        $tunjangan = $request->tunjangan ?? 0;
        $uang_lembur = $request->uang_lembur ?? 0;
        $potongan = $request->potongan ?? 0;
        
        $gaji_bersih = ($gaji_pokok + $tunjangan + $uang_lembur) - $potongan;

        DB::table('payrolls')->insert([
            'user_id'     => $request->user_id,
            'periode'     => $request->periode,
            'gaji_pokok'  => $gaji_pokok,
            'tunjangan'   => $tunjangan,
            'uang_lembur' => $uang_lembur,
            'potongan'    => $potongan,
            'gaji_bersih' => $gaji_bersih,
            'status'      => 'Pending',
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now()
        ]);

        return redirect()->back()->with('success', 'Draft Slip Gaji berhasil dibuat!');
    }

    public function pay($id)
    {
        $payroll = DB::table('payrolls')->where('id', $id)->first();

        if ($payroll->status == 'Dibayar') {
            return redirect()->back()->with('error', 'Gaji ini sudah berstatus Dibayar!');
        }

        // 1. HANYA Ubah status Slip Gaji menjadi "Dibayar" (TIDAK ADA JURNAL FAT DI SINI)
        DB::table('payrolls')->where('id', $id)->update([
            'status'          => 'Dibayar',
            'tanggal_dibayar' => \Carbon\Carbon::now()
        ]);

        return redirect()->back()->with('success', 'Gaji berhasil dibayar! (Data tersimpan di HRD, belum masuk ke FAT)');
    }

    // ========================================================
    // FUNGSI BARU: POSTING REKAPITULASI KE FAT SECARA ANONIM
    // ========================================================
    public function postRekap(Request $request)
    {
        $periode = $request->periode;

        // 1. Hitung TOTAL gaji bersih yang sudah DIBAYAR pada periode tersebut
        $totalGaji = DB::table('payrolls')
            ->where('periode', $periode)
            ->where('status', 'Dibayar')
            ->sum('gaji_bersih');

        if ($totalGaji == 0) {
            return redirect()->back()->with('error', 'Gagal! Tidak ada gaji berstatus "Dibayar" untuk periode ' . $periode . '.');
        }

        // 2. Cek apakah periode ini sudah pernah di-posting ke FAT (Mencegah Double Entry)
        $keterangan = 'Rekapitulasi Gaji Karyawan (' . $periode . ')';
        $cekJurnal = DB::table('journals')->where('keterangan', $keterangan)->exists();

        if ($cekJurnal) {
            return redirect()->back()->with('error', 'Gagal! Rekapitulasi gaji untuk periode ' . $periode . ' sudah pernah dikirim ke FAT.');
        }

        // 3. Cari Akun COA "Beban Gaji" & "Kas" di database
        $coaBebanGaji = DB::table('coas')->where('kategori', 'Beban')->where('nama_akun', 'LIKE', '%Gaji%')->first() 
                        ?? DB::table('coas')->where('kategori', 'Beban')->first();

        $coaKas = DB::table('coas')->where('kategori', 'Harta')->where('nama_akun', 'LIKE', '%Kas%')->first() 
                  ?? DB::table('coas')->where('kategori', 'Harta')->first();

        if (!$coaBebanGaji || !$coaKas) {
            return redirect()->back()->with('error', 'Master COA untuk Beban/Kas belum tersedia di FAT.');
        }

        // 4. Buat 1 Baris Header Jurnal Umum FAT
        $journal_id = DB::table('journals')->insertGetId([
            'nomor_jurnal' => 'PAY-REKAP-' . date('Ymd') . '-' . rand(100, 999),
            'tanggal'      => \Carbon\Carbon::now()->format('Y-m-d'),
            'keterangan'   => $keterangan,
            'created_at'   => \Carbon\Carbon::now(),
            'updated_at'   => \Carbon\Carbon::now()
        ]);

        // 5. Buat Rincian Mutasi (Buku Besar)
        DB::table('journal_details')->insert([ // DEBIT: Beban Gaji
            'journal_id' => $journal_id, 
            'coa_id' => $coaBebanGaji->id,
            'debit' => $totalGaji, 
            'kredit' => 0
        ]);

        DB::table('journal_details')->insert([ // KREDIT: Kas/Bank
            'journal_id' => $journal_id, 
            'coa_id' => $coaKas->id,
            'debit' => 0, 
            'kredit' => $totalGaji
        ]);

        return redirect()->back()->with('success', 'Rekap gaji periode ' . $periode . ' senilai Rp ' . number_format($totalGaji, 0, ',', '.') . ' berhasil di-posting ke FAT secara rahasia!');
    }
}