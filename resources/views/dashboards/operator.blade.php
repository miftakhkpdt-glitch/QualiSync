@extends('layouts.staff-layout')

@section('konten')
    <!-- 1. Banner Sapaan General khusus Operator -->
    <div class="welcome-banner" style="background: linear-gradient(135deg, #0f172a, #1e293b); color: white; padding: 22px 28px; border-radius: 12px; box-shadow: 0 6px 15px rgba(0,0,0,0.1); margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; border-left: 6px solid #10b981;">
        
        <!-- Sisi Kiri: Nama & Badge Online -->
        <div class="welcome-text">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                <h1 style="margin: 0; font-size: 22px; font-weight: bold; color: #ffffff; display: flex; align-items: center; gap: 8px;">
                    Halo, {{ Auth::user()->name }}! 👷‍♂️
                </h1>
                
                <!-- Badge Status Online -->
                <span style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); color: #34d399; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px;">
                    <span style="width: 7px; height: 7px; background-color: #10b981; border-radius: 50%; display: inline-block; box-shadow: 0 0 8px #10b981;"></span>
                    Online
                </span>
            </div>
            
            <p style="margin: 0; font-size: 13px; color: #94a3b8; line-height: 1.4;">
                Selamat bertugas di Dashboard Operator <b>{{ Auth::user()->department ?? 'Operasional' }}</b>. Utamakan Keselamatan Kerja (K3) dan jaga kualitas hasil kerja hari ini! ⚡
            </p>
        </div>

        <!-- Sisi Kanan: Shift Kerja Otomatis & Waktu Sistem -->
        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            
            <!-- Box Shift Kerja (Diberi ID 'live-shift' agar diisi otomatis oleh JS) -->
            <div style="text-align: right; background: rgba(255,255,255,0.05); padding: 8px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                <div style="font-size: 10px; color: #94a3b8; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">SHIFT KERJA</div>
                <div id="live-shift" style="font-size: 13px; font-weight: bold; color: #f59e0b; display: flex; align-items: center; gap: 6px; justify-content: flex-end; margin-top: 2px;">
                    <i class="fas fa-user-clock" style="font-size: 12px;"></i> Shift 1 (06:00 - 14:00)
                </div>
            </div>

            <!-- Box Waktu Sistem -->
            <div class="welcome-clock" style="text-align: right; background: rgba(255,255,255,0.05); padding: 8px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); min-width: 110px;">
                <div style="font-size: 10px; color: #38bdf8; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">Waktu Sistem</div>
                <div id="live-clock" style="font-size: 14px; font-weight: bold; font-family: monospace; margin-top: 2px; color: #f8fafc;">--:--:--</div>
            </div>

        </div>
    </div>

    <!-- 2. PEMANGGILAN KONTEN SPESIFIK ROLE -->
    @php
        $userRole = Str::slug(Auth::user()->role ?? 'operator', '_'); 
    @endphp

    @if(view()->exists("dashboards.konten.{$userRole}"))
        @include("dashboards.konten.{$userRole}")
    @else
        <div style="background: #fff; padding: 30px; border-radius: 10px; text-align: center; border: 1px dashed #cbd5e1; margin-bottom: 25px;">
            <i class="fas fa-folder-open" style="font-size: 36px; color: #94a3b8; margin-bottom: 10px;"></i>
            <h4 style="margin: 0 0 5px 0; color: #334155;">Konten Role Belum Tersedia</h4>
            <p style="margin: 0; font-size: 13px; color: #64748b;">File <code>dashboards/konten/{{ $userRole }}.blade.php</code> belum ditemukan.</p>
        </div>
    @endif

    <!-- 3. Widget Quotes K3 & Operasional General -->
    <div class="quote-box" style="background: #ffffff; padding: 20px 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.04); margin-top: 25px; border: 1px solid #e2e8f0; border-left: 4px solid #10b981; display: flex; justify-content: space-between; align-items: center;">
        <div class="quote-content">
            <h4 style="margin: 0 0 5px 0; font-size: 14px; color: #059669; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fas fa-hard-hat"></i> Pengingat K3 & Semangat Kerja</h4>
            <p id="quote-text" style="margin: 0; font-size: 14px; color: #334155; font-style: italic;">"Utamakan Keselamatan dan Kesehatan Kerja (K3) di setiap proses."</p>
        </div>
        <button onclick="gantiQuote()" class="btn-quote" style="background: #d1fae5; color: #047857; border: 1px solid #a7f3d0; padding: 8px 14px; border-radius: 6px; font-size: 12px; font-weight: bold; cursor: pointer; white-space: nowrap;">
            <i class="fas fa-sync-alt"></i> Ganti Quote
        </button>
    </div>

    <!-- Script Jam & Shift Kerja Otomatis -->
    <script>
        function updateClockAndShift() {
            const now = new Date();
            
            // 1. Update Jam
            const clockEl = document.getElementById('live-clock');
            if(clockEl) clockEl.innerText = now.toLocaleTimeString('id-ID');

            // 2. Update Shift Otomatis Berdasarkan Jam
            const hour = now.getHours();
            const shiftEl = document.getElementById('live-shift');
            
            if (shiftEl) {
                if (hour >= 7 && hour < 15) {
                    shiftEl.innerHTML = '<i class="fas fa-user-clock" style="font-size: 12px;"></i> Shift 1 (06:00 - 14:00)';
                } else if (hour >= 15 && hour < 23) {
                    shiftEl.innerHTML = '<i class="fas fa-user-clock" style="font-size: 12px;"></i> Shift 2 (14:00 - 22:00)';
                } else {
                    // Shift 3: Jam 22:00 - 06:00
                    shiftEl.innerHTML = '<i class="fas fa-user-clock" style="font-size: 12px;"></i> Shift 3 (22:00 - 06:00)';
                }
            }
        }

        setInterval(updateClockAndShift, 1000);
        updateClockAndShift();

        // Quotes khusus operasional/lapangan
        const quotes = [
            "\"Utamakan Keselamatan dan Kesehatan Kerja (K3) di setiap proses.\"",
            "\"Kualitas produk adalah cermin dari ketelitian operator.\"",
            "\"Kerja sama tim yang solid membuat target harian lebih mudah dicapai.\"",
            "\"Jangan ragu bertanya jika menemukan ketidaksesuaian pada material atau mesin.\"",
            "\"Tetap fokus dan jaga kondisi fisik selama jam kerja berlangsung.\""
        ];

        function gantiQuote() {
            const randomIndex = Math.floor(Math.random() * quotes.length);
            const quoteEl = document.getElementById('quote-text');
            if(quoteEl) quoteEl.innerText = quotes[randomIndex];
        }
    </script>
@endsection