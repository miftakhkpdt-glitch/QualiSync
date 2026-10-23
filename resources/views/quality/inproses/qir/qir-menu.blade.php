@extends('layouts.staff-layout') <!-- Sesuaikan dengan nama layout Anda -->

@section('title', 'Menu QIR Inspection')

@push('styles')
<style>
    /* Container Utama */
    .qir-container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 30px;
        border: 1px solid #eee8dd;
        border-radius: 28px;
        background: linear-gradient(145deg, #f4f8f1, #fff4eb 58%, #f5f0f8);
        box-shadow: 0 14px 34px rgba(107, 91, 72, .08);
    }

    /* Bagian Header */
    .qir-header {
        text-align: center;
        margin-bottom: 40px;
    }
    .qir-header h2 {
        font-size: 28px;
        color: #51443d;
        font-weight: 800;
        margin-bottom: 10px;
        letter-spacing: -0.5px;
    }
    .qir-header p {
        color: #887b73;
        font-size: 15px;
        margin: 0;
    }

    /* Grid Layout untuk Card */
    .qir-menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 22px;
        margin-bottom: 32px;
    }

    /* Styling Card */
    .qir-card {
        background: #fffdf9;
        border-radius: 22px;
        padding: 40px 30px;
        text-align: center;
        text-decoration: none;
        border: 1px solid #efe7dc;
        box-shadow: 0 8px 18px rgba(107, 91, 72, .07), inset 0 1px 0 rgba(255, 255, 255, .85);
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* Efek Hover saat mouse diarahkan */
    .qir-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 24px rgba(107, 91, 72, .12);
        border-color: #e7c8b5;
    }

    /* Lingkaran Ikon */
    .qir-icon-wrapper {
        width: 80px;
        height: 80px;
        border-radius: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 25px;
        font-size: 32px;
        transition: transform 0.3s ease;
    }
    .qir-card:hover .qir-icon-wrapper {
        transform: scale(1.1) rotate(5deg);
    }

    /* Varian Warna Card Kiri (Biru) */
    .card-input .qir-icon-wrapper {
        background-color: #f8dfd4;
        color: #c87862;
    }
    .card-input .qir-card-accent { 
        background: linear-gradient(90deg, #e9947c, #f2b29b);
    }

    /* Varian Warna Card Kanan (Hijau) */
    .card-history .qir-icon-wrapper {
        background-color: #dceee5;
        color: #5a9278;
    }
    .card-history .qir-card-accent { 
        background: linear-gradient(90deg, #8db9aa, #b2d4c3);
    }

    /* Teks dalam Card */
    .qir-card h3 {
        font-size: 20px;
        color: #51443d;
        margin: 0 0 12px 0;
        font-weight: 700;
    }
    .qir-card p {
        font-size: 14px;
        color: #887b73;
        line-height: 1.6;
        margin: 0;
    }

    /* Garis atas pemanis pada Card */
    .qir-card-accent {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
    }

    /* Link Kembali */
    .qir-back-link {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #82766c;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: color 0.2s;
        justify-content: center;
        padding: 12px 20px;
        border-radius: 14px;
        background-color: #faf6ee;
        width: fit-content;
        margin: 0 auto;
        border: 1px solid #eee4d8;
    }
    .qir-back-link:hover {
        color: #51443d;
        background-color: #f5eee3;
    }
    @media(max-width: 600px) {
        .qir-container { margin: 20px 10px; padding: 20px 15px; border-radius: 20px; }
        .qir-menu-grid { grid-template-columns: 1fr; }
        .qir-card { padding: 30px 22px; }
    }
</style>
@endpush

@section('konten')
    <div class="qir-container">
        <!-- Header -->
        <div class="qir-header">
            <h2>
                <i class="fas fa-clipboard-list" style="color: #df987e; margin-right: 8px;"></i>
                MENU QIR INSPECTION
            </h2>
            <p>Pilih tindakan untuk mengelola Quality Inspection Report</p>
        </div>

        <!-- Grid Menu -->
        <div class="qir-menu-grid">
            
            <!-- Card 1: Input QIR -->
            <a href="{{ url('/qir/create') }}" class="qir-card card-input">
                <div class="qir-card-accent"></div>
                <div class="qir-icon-wrapper">
                    <i class="fas fa-file-signature"></i>
                </div>
                <h3>Input QIR Baru</h3>
                <p>Buat laporan inspeksi kualitas baru untuk mencatat hasil pengecekan parameter secara terperinci.</p>
            </a>

            <!-- Card 2: Cek Riwayat -->
            <a href="{{ url('/qir/riwayat') }}" class="qir-card card-history">
                <div class="qir-card-accent"></div>
                <div class="qir-icon-wrapper">
                    <i class="fas fa-history"></i>
                </div>
                <h3>Cek Riwayat QIR</h3>
                <p>Pantau status, edit, dan analisis riwayat laporan inspeksi yang telah didaftarkan sebelumnya.</p>
            </a>

        </div>

        <!-- Tombol Kembali -->
        <a href="{{ url('/dashboard') }}" class="qir-back-link">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard Utama
        </a>
    </div>
@endsection