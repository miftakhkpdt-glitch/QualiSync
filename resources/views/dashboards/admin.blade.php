@extends('layouts.staff-layout')

@section('title', 'Dashboard Admin - PT KIMPAI DYNA TUBE')

@push('styles')
<style>
    .dashboard-admin h1 { font-size: 26px; color: var(--text-main); margin: 0 0 8px; }
    .dashboard-admin .subtitle { color: var(--text-muted); font-size: 14px; margin: 0 0 24px; }

    .dashboard-admin .baris-kpi {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }
    .dashboard-admin .kartu-kpi {
        min-height: 128px;
        box-sizing: border-box;
        background: #ffffff;
        padding: 18px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        border: 1px solid #e2e8f0;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .dashboard-admin .kartu-kpi:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.09);
    }
    .dashboard-admin .kpi-teks { min-width: 0; }
    .dashboard-admin .kpi-teks h3 {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 600;
        line-height: 1.4;
        margin: 0 0 8px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .dashboard-admin .kpi-teks .angka {
        font-size: 32px;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1;
        margin: 0;
    }

    .dashboard-admin .icon-box {
        width: 52px;
        height: 52px;
        flex: 0 0 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .dashboard-admin .icon-blue { background: #eff6ff; color: #2563eb; }
    .dashboard-admin .icon-red { background: #fef2f2; color: #dc2626; }
    .dashboard-admin .icon-green { background: #f0fdf4; color: #16a34a; }
    .dashboard-admin .icon-orange { background: #fffbeb; color: #d97706; }

    .dashboard-admin .baris-grafik {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr);
        gap: 20px;
    }
    .dashboard-admin .kartu-grafik {
        min-width: 0;
        background: #ffffff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
        border: 1px solid #e2e8f0;
    }
    .dashboard-admin .kartu-grafik h3 {
        font-size: 15px;
        margin: 0 0 16px;
        color: var(--text-main);
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 12px;
    }
    .dashboard-admin .wadah-gambar {
        width: 100%;
        height: 300px;
        background: #ffffff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .dashboard-admin .wadah-todo {
        width: 100%;
        height: 300px;
        background: #f8fafc;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 1px dashed #cbd5e1;
    }

    @media (max-width: 1199px) {
        .dashboard-admin .baris-kpi { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .dashboard-admin .baris-grafik { grid-template-columns: 1fr; }
    }
    @media (max-width: 700px) {
        .dashboard-admin .baris-kpi { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .dashboard-admin .kartu-kpi { padding: 14px; }
        .dashboard-admin .icon-box { width: 44px; height: 44px; flex-basis: 44px; font-size: 19px; }
    }
    @media (max-width: 480px) {
        .dashboard-admin .baris-kpi { grid-template-columns: 1fr; }
        .dashboard-admin .kartu-kpi { min-height: 100px; }
        .dashboard-admin .kartu-grafik { padding: 16px; }
    }
</style>
@endpush

@section('konten')
<div class="dashboard-admin">
    <h1>Dashboard Overview</h1>
    <p class="subtitle">Monitoring data operasional perusahaan secara real-time.</p>

    <!-- KARTU KPI (DATA REAL-TIME) -->
    <div class="baris-kpi">
        <div class="kartu-kpi">
            <div class="kpi-teks">
                <h3>Total COA</h3>
                <p class="angka">{{ number_format($totalCoa ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="icon-box icon-blue"><i class="fas fa-certificate"></i></div>
        </div>
        
        <div class="kartu-kpi">
            <div class="kpi-teks">
                <h3>CAPA Terbuka</h3>
                <p class="angka" style="color: #dc2626;">{{ number_format($capaTerbuka ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="icon-box icon-red"><i class="fas fa-folder-open"></i></div>
        </div>
        
        <div class="kartu-kpi">
            <div class="kpi-teks">
                <h3>CAPA Selesai</h3>
                <p class="angka" style="color: #16a34a;">{{ number_format($capaSelesai ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="icon-box icon-green"><i class="fas fa-folder-check"></i></div>
        </div>

        <div class="kartu-kpi">
            <div class="kpi-teks">
                <h3>PR Menunggu</h3>
                <p class="angka" style="color: #d97706;">{{ number_format($prMenunggu ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="icon-box icon-orange"><i class="fas fa-shopping-cart"></i></div>
        </div>

        <div class="kartu-kpi">
            <div class="kpi-teks">
                <h3>WO In-Proses</h3>
                <p class="angka" style="color: #2563eb;">{{ number_format($woAktif ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="icon-box icon-blue"><i class="fas fa-industry"></i></div>
        </div>
    </div>

    <!-- AREA GRAFIK & TO-DO LIST -->
    <div class="baris-grafik">
        
        <!-- GRAFIK PARETO -->
        <div class="kartu-grafik">
            <h3>Top 5 Jenis Reject (Pareto)</h3>
            <div class="wadah-gambar">
                <canvas id="paretoChart"></canvas>
            </div>
        </div>
        
        <!-- TO-DO LIST / NOTIFIKASI -->
        <div class="kartu-grafik">
            <h3>Perlu Tindakan Segera</h3>
            <div class="wadah-todo">
                <i class="fas fa-inbox" style="font-size: 40px; color: #cbd5e1; margin-bottom: 15px;"></i>
                <p style="color: #64748b; font-size: 14px; text-align: center; margin: 0;">Belum ada notifikasi<br>baru hari ini.</p>
            </div>
        </div>
        
    </div>
</div>
@endsection

@push('scripts')
<!-- Memanggil Library Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Ambil data dari Controller
        const rawParetoData = @json($paretoData ?? []);

        let chartLabels = rawParetoData.map(item => item.jenis_reject);
        let chartValues = rawParetoData.map(item => item.total);

        // 2. Jika data kosong (karena database baru di-reset)
        if(chartLabels.length === 0) {
            chartLabels = ['Belum ada data reject'];
            chartValues = [0];
        }

        // 3. Gambar Chart-nya
        const ctx = document.getElementById('paretoChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Total Reject (Pcs)',
                    data: chartValues,
                    backgroundColor: 'rgba(37, 99, 235, 0.8)',
                    borderColor: '#2563eb',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false } // Hilangkan label di atas
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 } // Angka di sumbu Y tidak boleh desimal
                    }
                }
            }
        });
    });
</script>
@endpush