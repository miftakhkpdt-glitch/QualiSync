@extends('layouts.staff-layout')

@section('title', 'Edit Akun Pengguna - PT KIMPAI DYNA TUBE')

@push('styles')
<style>
    .card-form { 
        background: #fff; 
        padding: 30px; 
        border-radius: 16px; 
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); 
        max-width: 800px; 
        border: 1px solid #f3f4f6;
    }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-weight: 600; font-size: 13px; color: var(--text-main); margin-bottom: 8px; }
    .form-control { width: 100%; padding: 12px 15px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: 0.2s; background-color: #fff; }
    .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(212, 163, 42, 0.15); }
    .form-text { font-size: 12px; color: var(--text-muted); margin-top: 5px; display: block; }
    
    .btn-simpan { background-color: #d4a32a; color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 14px; transition: 0.2s; }
    .btn-simpan:hover { background-color: #b5891f; }
    .btn-batal { background-color: #64748b; color: white; padding: 12px 20px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; display: inline-block; transition: 0.2s; }
    .btn-batal:hover { background-color: #475569; }

    /* Alert Error */
    .alert-danger { background-color: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f87171; }
    .alert-danger ul { margin: 0; padding-left: 20px; font-size: 13px; }
</style>
@endpush

@section('konten')
    
    <div style="margin-bottom: 25px;">
        <h1 style="font-size: 24px; color: var(--text-main); margin-bottom: 5px;">Edit Akun Pengguna</h1>
        <p style="color: var(--text-muted); font-size: 14px; margin: 0;">Perbarui data login, hak akses dashboard, dan departemen karyawan.</p>
    </div>

    <div class="card-form">
        <!-- Menampilkan pesan error jika validasi gagal -->
        @if ($errors->any())
            <div class="alert-danger">
                <strong>Gagal Memperbarui! Terdapat kesalahan:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ url('/hrd-ga/users/update/' . $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Nama Pengguna <span style="color: red;">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" autocomplete="off" required>
            </div>

            <div class="form-group">
                <label>Email Login <span style="color: red;">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" autocomplete="off" required>
            </div>

            <!-- Kolom 1: Role / Hak Akses Dashboard -->
            <div class="form-group">
                <label>Role (Hak Akses Tampilan Dashboard) <span style="color: red;">*</span></label>
                <select name="role" class="form-control" required>
                    <option value="supplier" {{ old('role', $user->role) == 'supplier' ? 'selected' : '' }}>Supplier</option>
                    <option value="operator" {{ old('role', $user->role) == 'operator' ? 'selected' : '' }}>Operator (Umum)</option>
                    
                    <!-- [BARU] TAMBAHAN ROLE OPERATOR SPESIFIK -->
                    <option value="operator_quality" {{ old('role', $user->role) == 'operator_quality' ? 'selected' : '' }}>Operator Quality</option>
                    <option value="operator_produksi" {{ old('role', $user->role) == 'operator_produksi' ? 'selected' : '' }}>Operator Produksi</option>
                    <option value="operator_warehouse" {{ old('role', $user->role) == 'operator_warehouse' ? 'selected' : '' }}>Operator Warehouse</option>

                    <option value="staff_hrd" {{ old('role', $user->role) == 'staff_hrd' ? 'selected' : '' }}>Staff HRD</option>
                    <option value="staff_ppic_warehouse" {{ old('role', $user->role) == 'staff_ppic_warehouse' ? 'selected' : '' }}>Staff PPIC / Warehouse</option>
                    <option value="staff_development" {{ old('role', $user->role) == 'staff_development' ? 'selected' : '' }}>Staff Development</option>
                    <option value="staff_produksi" {{ old('role', $user->role) == 'staff_produksi' ? 'selected' : '' }}>Staff Produksi</option>
                    <option value="staff_quality" {{ old('role', $user->role) == 'staff_quality' ? 'selected' : '' }}>Staff Quality</option>
                    <option value="staff_engineering" {{ old('role', $user->role) == 'staff_engineering' ? 'selected' : '' }}>Staff Engineering</option>
                    <option value="staff_fat" {{ old('role', $user->role) == 'staff_fat' ? 'selected' : '' }}>Staff FAT</option>
                    <option value="staff_sales_marketing" {{ old('role', $user->role) == 'staff_sales_marketing' ? 'selected' : '' }}>Staff Sales / Marketing</option>
                    <option value="manager_plan" {{ old('role', $user->role) == 'manager_plan' ? 'selected' : '' }}>Manager Plan</option>
                    
                    <!-- TAMBAHAN BARIS ROLE DIREKSI -->
                    <option value="direktur" {{ old('role', $user->role) == 'direktur' ? 'selected' : '' }}>Direktur</option>
                    <option value="presiden_direktur" {{ old('role', $user->role) == 'presiden_direktur' ? 'selected' : '' }}>Presiden Direktur</option>

                    <!-- PENGAMAN: Hanya Admin Utama yang bisa melihat opsi admin -->
                    @if(auth()->check() && auth()->user()->email == 'miftakhkpdt@gmail.com')
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin (Full Akses)</option>
                    @endif
                </select>
                <small class="form-text">Role ini menentukan halaman utama / dashboard yang akan dilihat saat pengguna login.</small>
            </div>

            <!-- Kolom 2: Hak Akses Portal Departemen -->
            <div class="form-group">
                <label>Hak Akses Portal Departemen <span style="color: red;">*</span></label>
                <select name="departemen" class="form-control" required>
                    <option value="Management" {{ old('departemen', $user->departemen ?? '') == 'Management' ? 'selected' : '' }}>Management</option>
                    <option value="Supplier" {{ old('departemen', $user->departemen ?? '') == 'Supplier' ? 'selected' : '' }}>Supplier</option>
                    <option value="HRD-GA" {{ old('departemen', $user->departemen ?? '') == 'HRD-GA' ? 'selected' : '' }}>HRD-GA</option>
                    <option value="PPIC / Warehouse" {{ old('departemen', $user->departemen ?? '') == 'PPIC / Warehouse' ? 'selected' : '' }}>PPIC / Warehouse</option>
                    <option value="Development" {{ old('departemen', $user->departemen ?? '') == 'Development' ? 'selected' : '' }}>Development</option>
                    <option value="Produksi" {{ old('departemen', $user->departemen ?? '') == 'Produksi' ? 'selected' : '' }}>Produksi</option>
                    <option value="Quality" {{ old('departemen', $user->departemen ?? '') == 'Quality' ? 'selected' : '' }}>Quality</option>
                    <option value="Engineering" {{ old('departemen', $user->departemen ?? '') == 'Engineering' ? 'selected' : '' }}>Engineering</option>
                    <option value="FAT" {{ old('departemen', $user->departemen ?? '') == 'FAT' ? 'selected' : '' }}>FAT</option>
                    <option value="Sales / Marketing" {{ old('departemen', $user->departemen ?? '') == 'Sales / Marketing' ? 'selected' : '' }}>Sales / Marketing</option>
                    <option value="Other" {{ old('departemen', $user->departemen ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                <small class="form-text">Menentukan izin akses fitur spesifik berdasarkan unit kerja.</small>
            </div>

            <!-- Level Jabatan -->
            <div class="form-group">
                <label>Level Jabatan</label>
                <select name="level_jabatan" class="form-control">
                    <option value="External" {{ old('level_jabatan', $user->level_jabatan ?? '') == 'External' ? 'selected' : '' }}>External / Partner</option>
                    <option value="Operator" {{ old('level_jabatan', $user->level_jabatan ?? '') == 'Operator' ? 'selected' : '' }}>Operator</option>
                    <option value="Staff" {{ old('level_jabatan', $user->level_jabatan ?? '') == 'Staff' ? 'selected' : '' }}>Staff</option>
                    <option value="Spv / Section Head" {{ old('level_jabatan', $user->level_jabatan ?? '') == 'Spv / Section Head' ? 'selected' : '' }}>Spv / Section Head</option>
                    <option value="Manager" {{ old('level_jabatan', $user->level_jabatan ?? '') == 'Manager' ? 'selected' : '' }}>Manager</option>
                    
                    <!-- TAMBAHAN BARIS LEVEL DIREKSI -->
                    <option value="Direktur" {{ old('level_jabatan', $user->level_jabatan ?? '') == 'Direktur' ? 'selected' : '' }}>Direktur</option>
                    <option value="Presiden Direktur" {{ old('level_jabatan', $user->level_jabatan ?? '') == 'Presiden Direktur' ? 'selected' : '' }}>Presiden Direktur</option>
                </select>
            </div>

            <div class="form-group">
                <label>Password Baru (Opsional)</label>
                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password" autocomplete="new-password">
                <small class="form-text">Isi kolom ini hanya jika Anda ingin mereset password pengguna.</small>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 30px;">
                <button type="submit" class="btn-simpan"><i class="fas fa-save"></i> Perbarui Akun</button>
                <a href="{{ url('/hrd-ga/users') }}" class="btn-batal"><i class="fas fa-arrow-left"></i> Batal</a>
            </div>
        </form>
    </div>

@endsection