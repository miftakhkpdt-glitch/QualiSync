@extends('layouts.staff-layout')

@section('konten')
<div style="margin-bottom: 25px;">
    <h3 style="color: #1e293b; font-weight: bold; margin-bottom: 5px;">Dashboard Quality</h3>
    <p style="color: #64748b; margin-top: 0;">Monitoring aktivitas inspeksi dan penjaminan mutu hari ini.</p>
</div>

<!-- KARTU RINGKASAN (KPI) -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px;">
    
    <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.03); border-left: 5px solid #f59e0b; position: relative; overflow: hidden;">
        <p style="color: #64748b; font-size: 13px; font-weight: bold; margin: 0 0 10px 0;">PENDING INCOMING QC</p>
        <div style="display: flex; justify-content: space-between; align-items: center; position: relative; z-index: 1;">
            <h2 style="color: #1e293b; margin: 0; font-size: 36px;">{{ $pendingIncoming ?? 0 }}</h2>
        </div>
        <!-- Icon Background transparan -->
        <i class="fas fa-boxes" style="font-size: 60px; color: #fef3c7; position: absolute; right: 10px; bottom: 10px; z-index: 0;"></i>
    </div>

    <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.03); border-left: 5px solid #3b82f6; position: relative; overflow: hidden;">
        <p style="color: #64748b; font-size: 13px; font-weight: bold; margin: 0 0 10px 0;">QIR HARI INI</p>
        <div style="display: flex; justify-content: space-between; align-items: center; position: relative; z-index: 1;">
            <h2 style="color: #1e293b; margin: 0; font-size: 36px;">{{ $qirHariIni ?? 0 }}</h2>
        </div>
        <i class="fas fa-clipboard-check" style="font-size: 60px; color: #dbeafe; position: absolute; right: 10px; bottom: 10px; z-index: 0;"></i>
    </div>

    <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.03); border-left: 5px solid #ef4444; position: relative; overflow: hidden;">
        <p style="color: #64748b; font-size: 13px; font-weight: bold; margin: 0 0 10px 0;">CAPA 8D TERBUKA</p>
        <div style="display: flex; justify-content: space-between; align-items: center; position: relative; z-index: 1;">
            <h2 style="color: #1e293b; margin: 0; font-size: 36px;">{{ $capaTerbuka ?? 0 }}</h2>
        </div>
        <i class="fas fa-exclamation-triangle" style="font-size: 60px; color: #fee2e2; position: absolute; right: 10px; bottom: 10px; z-index: 0;"></i>
    </div>

</div>

