<?php

namespace App\Http\Controllers\HrdGa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LemburKaryawan;
use Illuminate\Support\Facades\Auth;

class LemburKaryawanController extends Controller
{
    // Tampilan daftar lembur pribadi karyawan
    public function index()
    {
        $lemburList = LemburKaryawan::where('user_id', Auth::id())->latest()->get();
        return view('hrd-ga.lembur.pribadi', compact('lemburList'));
    }

    // Simpan pengajuan lembur baru
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_lembur' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'keterangan_pekerjaan' => 'required',
        ]);

        LemburKaryawan::create([
            'user_id' => Auth::id(),
            'tanggal_lembur' => $request->tanggal_lembur,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keterangan_pekerjaan' => $request->keterangan_pekerjaan,
            'status_dept' => 'Pending',
            'status_hrd' => 'Pending',
        ]);

        return redirect()->back()->with('success', 'Pengajuan lembur berhasil dikirim!');
    }

    // Hapus pengajuan lembur
    public function destroy($id)
    {
        $lembur = LemburKaryawan::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $lembur->delete();

        return redirect()->back()->with('success', 'Pengajuan lembur berhasil dibatalkan.');
    }

    // 1. Tampilan Halaman Approval Khusus Kepala Departemen
    public function approvalIndex()
    {
        $userLogin = Auth::user();

        if ($userLogin->role == 'admin' || $userLogin->role == 'hrd_ga') {
            $lemburList = LemburKaryawan::with('user')->latest()->get();
        } else {
            $lemburList = LemburKaryawan::whereHas('user', function ($query) use ($userLogin) {
                $query->where('role', $userLogin->role); 
            })->latest()->get();
        }

        return view('hrd-ga.lembur.approval', compact('lemburList'));
    }

    // 2. Tampilan Halaman Approval Khusus HRD & GA (Hanya meloloskan data yang sudah Approved dari Dept)
    public function hrdApprovalIndex()
    {
        $lemburList = LemburKaryawan::with('user')->where('status_dept', 'Approved')->latest()->get();
        
        return view('hrd-ga.lembur.hrd-approval', compact('lemburList'));
    }

    // Update Status Approval oleh Kepala Departemen
    public function updateStatusDept(Request $request, $id)
    {
        $lembur = LemburKaryawan::findOrFail($id);
        $lembur->status_dept = $request->status; // Approved / Rejected
        $lembur->save();

        return redirect()->back()->with('success', 'Status approval departemen berhasil diperbarui!');
    }

    // Update Status Approval Final oleh HRD
    public function updateStatusHrd(Request $request, $id)
    {
        $lembur = LemburKaryawan::findOrFail($id);
        $lembur->status_hrd = $request->status; // Approved / Rejected
        $lembur->save();

        return redirect()->back()->with('success', 'Status approval final HRD berhasil diperbarui!');
    }
}