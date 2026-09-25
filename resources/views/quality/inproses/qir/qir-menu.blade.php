@extends('layouts.staff-layout') <!-- Sesuaikan dengan nama layout Anda -->

@section('title', 'Menu QIR Inspection')

@push('styles')
<style>
    /* Container Utama */
    .qir-container {
        max-width: 900px;
        margin: 40px auto;
        padding: 0 20px;
    }

    /* Bagian Header */
    .qir-header {
        text-align: center;
        margin-bottom: 40px;
    }
    .qir-header h2 {
        font-size: 28px;
        color: var(--text-main, #1e293b);
        font-weight: 800;
        margin-bottom: 10px;
        letter-spacing: -0.5px;
    }
    .qir-header p {
        color: var(--text-muted, #64748b);
        font-size: 15px;
        margin: 0;
    }

    /* Grid Layout untuk Card */
    .qir-menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 30px;
        margin-bottom: 40px;
    }

    /* Styling Card */
    .qir-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 40px 30px;
        text-align: center;
        text-decoration: none;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* Efek Hover saat mouse diarahkan */
    .qir-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        border-color: #cbd5e1;
    }

    /* Lingkaran Ikon */
    .qir-icon-wrapper {
        width: 80px;
        height: 80px;
        border-radius: 50%;
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
        background-color: #eff6ff;
        color: #3b82f6;
    }
    .card-input .qir-card-accent { 
        background: linear-gradient(90deg, #3b82f6, #60a5fa); 
    }

    /* Varian Warna Card Kanan (Hijau) */
    .card-history .qir-icon-wrapper {
        background-color: #f0fdf4;
        color: #22c55e;
    }
    .card-history .qir-card-accent { 
        background: linear-gradient(90deg, #22c55e, #4ade80); 
    }

    /* Teks dalam Card */
    .qir-card h3 {
        font-size: 20px;
        color: #334155;
        margin: 0 0 12px 0;
        font-weight: 700;
    }
    .qir-card p {
        font-size: 14px;
        color: #64748b;
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
        color: #64748b;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: color 0.2s;
        justify-content: center;
        padding: 12px 20px;
        border-radius: 8px;
        background-color: #f8fafc;
        width: fit-content;
        margin: 0 auto;
        border: 1px solid #e2e8f0;
    }
    .qir-back-link:hover {
        color: #0f172a;
        background-color: #f1f5f9;
    }
</style>
@endpush

@section('konten')
    <div class="qir-container">
        <!-- Header -->
        <div class="qir-header">
            <h2>
                <i class="fas fa-clipboard-list" style="color: #d4a32a; margin-right: 8px;"></i> 
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