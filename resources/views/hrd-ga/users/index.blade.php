@extends('layouts.staff-layout')

@section('title', 'Manajemen Akun Pengguna - PT KIMPAI DYNA TUBE')

@push('styles')
<style>
    .card-table { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .btn-tambah { background-color: #d4a32a; color: white; padding: 10px 18px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 20px; transition: 0.2s; }
    .btn-tambah:hover { background-color: #b5891f; }
    
    /* Disamakan persis dengan tabel Data Karyawan */
    .tabel-custom { width: 100%; border-collapse: collapse; }
    .tabel-custom th { background-color: #f8fafc; padding: 10px 8px; text-align: left; font-size: 11px; color: #64748b; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; white-space: nowrap; }
    .tabel-custom td { padding: 10px 8px; font-size: 12px; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .tabel-custom tr:hover td { background-color: #f8fafc; }
    
    .badge-role { padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: bold; color: white; text-transform: uppercase; display: inline-block; white-space: nowrap; }
    .bg-admin { background-color: #dc2626; }
    .bg-quality { background-color: #16a34a; }
    .bg-qc { background-color: #0284c7; }
    
    /* Tombol Aksi */
    .btn-hapus { background-color: #fee2e2; color: #ef4444; border: none; padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: 0.2s; white-space: nowrap; }
    .btn-hapus:hover { background-color: #fecaca; }
    
    .btn-edit { background-color: #eab308; color: white; border: none; padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: 0.2s; white-space: nowrap; text-decoration: none; }
    .btn-edit:hover { background-color: #ca8a04; }
</style>
@endpush

@section('konten')
    
    <div style="margin-bottom: 20px;">
        <h1 style="font-size: 24px; color: var(--text-main); margin-bottom: 5px;">Daftar Akun Pengguna & Hak Akses (Role)</h1>
        <p style="color: var(--text-muted); font-size: 14px; margin: 0;">Kelola akun login dan hak akses departemen karyawan.</p>
    </div>

    <a href="{{ url('/hrd-ga/users/tambah') }}" class="btn-tambah"><i class="fas fa-user-plus"></i> Tambah Akun Baru</a>

    <div class="card-table">
        <div style="overflow-x: auto;">
            <table class="tabel-custom">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pengguna</th>
                        <th>Email Login</th>
                        <th>Role / Hak Akses</th>
                        <th>Level Jabatan</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    
                    @forelse ($users ?? [] as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="font-weight: 600;">{{ $user->name ?? '-' }}</td>
                            <td>{{ $user->email ?? '-' }}</td>
                            <td><span class="badge-role bg-quality">{{ $user->role ?? '-' }}</span></td>
                            <!-- [REVISI]: Mengubah $user->jabatan menjadi $user->level_jabatan agar sesuai dengan database -->
                            <td>{{ $user->level_jabatan ?? '-' }}</td>
                            <td style="text-align: center; white-space: nowrap;">
                                @if(strtolower($user->role ?? '') !== 'admin')
                                    <div style="display: flex; gap: 5px; justify-content: center;">
                                        <a href="{{ url('/hrd-ga/users/edit/' . $user->id) }}" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                        <form action="{{ url('/hrd-ga/users/delete/' . $user->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-hapus"><i class="fas fa-trash"></i> Hapus</button>
                                        </form>
                                    </div>
                                @else
                                    <span style="color: var(--text-muted); font-style: italic; font-size: 12px;">Protected</span>
                                @endif
                            </td>
                        </tr>
                    
                    @empty
                        <!-- Data dummy sementara dihapus agar tidak membingungkan saat database kosong -->
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px; color: #64748b;">Belum ada data pengguna yang terdaftar.</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>

@endsection