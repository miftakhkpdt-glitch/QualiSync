<?php

namespace App\Http\Controllers\HrdGa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class HrdGaController extends Controller
{
    // 1. Menampilkan Halaman Data Karyawan + Hitung Otomatis Akrual Bulanan (Max 18 Hari)
    public function indexKaryawan()
    {
        $karyawans = DB::table('karyawans')
            ->leftJoin('riwayat_cuti', 'karyawans.id', '=', 'riwayat_cuti.karyawan_id')
            ->select('karyawans.*', 'riwayat_cuti.id as riwayat_id', 'riwayat_cuti.sisa_cuti', 'riwayat_cuti.updated_at as cuti_updated_at')
            ->orderBy('karyawans.nama_karyawan', 'asc')
            ->get();

        // Logika Akrual Bulanan Otomatis (+1 setiap bulan, max 18)
        foreach ($karyawans as $karyawan) {
            if ($karyawan->riwayat_id) {
                $lastUpdated = Carbon::parse($karyawan->cuti_updated_at ?? now());
                $now = Carbon::now();

                // Hitung berapa bulan yang terlewat sejak update terakhir
                $bulanTerlewat = $lastUpdated->diffInMonths($now);

                if ($bulanTerlewat >= 1) {
                    // Tambah 1 hari per bulan yang terlewat, tapi dibatasi maksimal 18 hari
                    $sisaBaru = min(18, $karyawan->sisa_cuti + $bulanTerlewat);

                    // Update ke database jika ada penambahan
                    if ($sisaBaru != $karyawan->sisa_cuti) {
                        DB::table('riwayat_cuti')->where('id', $karyawan->riwayat_id)->update([
                            'sisa_cuti' => $sisaBaru,
                            'updated_at' => $now,
                        ]);
                        $karyawan->sisa_cuti = $sisaBaru; 
                    }
                }
            }
        }
            
        return view('hrd-ga.karyawan', compact('karyawans'));
    }

    // 2. Menampilkan Form Tambah Karyawan
    public function createKaryawan()
    {
        return view('hrd-ga.tambah-karyawan');
    }

