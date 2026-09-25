<?php

namespace App\Http\Controllers\Fat;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JournalController extends Controller
{
    public function index()
    {
        // Menarik data jurnal beserta detail dan nama akunnya
        $journals = DB::table('journals')
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($journals as $journal) {
            $journal->details = DB::table('journal_details')
                ->join('coas', 'journal_details.coa_id', '=', 'coas.id')
                ->where('journal_details.journal_id', $journal->id)
                ->select('journal_details.*', 'coas.kode_akun', 'coas.nama_akun')
                ->get();
                
            // Hitung total debit untuk ditampilkan di ringkasan
            $journal->total_debit = $journal->details->sum('debit');
        }

        return view('fat.journals.index', compact('journals'));
    }

    public function create()
    {
        // Kirim data master COA ke form agar user bisa memilih akun
        $coas = DB::table('coas')->orderBy('kode_akun', 'asc')->get();
        return view('fat.journals.create', compact('coas'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input Array
        $request->validate([
            'tanggal'       => 'required|date',
            'keterangan'    => 'required|string|max:255',
            'coa_id'        => 'required|array|min:2', // Minimal harus ada 2 baris (Debit & Kredit)
            'debit'         => 'required|array|min:2',
            'kredit'        => 'required|array|min:2',
        ]);

        // 2. Validasi Balance (Total Debit WAJIB sama dengan Total Kredit)
        $totalDebit = array_sum($request->debit);
        $totalKredit = array_sum($request->kredit);

        if ($totalDebit !== $totalKredit) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan! Total Debit (Rp '.number_format($totalDebit,0,',','.').') tidak balance dengan Total Kredit (Rp '.number_format($totalKredit,0,',','.').').');
        }

        if ($totalDebit == 0) {
            return redirect()->back()->with('error', 'Total jurnal tidak boleh 0.');
        }

        // 3. Generate Nomor Jurnal Otomatis (Contoh: JU-20260828-001)
        $prefix = 'JU-' . Carbon::parse($request->tanggal)->format('Ymd') . '-';
        $lastJournal = DB::table('journals')->where('nomor_jurnal', 'like', $prefix . '%')->orderBy('nomor_jurnal', 'desc')->first();
        
        $urut = 1;
        if ($lastJournal) {
            $lastUrut = (int) substr($lastJournal->nomor_jurnal, -3);
            $urut = $lastUrut + 1;
        }
        $nomorJurnal = $prefix . str_pad($urut, 3, '0', STR_PAD_LEFT);

        // 4. Proses Insert dengan Database Transaction (Mencegah data separuh masuk jika error)
        DB::beginTransaction();
        try {
            // Insert Header
            $journalId = DB::table('journals')->insertGetId([
                'tanggal'      => $request->tanggal,
                'nomor_jurnal' => $nomorJurnal,
                'keterangan'   => $request->keterangan,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now()
            ]);

            // Insert Details (Looping sesuai jumlah baris akun yang diinput)
            for ($i = 0; $i < count($request->coa_id); $i++) {
                // Hanya masukkan baris yang ada nilainya (Debit > 0 ATAU Kredit > 0)
                if ($request->debit[$i] > 0 || $request->kredit[$i] > 0) {
                    DB::table('journal_details')->insert([
                        'journal_id' => $journalId,
                        'coa_id'     => $request->coa_id[$i],
                        'debit'      => $request->debit[$i] ?: 0,
                        'kredit'     => $request->kredit[$i] ?: 0
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('fat.journals.index')->with('success', 'Jurnal Umum berhasil dicatat dan sudah Balance!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}