@extends('layouts.staff-layout')

@push('styles')
<style>
    .fg-clay-page {
        max-width: 1200px;
        margin: 50px auto;
        padding: 30px;
        border: 1px solid #eee8dd;
        border-radius: 28px;
        background: linear-gradient(145deg, #f4f8f1, #fff4eb 58%, #f5f0f8);
        box-shadow: 0 14px 34px rgba(107, 91, 72, .08);
    }
    .fg-clay-page .menu-header {
        text-align: center;
        margin-bottom: 32px;
    }
    .fg-clay-page .menu-header h2 {
        font-size: 24px;
        font-weight: 800;
        color: #51443d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .fg-clay-page .menu-header h2 i {
        color: #df987e !important;
    }
    .fg-clay-page .menu-header p {
        color: #887b73;
        font-size: 14px;
        margin: 0;
    }
    .fg-clay-page .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 20px;
    }
    .fg-clay-page .menu-card {
        background: #fffdf9;
        border: 1px solid #efe7dc;
        border-radius: 22px;
        padding: 30px;
        text-decoration: none;
        transition: all 0.25s ease;
        box-shadow: 0 8px 18px rgba(107, 91, 72, .07), inset 0 1px 0 rgba(255, 255, 255, .85);
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }
    .fg-clay-page .menu-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 24px rgba(107, 91, 72, .12);
        border-color: #e7c8b5;
    }
    .fg-clay-page .card-icon {
        width: 50px;
        height: 50px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 20px;
    }
    
    .fg-clay-page .icon-create {
        background: #f8dfd4;
        color: #c87862;
    }
    .fg-clay-page .icon-history {
        background: #dceee5;
        color: #5a9278;
    }
    .fg-clay-page .icon-report {
        background: #eee2f1;
        color: #9875a5;
    }
    .fg-clay-page .icon-cpk {
        background-color: #f8e8c9;
        color: #b88748;
    }

    .fg-clay-page .menu-card h3 {
        font-size: 18px;
        font-weight: 700;
        color: #51443d;
        margin: 0 0 8px 0;
    }
    .fg-clay-page .menu-card p {
        font-size: 13px;
        color: #887b73;
        margin: 0;
        line-height: 1.5;
    }
    .fg-clay-page .menu-card[style*="border-top"] {
        border-top: 1px solid #efe7dc !important;
    }
    @media(max-width: 600px) {
        .fg-clay-page { margin: 20px 10px; padding: 20px 15px; border-radius: 20px; }
        .fg-clay-page .menu-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('konten')
<div class="menu-container fg-clay-page">
    <div class="menu-header">
        <h2><i class="fas fa-boxes" style="color: #df987e; margin-right: 8px;"></i> Menu In-Proses Finished Goods (FG)</h2>
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