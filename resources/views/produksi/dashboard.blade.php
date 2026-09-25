@extends('layouts.staff-layout')

@section('title', 'Dashboard Utama Produksi')

@section('konten')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div style="padding: 30px; background-color: #f8fafc; min-height: 100vh;">
    
    <!-- HEADER -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 style="font-size: 24px; font-weight: bold; color: #1e293b; margin: 0;">
                <i class="fas fa-desktop" style="color: #3b82f6; margin-right: 8px;"></i> Dashboard Utama Produksi
            </h2>
            <p style="color: #64748b; font-size: 14px; margin-top: 5px;">Ringkasan aktivitas operasional lantai produksi HARI INI ({{ date('d F Y') }}).</p>
        </div>
        
        <!-- TOMBOL JALAN PINTAS (QUICK ACTIONS) -->
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('produksi.oee.dashboard') }}" style="background: #1e293b; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold; box-shadow: 0 2px 4px rgba(30,41,59,0.3);">
                <i class="fas fa-chart-line"></i> Analisis OEE
            </a>
        </div>
    </div>

    <!-- 4 KARTU STATISTIK (HARI INI) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <!-- Card 1 -->
        <div style="background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-left: 5px solid #3b82f6; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-size: 13px; color: #64748b; font-weight: bold; text-transform: uppercase;">Total WO Aktif</div>
                <div style="font-size: 28px; font-weight: bold; color: #1e293b; margin-top: 5px;">{{ $wo_aktif }}</div>
            </div>
            <i class="fas fa-clipboard-list" style="font-size: 35px; color: #cbd5e1;"></i>
        </div>

        <!-- Card 2 -->
        <div style="background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-left: 5px solid #10b981; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-size: 13px; color: #64748b; font-weight: bold; text-transform: uppercase;">Output Hari Ini (FG)</div>
                <div style="font-size: 28px; font-weight: bold; color: #1e293b; margin-top: 5px;">{{ number_format($output_hari_ini) }}</div>
            </div>
            <i class="fas fa-box-open" style="font-size: 35px; color: #cbd5e1;"></i>
        </div>

        <!-- Card 3 -->
        <div style="background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-left: 5px solid #ef4444; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-size: 13px; color: #64748b; font-weight: bold; text-transform: uppercase;">Reject Hari Ini</div>
                <div style="font-size: 28px; font-weight: bold; color: #1e293b; margin-top: 5px;">{{ number_format($reject_hari_ini) }}</div>
            </div>
            <i class="fas fa-times-circle" style="font-size: 35px; color: #cbd5e1;"></i>
        </div>

        <!-- Card 4 -->
        <div style="background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-left: 5px solid #f59e0b; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-size: 13px; color: #64748b; font-weight: bold; text-transform: uppercase;">Mesin Mati Hari Ini</div>
                <div style="font-size: 28px; font-weight: bold; color: #1e293b; margin-top: 5px;">{{ number_format($downtime_hari_ini) }} <span style="font-size:12px;">Mnt</span></div>
            </div>
            <i class="fas fa-power-off" style="font-size: 35px; color: #cbd5e1;"></i>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        
        <!-- TABEL WO TERBARU -->
        <div style="background: white; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; overflow: hidden;">
            <div style="padding: 15px 20px; border-bottom: 1px solid #f1f5f9; background: #fff; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 16px; margin: 0; color: #334155;"><i class="fas fa-tasks text-blue-500"></i> Work Order Berjalan</h3>
                <a href="#" style="font-size: 12px; color: #3b82f6; text-decoration: none; font-weight: bold;">Lihat Semua &rarr;</a>
            </div>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead style="background-color: #f8fafc; color: #64748b; font-size: 12px; text-transform: uppercase;">
                    <tr>
                        <th style="padding: 12px 20px;">Nomor WO</th>
                        <th style="padding: 12px 20px;">Produk (Material)</th>
                        <th style="padding: 12px 20px;">Target Qty</th>
                        <th style="padding: 12px 20px; text-align: center;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wo_berjalan as $w)
                    <tr style="border-bottom: 1px solid #f1f5f9; font-size: 13px;">
                        <td style="padding: 12px 20px; font-weight: bold; color: #1e293b;">{{ $w->nomor_wo ?? '-' }}</td>
                        <td style="padding: 12px 20px;">{{ $w->nama_material ?? 'Tidak diketahui' }}</td>
                        <td style="padding: 12px 20px;">{{ number_format($w->qty_rencana ?? 0) }} Pcs</td>
                        <td style="padding: 12px 20px; text-align: center;">
                            <!-- Sesuaikan route form daily report -->
                            <a href="#" style="background: #10b981; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 11px; font-weight: bold;">Isi Laporan</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="padding: 20px; text-align: center; color: #94a3b8;">Belum ada Work Order.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- GRAFIK 7 HARI TERAKHIR -->
        <div style="background: white; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; padding: 20px;">
            <h3 style="font-size: 16px; margin: 0 0 15px 0; color: #334155;"><i class="fas fa-chart-area text-blue-500"></i> Output 7 Hari Terakhir</h3>
            <div id="chart7Hari"></div>
        </div>

    </div>
</div>

<script>
    var options7Hari = {
        series: [{ name: 'Total Output (FG)', data: {!! json_encode($chart_values) !!} }],
        chart: { type: 'area', height: 250, toolbar: { show: false } },
        colors: ['#3b82f6'],
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.1, stops: [0, 90, 100] } },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        xaxis: { categories: {!! json_encode($chart_labels) !!} },
        yaxis: { labels: { formatter: function (val) { return Math.round(val); } } },
        tooltip: { y: { formatter: function (val) { return val + " Pcs" } } }
    };
    new ApexCharts(document.querySelector("#chart7Hari"), options7Hari).render();
</script>
@endsection