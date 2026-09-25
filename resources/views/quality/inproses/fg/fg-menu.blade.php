@extends('layouts.staff-layout')

@push('styles')
<style>
    .menu-container {
        max-width: 1100px; /* Diperlebar agar 3 kartu muat sebaris */
        margin: 50px auto;
        padding: 20px;
    }
    .menu-header {
        text-align: center;
        margin-bottom: 40px;
    }
    .menu-header h2 {
        font-size: 24px;
        font-weight: 800;
        color: #1e293b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .menu-header p {
        color: #64748b;
        font-size: 14px;
        margin: 0;
    }
    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 20px;
    }
    .menu-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 30px;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }
    .menu-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
        border-color: #cbd5e1;
    }
    .card-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 20px;
    }
    
    /* Warna Ikon Masing-masing Kartu */
    .icon-create {
        background: #eff6ff;
        color: #2563eb;
    }
    .icon-history {
        background: #f0fdf4;
        color: #16a34a;
    }
    .icon-report {
        background: #f3e8ff; /* Ungu Muda */
        color: #9333ea; /* Ungu Tua */
    }
    .icon-cpk {
        background-color: #ffedd5;
        color: #f97316;
    }

    .menu-card h3 {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 8px 0;
    }
    .menu-card p {
        font-size: 13px;
        color: #64748b;
        margin: 0;
        line-height: 1.5;
    }
</style>
@endpush

@section('konten')
<div class="menu-container">
    <div class="menu-header">
        <h2><i class="fas fa-boxes" style="color: #2563eb; margin-right: 8px;"></i> Menu In-Proses Finished Goods (FG)</h2>
        <p>Pilih menu di bawah ini untuk mengelola input data atau melihat riwayat inspeksi Finished Goods.</p>
    </div>

    <div class="menu-grid">
        <!-- Kartu 1: Buat Input Baru -->
        <a href="{{ url('/in-proses/fg/create') }}" class="menu-card">
            <div class="card-icon icon-create">
                <i class="fas fa-plus-circle"></i>
            </div>
            <h3>Input FG Baru</h3>
            <p>Mulai sesi pencatatan dan pengujian lembar inspeksi Finished Goods baru berdasarkan batch dan shift kerja.</p>
        </a>

        <!-- Kartu 2: Riwayat / Data Tersimpan -->
        <a href="{{ url('/in-proses/fg/riwayat') }}" class="menu-card">
            <div class="card-icon icon-history">
                <i class="fas fa-history"></i>
            </div>
            <h3>Riwayat & Data FG</h3>
            <p>Melihat daftar arsip, mencetak dokumen, atau mengubah kembali data inspeksi Finished Goods yang pernah disimpan.</p>
        </a>

        <!-- Kartu 3: Laporan Weekly (Pareto Chart) -->
        <a href="{{ url('/in-proses/fg/laporan-weekly') }}" class="menu-card" style="border-top: 4px solid #9333ea;">
            <div class="card-icon icon-report">
                <i class="fas fa-chart-pie"></i>
            </div>
            <h3>Laporan Weekly (Grafik)</h3>
            <p>Generate laporan Pareto cacat/defect per line otomatis berdasarkan rentang tanggal inspeksi In-Proses FG Anda.</p>
        </a>

        <!-- Kartu 4: Laporan CPK Proses -->
        <a href="{{ url('/in-proses/fg/cpk') }}" class="menu-card" style="border-top: 4px solid #f97316;">
            <div class="card-icon icon-cpk">
                <i class="fas fa-tachometer-alt"></i>
            </div>
            <h3>Laporan CPK Proses</h3>
            <p>Analisis Kapabilitas Proses (CPK) secara sistematis berdasarkan data inspeksi dan pengukuran per line produksi.</p>
        </a>
        
    </div>
</div>
@endsection