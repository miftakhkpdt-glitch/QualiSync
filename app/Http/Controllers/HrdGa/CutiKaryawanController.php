<?php

namespace App\Http\Controllers\HrdGa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PengajuanCuti; // Memanggil model yang Anda miliki
use App\Models\Karyawan;       // Memanggil model Karyawan

class CutiKaryawanController extends Controller
{
    // Menampilkan Halaman Form & Riwayat Pengajuan Cuti Karyawan
    public function index()
    {
        $user = Auth::user();
        
        $riwayatPengajuan = PengajuanCuti::where('nama_karyawan', $user->name)
                            ->orderBy('id', 'desc')
                            ->get();

        return view('karyawan.pengajuan-cuti', compact('riwayatPengajuan'));
    }

    // Menyimpan Data Pengajuan Cuti Baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_karyawan'   => 'required',
            'jumlah_hari'     => 'required|integer|min:1',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date',
            'alasan'          => 'required',
        ]);

        $user = Auth::user();
        $karyawan = Karyawan::where('nama_karyawan', $request->nama_karyawan)->first();

        $statusDeptAwal = 'Pending';
        if ($user && in_array($user->role, ['staff', 'admin', 'hrd_ga'])) {
            $statusDeptAwal = 'Disetujui';
        }

        PengajuanCuti::create([
            'karyawan_id'     => $karyawan ? $karyawan->id : null,
            'nama_karyawan'   => $request->nama_karyawan,
            'jumlah_hari'     => $request->jumlah_hari,
            'departemen'      => $request->departemen ?? ($user->departemen ?? '-'),
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan'          => $request->alasan,
            'status_dept'     => $statusDeptAwal,
            'status_hrd'      => 'Pending',
        ]);

        return redirect('/pengajuan-cuti')->with('success', 'Pengajuan cuti berhasil dikirim!');
    }

    // Menampilkan Form Edit Cuti
    public function edit($id)
    {
        $cuti = PengajuanCuti::findOrFail($id);
        return view('karyawan.edit-cuti', compact('cuti'));
    }

    // Memperbarui Data Cuti
    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah_hari'     => 'required|integer|min:1',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date',
            'alasan'          => 'required',
        ]);

        $cuti = PengajuanCuti::findOrFail($id);
        $cuti->update($request->only(['jumlah_hari', 'tanggal_mulai', 'tanggal_selesai', 'alasan']));

        return redirect('/pengajuan-cuti')->with('success', 'Pengajuan cuti berhasil diperbarui!');
    }

    // Menghapus Data Cuti
    public function destroy($id)
    {
        $cuti = PengajuanCuti::findOrFail($id);
        $cuti->delete();

        return redirect('/pengajuan-cuti')->with('success', 'Pengajuan cuti berhasil dihapus!');
    }

    // ==========================================
    // FITUR APPROVAL CUTI
    // ==========================================

    // Menampilkan Halaman Approval HRD
    public function approvalIndex()
    {
        $daftarCuti = PengajuanCuti::with('karyawan')->orderBy('id', 'desc')->get();
        return view('hrd-ga.persetujuan-cuti', compact('daftarCuti'));
    }

    // Approval Level Departemen
    public function updateStatusDept(Request $request, $id)
    {
        $cuti = PengajuanCuti::findOrFail($id);
        $cuti->update(['status_dept' => $request->status]);

        return redirect()->back()->with('success', 'Status departemen diperbarui!');
    }

    // Approval Final HRD & Potong Kuota Cuti
    public function updateStatusHrd(Request $request, $id)
    {
        $cuti = PengajuanCuti::with('karyawan.riwayatCuti')->findOrFail($id);
        $statusHrd = $request->status_hrd;

        if ($statusHrd === 'Disetujui' && $cuti->status_hrd !== 'Disetujui') {
            
            // Logika pemotongan otomatis yang bersih
            if ($cuti->karyawan && $cuti->karyawan->riwayatCuti) {
                $riwayat = $cuti->karyawan->riwayatCuti;
                $sisaBaru = max(0, $riwayat->sisa_cuti - $cuti->jumlah_hari);
                $riwayat->update(['sisa_cuti' => $sisaBaru]);
            }
        }

        $cuti->update(['status_hrd' => $statusHrd]);

        return redirect()->back()->with('success', 'Persetujuan HRD berhasil diperbarui!');
    }
}