    // 3. Menyimpan Data Karyawan Baru + Email + Upload KTP/KK + Sisa Cuti Awal
    public function storeKaryawan(Request $request)
    {
        $request->validate([
            'nik'             => 'required|unique:karyawans,nik',
            'nama_karyawan'   => 'required',
            'email'           => 'required|email|unique:karyawans,email',
            'departemen'      => 'required',
            'jabatan'         => 'required',
            'tanggal_masuk'   => 'required|date',
            'status_karyawan' => 'required',
            'sisa_cuti'       => 'required|integer|max:18',
            'ktp'             => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'kk'              => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $ktpPath = null;
        if ($request->hasFile('ktp')) {
            $ktpPath = $request->file('ktp')->store('lampiran-ktp', 'public');
        }

        $kkPath = null;
        if ($request->hasFile('kk')) {
            $kkPath = $request->file('kk')->store('lampiran-kk', 'public');
        }

        // Simpan Data Karyawan dan Ambil ID-nya
        $karyawanId = DB::table('karyawans')->insertGetId([
            'nik'             => $request->input('nik'),
            'nama_karyawan'   => $request->input('nama_karyawan'),
            'email'           => $request->input('email'),
            'departemen'      => $request->input('departemen'),
            'jabatan'         => $request->input('jabatan'),
            'tanggal_masuk'   => $request->input('tanggal_masuk'),
            'status_karyawan' => $request->input('status_karyawan'),
            'ktp'             => $ktpPath,
            'kk'              => $kkPath,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // Ambil nilai sisa cuti dari form (maksimal 18)
        $sisaCutiAwal = min(18, $request->input('sisa_cuti'));
        $tanggalSekarang = date('Y-m-d');

        // Masukkan ke riwayat cuti (disesuaikan agar tidak error database)
        DB::table('riwayat_cuti')->insert([
            'karyawan_id'     => $karyawanId,
            'kuota_diberikan' => $sisaCutiAwal,
            'sisa_cuti'       => $sisaCutiAwal,
            'tanggal_dapat'   => $tanggalSekarang,
            'status'          => 'Aktif',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return redirect('/hrd-ga/karyawan')->with('success', 'Data Karyawan & Sisa Cuti Berhasil Ditambahkan!');
    }

    // 4. Menampilkan Form Edit Karyawan
    public function editKaryawan($id)
    {
        // Ambil data karyawan beserta sisa cutinya dari tabel riwayat_cuti
        $karyawan = DB::table('karyawans')
            ->leftJoin('riwayat_cuti', 'karyawans.id', '=', 'riwayat_cuti.karyawan_id')
            ->select('karyawans.*', 'riwayat_cuti.sisa_cuti')
            ->where('karyawans.id', $id)
            ->first();

        if (!$karyawan) {
            return redirect('/hrd-ga/karyawan')->with('error', 'Data karyawan tidak ditemukan!');
        }

        return view('hrd-ga.edit-karyawan', compact('karyawan'));
    }

    // 5. Memperbarui Data Karyawan
    public function updateKaryawan(Request $request, $id)
    {
        $karyawan = DB::table('karyawans')->where('id', $id)->first();

        $request->validate([
            'nik'             => 'required|unique:karyawans,nik,' . $id,
            'nama_karyawan'   => 'required',
            'email'           => 'required|email|unique:karyawans,email,' . $id,
            'departemen'      => 'required',
            'jabatan'         => 'required',
            'tanggal_masuk'   => 'required|date',
            'status_karyawan' => 'required',
            'sisa_cuti'       => 'required|integer|max:18', // Validasi input sisa cuti saat edit
            'ktp'             => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'kk'              => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $ktpPath = $karyawan->ktp;
        if ($request->hasFile('ktp')) {
            if ($karyawan->ktp) {
                Storage::disk('public')->delete($karyawan->ktp);
            }
            $ktpPath = $request->file('ktp')->store('lampiran-ktp', 'public');
        }

        $kkPath = $karyawan->kk;
        if ($request->hasFile('kk')) {
            if ($karyawan->kk) {
                Storage::disk('public')->delete($karyawan->kk);
            }
            $kkPath = $request->file('kk')->store('lampiran-kk', 'public');
        }

        // Update data utama karyawan
        DB::table('karyawans')->where('id', $id)->update([
            'nik'             => $request->input('nik'),
            'nama_karyawan'   => $request->input('nama_karyawan'),
            'email'           => $request->input('email'),
            'departemen'      => $request->input('departemen'),
            'jabatan'         => $request->input('jabatan'),
            'tanggal_masuk'   => $request->input('tanggal_masuk'),
            'status_karyawan' => $request->input('status_karyawan'),
            'ktp'             => $ktpPath,
            'kk'              => $kkPath,
            'updated_at'      => now(),
        ]);

        // Update atau masukkan data sisa cuti ke tabel riwayat_cuti
        DB::table('riwayat_cuti')->updateOrInsert(
            ['karyawan_id' => $id],
            [
                'sisa_cuti' => min(18, $request->input('sisa_cuti')),
                'updated_at' => now()
            ]
        );

        return redirect('/hrd-ga/karyawan')->with('success', 'Data Karyawan & Sisa Cuti Berhasil Diperbarui!');
    }

    // 6. Menghapus Data Karyawan
    public function deleteKaryawan($id)
    {
        $karyawan = DB::table('karyawans')->where('id', $id)->first();
        if ($karyawan) {
            if ($karyawan->ktp) {
                Storage::disk('public')->delete($karyawan->ktp);
            }
            if ($karyawan->kk) {
                Storage::disk('public')->delete($karyawan->kk);
            }
            DB::table('riwayat_cuti')->where('karyawan_id', $id)->delete();
            DB::table('karyawans')->where('id', $id)->delete();
        }

        return redirect('/hrd-ga/karyawan')->with('success', 'Data Karyawan Berhasil Dihapus!');
    }
}