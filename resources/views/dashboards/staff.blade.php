@extends('layouts.staff-layout')

@section('konten')
    <style>
        .btn-quote {
            transition: all 0.3s ease;
        }
        .btn-quote:hover {
            background: #fde68a !important;
            transform: scale(1.05);
        }
    </style>

    <!-- 1. Banner Sapaan General -->
    <div class="welcome-banner" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: white; padding: 25px 30px; border-radius: 12px; box-shadow: 0 6px 15px rgba(0,0,0,0.1); margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; border-left: 6px solid #3b82f6;">
        <div class="welcome-text">
            <h1 style="margin: 0 0 6px 0; font-size: 22px; display: flex; align-items: center; gap: 10px;">Halo, {{ Auth::user()->name }}! ☕</h1>
            <p style="margin: 0; font-size: 13px; color: #94a3b8; line-height: 1.4;">
                Selamat datang di Dashboard Staff <b>{{ ucwords(Auth::user()->department ?? 'Quality') }}</b>. Semoga harimu lancar dan produktif! 🚀
            </p>
        </div>
        <div class="welcome-clock" style="text-align: right; background: rgba(255,255,255,0.05); padding: 10px 18px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); min-width: 120px;">
            <div style="font-size: 10px; color: #38bdf8; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">Waktu Sistem</div>
            <div id="live-clock" style="font-size: 15px; font-weight: bold; font-family: monospace; margin-top: 2px; color: #f8fafc;">--:--:--</div>
        </div>
    </div>

    <!-- 2. PEMANGGILAN KONTEN SPESIFIK DEPARTEMEN -->
    @php
        $userDept = Str::slug(Auth::user()->department ?? 'staff_quality', '_'); 
    @endphp

    @if(view()->exists("dashboards.konten.{$userDept}"))
        @include("dashboards.konten.{$userDept}")
    @else
        <div style="background: #fff; padding: 30px; border-radius: 10px; text-align: center; border: 1px dashed #cbd5e1; margin-bottom: 25px;">
            <i class="fas fa-folder-open" style="font-size: 36px; color: #94a3b8; margin-bottom: 10px;"></i>
            <h4 style="margin: 0 0 5px 0; color: #334155;">Konten Departemen Belum Tersedia</h4>
            <p style="margin: 0; font-size: 13px; color: #64748b;">File <code>dashboards/konten/{{ $userDept }}.blade.php</code> belum ditemukan.</p>
        </div>
    @endif

    <!-- 3. Widget Quotes General -->
    <div class="quote-box" style="background: #ffffff; padding: 20px 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.04); margin-top: 25px; border: 1px solid #e2e8f0; border-left: 4px solid #f59e0b; display: flex; justify-content: space-between; align-items: center;">
        <div class="quote-content">
            <h4 style="margin: 0 0 5px 0; font-size: 14px; color: #d97706; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fas fa-lightbulb"></i> Quotes Hari Ini</h4>
            <p id="quote-text" style="margin: 0; font-size: 14px; color: #334155; font-style: italic;">"Kerja cerdas dan konsisten adalah kunci sukses pekerjaan harian."</p>
        </div>
        <button onclick="gantiQuote()" class="btn-quote" style="background: #fef3c7; color: #d97706; border: 1px solid #fde68a; padding: 8px 14px; border-radius: 6px; font-size: 12px; font-weight: bold; cursor: pointer; white-space: nowrap;">
            <i class="fas fa-sync-alt"></i> Ganti Quote
        </button>
    </div>

    <!-- Script Jam & Quotes -->
    <script>
        function updateClock() {
            const now = new Date();
            const clockEl = document.getElementById('live-clock');
            if(clockEl) clockEl.innerText = now.toLocaleTimeString('id-ID');
        }
        setInterval(updateClock, 1000);
        updateClock();

        const quotes = [
            "\"Kerja cerdas dan konsisten adalah kunci sukses pekerjaan harian.\"",
            "\"Fokus pada solusi, bukan pada hambatan.\"",
            "\"Jangan takut salah input data, yang penting selalu lakukan verifikasi ulang.\"",
            "\"Komunikasi yang baik mencegah 90% masalah di tempat kerja.\"",
            "\"Istirahat yang cukup membuat pikiran lebih jernih dan teliti.\""
        ];

        function gantiQuote() {
            const randomIndex = Math.floor(Math.random() * quotes.length);
            const quoteEl = document.getElementById('quote-text');
            if(quoteEl) quoteEl.innerText = quotes[randomIndex];
        }
    </script>
@endsection