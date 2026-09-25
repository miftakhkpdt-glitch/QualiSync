@extends('layouts.staff-layout')

@section('konten')
<!-- Custom Style khusus Form Pencarian COA -->
<style>
    .coa-create-wrapper {
        max-width: 680px;
        margin: 30px auto;
        padding: 0 15px;
    }
    .coa-card {
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid #eef2f5;
        overflow: hidden;
    }
    .coa-header {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: #ffffff;
        padding: 22px 28px;
    }
    .coa-header h4 {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .coa-header p {
        margin: 6px 0 0;
        font-size: 0.85rem;
        opacity: 0.85;
    }
    .coa-body {
        padding: 28px;
    }
    .form-group-custom {
        margin-bottom: 22px;
    }
    .form-label-custom {
        display: block;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }
    .form-control-custom {
        width: 100%;
        padding: 11px 14px;
        border: 1.5px solid #d1d3e2;
        border-radius: 6px;
        font-size: 0.92rem;
        color: #495057;
        background-color: #fff;
        transition: all 0.2s ease-in-out;
        box-sizing: border-box;
    }
    .form-control-custom:focus {
        border-color: #4e73df;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.15);
    }
    .form-help-text {
        display: block;
        font-size: 0.8rem;
        color: #858796;
        margin-top: 6px;
        line-height: 1.4;
    }
    .coa-footer {
        background-color: #f8f9fc;
        padding: 18px 28px;
        border-top: 1px solid #eaecf4;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .btn-custom {
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 0.88rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-secondary-custom {
        background-color: #eaecf4;
        color: #5a5c69 !important;
    }
    .btn-secondary-custom:hover {
        background-color: #d1d3e2;
    }
    .btn-primary-custom {
        background-color: #4e73df;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(78, 115, 223, 0.3);
    }
    .btn-primary-custom:hover {
        background-color: #2e59d9;
        transform: translateY(-1px);
    }
    .alert-danger-custom {
        background-color: #f8d7da;
        color: #721c24;
        padding: 12px 18px;
        border-radius: 6px;
        margin-bottom: 20px;
        border-left: 4px solid #e74a3b;
        font-size: 0.9rem;
    }
</style>

<div class="coa-create-wrapper">

    <!-- Alert Notifikasi Error -->
    @if(session('error'))
        <div class="alert-danger-custom">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="coa-card">
        <!-- Card Header -->
        <div class="coa-header">
            <h4><i class="fas fa-file-medical"></i> Buat Dokumen COA Baru</h4>
            <p>Pilih material Finish Good dan masukkan nomor batch yang telah diinspeksi di QIR.</p>
        </div>

        <form action="{{ url('/coa/process') }}" method="POST">
            @csrf
            <div class="coa-body">
                
                <!-- Input 1: Pilih Material -->
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        Pilih Material (Finish Good) <span style="color: #e74a3b;">*</span>
                    </label>
                    <select name="no_mm" id="no_mm" class="form-control-custom" required>
                        <option value="" disabled selected>-- Pilih Material --</option>
                        @foreach($masterItems as $item)
                            <option value="{{ $item->no_mm }}">
                                [{{ $item->no_mm }}] - {{ $item->nama_material ?? $item->nama_item ?? 'Material Tanpa Nama' }}
                            </option>
                        @endforeach
                    </select>
                    <span class="form-help-text">Daftar item diambil dari Master Material kategori Finish Good.</span>
                </div>

                <!-- Input 2: Nomor Batch -->
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        Nomor Batch <span style="color: #e74a3b;">*</span>
                    </label>
                    <input type="text" name="no_batch" class="form-control-custom" placeholder="Contoh: 2601 atau 2601, 2602, 2603" required>
                    <span class="form-help-text">
                        <i class="fas fa-info-circle"></i> Pisahkan dengan koma jika ingin menggabungkan beberapa batch (contoh: <code style="background:#e3e6f0; padding:2px 5px; border-radius:3px; color:#333;">2601, 2602</code>).
                    </span>
                </div>

            </div>

            <!-- Card Footer & Action Buttons -->
            <div class="coa-footer">
                <a href="{{ url('/coa') }}" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-arrow-left"></i> Batal
                </a>
                <button type="submit" class="btn-custom btn-primary-custom">
                    Lanjut ke Form Input <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection