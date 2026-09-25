<?php

namespace App\Http\Controllers\HrdGa;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Menampilkan daftar semua akun user dan rolenya
    public function index()
    {
        $users = DB::table('users')->orderBy('name', 'asc')->get();
        return view('hrd-ga.users.index', compact('users'));
    }

    // Menampilkan form tambah akun user baru
    public function create()
    {
        $karyawans = DB::table('karyawans')->orderBy('nama_karyawan', 'asc')->get();
        return view('hrd-ga.users.create', compact('karyawans'));
    }

    // Menyimpan akun user baru ke database dengan Pengaman Admin
    public function store(Request $request)
    {
        // Pengamanan: Cegah HRD membuat akun ber-role admin
        if ($request->input('role') === 'admin' && Auth::user()->email !== 'miftakhkpdt@gmail.com') {
            return back()->withErrors(['role' => 'Peringatan! Hanya Admin Utama yang berhak mendaftarkan akun Administrator.'])->withInput();
        }

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6',
            'role'          => 'required|string',
            'departemen'    => 'required|string',
            'level_jabatan' => 'required|string',
        ]);

        DB::table('users')->insert([
            'name'          => $request->input('name'),
            'email'         => $request->input('email'),
            'password'      => Hash::make($request->input('password')),
            'role'          => $request->input('role'),
            'departemen'    => $request->input('departemen'),
            'level_jabatan' => $request->input('level_jabatan'),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return redirect('/hrd-ga/users')->with('success', 'Akun pengguna baru berhasil ditambahkan!');
    }

    // Menampilkan form edit akun user
    public function edit($id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        
        if (!$user) {
            return redirect('/hrd-ga/users')->with('error', 'Data pengguna tidak ditemukan!');
        }

        return view('hrd-ga.users.edit', compact('user'));
    }

    // Memproses Perubahan Data User
    public function update(Request $request, $id)
    {
        // Pengamanan: Cegah HRD mengubah akun menjadi role admin
        if ($request->input('role') === 'admin' && Auth::user()->email !== 'miftakhkpdt@gmail.com') {
            return back()->withErrors(['role' => 'Anda tidak memiliki hak akses untuk mengubah akun menjadi Administrator.'])->withInput();
        }

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $id,
            'role'          => 'required|string',
            'departemen'    => 'required|string',
            'level_jabatan' => 'required|string',
        ]);

        $dataUpdate = [
            'name'          => $request->input('name'),
            'email'         => $request->input('email'),
            'role'          => $request->input('role'),
            'departemen'    => $request->input('departemen'),
            'level_jabatan' => $request->input('level_jabatan'),
            'updated_at'    => now(),
        ];

        // Jika form password diisi, maka update passwordnya juga
        if ($request->filled('password')) {
            $dataUpdate['password'] = Hash::make($request->input('password'));
        }

        DB::table('users')->where('id', $id)->update($dataUpdate);

        return redirect('/hrd-ga/users')->with('success', 'Akun pengguna berhasil diperbarui!');
    }

    // Menghapus akun user
    public function delete($id)
    {
        DB::table('users')->where('id', $id)->delete();
        return redirect('/hrd-ga/users')->with('success', 'Akun pengguna berhasil dihapus!');
    }
}