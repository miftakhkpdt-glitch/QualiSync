@extends('layouts.staff-layout')

@push('styles')
<style>
    /* CSS Khusus Form CAPA */
    .card-form { 
        background: #ffffff; 
        border-radius: 10px; 
        padding: 25px; 
        box-shadow: 0 4px 6px rgba(0,0,0,0.04); 
        max-width: 800px; 
        border: 1px solid #e2e8f0;
    }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-weight: 600; color: #334155; margin-bottom: 8px; font-size: 14px; }
    .form-control { 
        width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; 
        border-radius: 6px; font-size: 14px; box-sizing: border-box; 
        transition: all 0.2s; background-color: #fff;
    }
    .form-control:focus { border-color: #198754; outline: none; box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.15); }
    .btn-simpan { background-color: #198754; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 14px; transition: background 0.2s; }
    .btn-simpan:hover { background-color: #157347; }
    .btn-kembali { background-color: #64748b; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px; display: inline-block; margin-right: 10px; transition: background 0.2s; }
    .btn-kembali:hover { background-color: #475569; }
</style>
@endpush

@section('konten')

    <h2 style="margin-top: 0; margin-bottom: 20px; font-size: 24px; color: #1e293b;">
        <i class="fas fa-file-signature" style="color: #198754; margin-right: 8px;"></i> Form Buat CAPA Supplier Baru
    </h2>

    <div class="card-form">
        <form action="/capa-8d/supplier/store" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group">
                <label for="tema_masalah">Tema Masalah (Problem Theme)</label>
                <input type="text" id="tema_masalah" name="tema_masalah" class="form-control" placeholder="Contoh: Defek Seaming Pipa Bocor" required>
            </div>

            <div class="form-group">
                <label for="nama_supplier">Nama Supplier</label>
                <select id="nama_supplier" name="nama_supplier" class="form-control" required>
                    <option value="">-- Pilih Nama Supplier --</option>
                    <option value="PT Best Label">PT Best Label</option>
                    <option value="PT Berkah Sentosa">PT Berkah Sentosa</option>
                    <option value="PT Mitra Sejati Teknik">PT Mitra Sejati Teknik</option>
                    <!-- Tambahkan daftar supplier lainnya di sini sesuai kebutuhan -->
                </select>
            </div>

            <div class="form-group">
                <label for="tanggal_temuan">Tanggal Temuan / Komplain</label>
                <input type="date" id="tanggal_temuan" name="tanggal_temuan" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="deskripsi_masalah">Deskripsi Masalah & Fenomena</label>
                <textarea id="deskripsi_masalah" name="deskripsi_masalah" class="form-control" rows="4" placeholder="Jelaskan detail masalah atau temuan di line..." required></textarea>
            </div>

            <!-- Input Upload Foto -->
            <div class="form-group">
                <label for="foto_masalah">Upload Foto Barang NG (Problem Picture)</label>
                <input type="file" id="foto_masalah" name="foto_masalah" class="form-control" accept="image/*">
                <small style="color: #64748b; font-size: 12px; margin-top: 5px; display: block;">Format: JPG, PNG, JPEG (Maks. 2MB)</small>
            </div>

            <div style="margin-top: 30px;">
                <a href="{{ url('/capa-8d/supplier') }}" class="btn-kembali">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn-simpan">
                    <i class="fas fa-save"></i> Simpan Data CAPA
                </button>
            </div>
        </form>
    </div>

@endsection