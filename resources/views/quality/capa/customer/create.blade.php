@extends('layouts.staff-layout')

@push('styles')
<style>
    .card-form { background: #ffffff; border-radius: 10px; padding: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.04); max-width: 800px; border: 1px solid #e2e8f0; margin: 0 auto; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-weight: 600; color: #334155; margin-bottom: 8px; font-size: 14px; }
    .form-control { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; box-sizing: border-box; }
    .form-control:focus { border-color: #a0522d; outline: none; box-shadow: 0 0 0 3px rgba(160, 82, 45, 0.15); }
    
    .btn-simpan { background-color: #a0522d; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 14px; }
    .btn-simpan:hover { background-color: #8b4513; }
    .btn-kembali { background-color: #64748b; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px; display: inline-block; margin-right: 10px; }
    .btn-kembali:hover { background-color: #475569; }
</style>
@endpush

@section('konten')
    <h2 style="margin-top: 0; margin-bottom: 20px; font-size: 24px; color: #1e293b;">
        <i class="fas fa-plus-circle" style="color: #a0522d; margin-right: 8px;"></i> Tambah CAPA Customer
    </h2>

    <div class="card-form">
        <form action="{{ url('/capa-8d/customer/store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label>Complaint Month</label>
                <input type="text" name="complaint_month" class="form-control" placeholder="Contoh: August / 2026" required>
            </div>

            <div class="form-group">
                <label>Customer</label>
                <input type="text" name="customer" class="form-control" placeholder="Nama Customer" required>
            </div>

            <div class="form-group">
                <label>No. Capa Customer</label>
                <input type="text" name="no_capa_customer" class="form-control" placeholder="Nomor CAPA dari Customer" required>
            </div>

            <div class="form-group">
                <label>MM</label>
                <input type="text" name="mm" class="form-control" placeholder="Month/Model">
            </div>

            <div class="form-group">
                <label>Item</label>
                <input type="text" name="item" class="form-control" placeholder="Nama Item / Produk" required>
            </div>

            <div class="form-group">
                <label>Defect</label>
                <input type="text" name="defect" class="form-control" placeholder="Jenis Kerusakan">
            </div>

            <div class="form-group">
                <label>Source</label>
                <input type="text" name="source" class="form-control" placeholder="Sumber Temuan">
            </div>

            <div class="form-group">
                <label>Category Defect</label>
                <input type="text" name="category_defect" class="form-control" placeholder="Kategori Defect">
            </div>

            <div class="form-group">
                <label>SNCR (NC / NON NC)</label>
                <select name="sncr" class="form-control">
                    <option value="NC">NC</option>
                    <option value="NON NC">NON NC</option>
                </select>
            </div>

            <div style="margin-top: 30px;">
                <a href="{{ url('/capa-8d/customer') }}" class="btn-kembali">
                    <i class="fas fa-arrow-left"></i> Batal
                </a>
                <button type="submit" class="btn-simpan">
                    <i class="fas fa-save"></i> Simpan Data
                </button>
            </div>
        </form>
    </div>
@endsection