<!-- AREA KONTEN UTAMA: DIBAGI 2 KOLOM (KIRI & KANAN) -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
    
    <!-- KOLOM KIRI (GRAFIK & TABEL) -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        
        <!-- 1. KARTU GRAFIK INSPEKSI -->
        <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.03);">
            <h5 style="margin-top: 0; color: #1e293b; font-weight: bold; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                <i class="fas fa-chart-bar" style="color: #6366f1; margin-right: 5px;"></i> Tren Inspeksi vs Reject (Bulan Ini)
            </h5>
            <div style="position: relative; height: 250px; width: 100%; margin-top: 15px;">
                <!-- Canvas untuk Chart.js -->
                <canvas id="qcTrendChart"></canvas>
            </div>
        </div>

        <!-- 2. KARTU PERLU TINDAKAN SEGERA -->
        <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.03);">
            <h5 style="margin-top: 0; color: #1e293b; font-weight: bold; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                <i class="fas fa-bell" style="color: #f59e0b; margin-right: 5px;"></i> Perlu Tindakan Segera
            </h5>
            <table style="width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 14px;">
                <thead>
                    <tr style="background: #f8fafc; text-align: left;">
                        <th style="padding: 10px; border-bottom: 1px solid #e2e8f0;">Tugas</th>
                        <th style="padding: 10px; border-bottom: 1px solid #e2e8f0;">Referensi</th>
                        <th style="padding: 10px; border-bottom: 1px solid #e2e8f0;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tugasSegera ?? [] as $tugas)
                    <tr>
                        <td style="padding: 12px 10px; border-bottom: 1px solid #e2e8f0; font-weight: 500;">Approval QIR</td>
                        <td style="padding: 12px 10px; border-bottom: 1px solid #e2e8f0; color: #3b82f6;">{{ $tugas->nomor_qir ?? $tugas->id }}</td>
                        <td style="padding: 12px 10px; border-bottom: 1px solid #e2e8f0;">
                            <span style="background: #fef3c7; color: #d97706; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">{{ $tugas->status }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="padding: 25px; text-align: center; color: #94a3b8; border-bottom: 1px solid #e2e8f0;">
                            <i class="fas fa-check-circle" style="font-size: 24px; color: #10b981; display: block; margin-bottom: 10px;"></i>
                            Hebat! Tidak ada tugas mendesak saat ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <!-- KOLOM KANAN (AKSES CEPAT & AKTIVITAS) -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        
        <!-- 1. KARTU AKSES CEPAT -->
        <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.03);">
            <h5 style="margin-top: 0; color: #1e293b; font-weight: bold; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                <i class="fas fa-bolt" style="color: #3b82f6; margin-right: 5px;"></i> Akses Cepat
            </h5>
            <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 15px;">
                <a href="{{ url('/qir/create') }}" style="padding: 12px 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; color: #334155; display: flex; align-items: center; gap: 10px; transition: all 0.2s; font-weight: 500;">
                    <i class="fas fa-plus-circle" style="color: #10b981; font-size: 18px;"></i> Buat QIR Baru
                </a>
                <a href="{{ url('/coa/create') }}" style="padding: 12px 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; color: #334155; display: flex; align-items: center; gap: 10px; transition: all 0.2s; font-weight: 500;">
                    <i class="fas fa-certificate" style="color: #3b82f6; font-size: 18px;"></i> Terbitkan COA
                </a>
                <a href="#" style="padding: 12px 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; color: #334155; display: flex; align-items: center; gap: 10px; transition: all 0.2s; font-weight: 500;">
                    <i class="fas fa-exclamation-triangle" style="color: #ef4444; font-size: 18px;"></i> Laporan CAPA 8D
                </a>
            </div>
        </div>

        <!-- 2. KARTU AKTIVITAS TERBARU -->
        <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.03);">
            <h5 style="margin-top: 0; color: #1e293b; font-weight: bold; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                <i class="fas fa-history" style="color: #8b5cf6; margin-right: 5px;"></i> Aktivitas Terbaru
            </h5>
            
            <div style="margin-top: 15px; display: flex; flex-direction: column; gap: 15px;">
                <!-- Item Log 1 -->
                <div style="border-left: 3px solid #10b981; padding-left: 12px; position: relative;">
                    <div style="font-weight: bold; font-size: 13px; color: #1e293b;">COA Diterbitkan</div>
                    <div style="font-size: 12px; color: #475569; margin-top: 2px;">COA/09/2026/011 - PT Yasulor</div>
                    <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;"><i class="far fa-clock"></i> 10 menit yang lalu</div>
                </div>
                <!-- Item Log 2 -->
                <div style="border-left: 3px solid #ef4444; padding-left: 12px; position: relative;">
                    <div style="font-weight: bold; font-size: 13px; color: #1e293b;">QIR Dibuat (Reject)</div>
                    <div style="font-size: 12px; color: #475569; margin-top: 2px;">Material Tube NG Dimensi</div>
                    <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;"><i class="far fa-clock"></i> 2 jam yang lalu</div>
                </div>
                <!-- Item Log 3 -->
                <div style="border-left: 3px solid #3b82f6; padding-left: 12px; position: relative;">
                    <div style="font-weight: bold; font-size: 13px; color: #1e293b;">Incoming Material</div>
                    <div style="font-size: 12px; color: #475569; margin-top: 2px;">Kedatangan Resin Supplier A</div>
                    <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;"><i class="far fa-clock"></i> Kemarin, 14:00</div>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 15px; padding-top: 10px; border-top: 1px solid #f1f5f9;">
                <a href="#" style="font-size: 12px; color: #3b82f6; text-decoration: none; font-weight: 600;">Lihat Semua Log</a>
            </div>
        </div>

    </div>

</div>

<!-- SCRIPT UNTUK MEMANGGIL CHART.JS DENGAN DATA REAL -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('qcTrendChart').getContext('2d');
        
        // Data dari Controller dikonversi ke format JSON JavaScript
        const chartLabels = {!! json_encode($labels) !!};
        const dataInspeksi = {!! json_encode($totalInspeksi) !!};
        const dataReject = {!! json_encode($totalReject) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        type: 'line',
                        label: 'Reject (NG)',
                        data: dataReject,
                        borderColor: '#ef4444',
                        backgroundColor: '#ef4444',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: false
                    },
                    {
                        type: 'bar',
                        label: 'Total Inspeksi',
                        data: dataInspeksi,
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: '#3b82f6',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 4], color: '#e2e8f0' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
@endsection