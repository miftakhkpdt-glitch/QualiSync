@extends('layouts.staff-layout')

@section('title', 'Tambah Karyawan - PT KIMPAI DYNA TUBE')

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
    .form-control { width: 100%; padding: 12px 15px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: 0.2s; background: #fff; }
    .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(212, 163, 42, 0.15); }
    .form-text { font-size: 12px; color: var(--text-muted); margin-top: 5px; display: block; }
    
    .btn-simpan { background-color: #d4a32a; color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 14px; transition: 0.2s; }
    .btn-simpan:hover { background-color: #b5891f; }
    .btn-batal { background-color: #64748b; color: white; padding: 12px 20px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; display: inline-block; transition: 0.2s; }
    .btn-batal:hover { background-color: #475569; }
    
    /* Style untuk pesan error */
    .alert-danger {
        background-color: #fee2e2;
        color: #b91c1c;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #f87171;
    }
    .alert-danger ul { margin: 0; padding-left: 20px; font-size: 13px; }
</style>
@endpush

@section('konten')
    
    <div style="margin-bottom: 25px;">
        <h1 style="font-size: 24px; color: var(--text-main); margin-bottom: 5px;">Form Tambah Karyawan Baru</h1>
        <p style="color: var(--text-muted); font-size: 14px; margin: 0;">Masukkan data lengkap karyawan baru ke dalam sistem perusahaan.</p>
    </div>

    <div class="card-form">
        <!-- Menampilkan pesan error jika validasi gagal -->
        @if ($errors->any())
            <div class="alert-danger">
                <strong>Gagal Menyimpan! Terdapat kesalahan:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Wajib tambah enctype="multipart/form-data" untuk upload file -->
        <form action="{{ url('/hrd-ga/karyawan/store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label>NIK (Nomor Induk Karyawan) <span style="color: red;">*</span></label>
                <input type="text" name="nik" class="form-control" value="{{ old('nik') }}" placeholder="Contoh: KMP-001" required>
            </div>

            <div class="form-group">
                <label>Nama Lengkap Karyawan <span style="color: red;">*</span></label>
                <input type="text" name="nama_karyawan" class="form-control" value="{{ old('nama_karyawan') }}" placeholder="Masukkan nama lengkap..." required>
            </div>

            <div class="form-group">
                <label>Email Karyawan <span style="color: red;">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Contoh: karyawan@gmail.com" required>
            </div>

            <div class="form-group">
                <label>Departemen <span style="color: red;">*</span></label>
                <input type="text" name="departemen" class="form-control" value="{{ old('departemen') }}" placeholder="Contoh: Quality Control" required>
            </div>

            <div class="form-group">
                <label>Jabatan <span style="color: red;">*</span></label>
                <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan') }}" placeholder="Contoh: QC Inspector" required>
            </div>

            <!-- Tambahan Kolom Sesuai Controller -->
            <div class="form-group">
                <label>Tanggal Masuk <span style="color: red;">*</span></label>
                <input type="date" name="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk') }}" required>
            </div>

            <div class="form-group">
                <label>Status Karyawan <span style="color: red;">*</span></label>
                <select name="status_karyawan" class="form-control" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="Tetap" {{ old('status_karyawan') == 'Tetap' ? 'selected' : '' }}>Tetap</option>
                    <option value="Kontrak" {{ old('status_karyawan') == 'Kontrak' ? 'selected' : '' }}>Kontrak</option>
                    <option value="Magang" {{ old('status_karyawan') == 'Magang' ? 'selected' : '' }}>Magang</option>
                </select>
            </div>

            <div class="form-group">
                <label>Sisa Cuti Awal <span style="color: red;">*</span></label>
                <input type="number" name="sisa_cuti" class="form-control" value="{{ old('sisa_cuti') }}" max="18" min="0" placeholder="Maksimal 18 Hari" required>
            </div>

            <div class="form-group">
                <label>Lampiran KTP (Opsional)</label>
                <input type="file" name="ktp" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                <small class="form-text">Maksimal ukuran 2MB (PDF/JPG/PNG).</small>
            </div>

            <div class="form-group">
                <label>Lampiran KK (Opsional)</label>
                <input type="file" name="kk" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                <small class="form-text">Maksimal ukuran 2MB (PDF/JPG/PNG).</small>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 30px;">
                <button type="submit" class="btn-simpan"><i class="fas fa-save"></i> Simpan Data</button>
                <a href="{{ url('/hrd-ga/karyawan') }}" class="btn-batal"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>

        </form>
    </div>

@endsection