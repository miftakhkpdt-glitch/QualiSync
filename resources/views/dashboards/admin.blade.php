@extends('layouts.staff-layout')

@section('title', 'Dashboard Admin - PT KIMPAI DYNA TUBE')

@push('styles')
<style>
    /* CSS Khusus Halaman Dashboard Admin Saja */
    h1 { font-size: 24px; color: var(--text-main); margin-bottom: 8px; }
    p.subtitle { color: var(--text-muted); font-size: 14px; margin-bottom: 30px; margin-top: 0; }
    
    /* Ubah grid dari 3 kolom jadi 5 kolom agar muat semua KPI */
    .baris-kpi { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 40px; }
    .kartu-kpi { background-color: var(--white); padding: 24px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; border: 1px solid #f3f4f6; transition: 0.2s; }
    .kartu-kpi:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
    
    .kpi-teks h3 { font-size: 14px; color: var(--text-muted); font-weight: 500; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
    .kpi-teks .angka { font-size: 36px; font-weight: 700; color: var(--text-main); line-height: 1; }
    
    .icon-box { width: 64px; height: 64px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 28px; }
    .icon-gold { background-color: #fef9c3; color: #ca8a04; }
    .icon-red { background-color: #fee2e2; color: #ef4444; }
    .icon-green { background-color: #dcfce7; color: #22c55e; }
    /* Warna baru untuk PR dan WO */
    .icon-orange { background-color: #fef3c7; color: #d97706; } 
    .icon-purple { background-color: #ede9fe; color: #7c3aed; } 
    
    /* Ubah grid grafik jadi 2 kolom (Kiri lebih lebar untuk chart, kanan untuk To-Do List) */
    .baris-grafik { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }
    .kartu-grafik { background-color: var(--white); padding: 24px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #f3f4f6; }
    .kartu-grafik h3 { font-size: 16px; margin-bottom: 20px; color: var(--text-main); border-bottom: 1px solid #f3f4f6; padding-bottom: 15px; }
    .wadah-gambar { width: 100%; height: 300px; background-color: var(--white); border-radius: 12px; display: flex; align-items: center; justify-content: center; position: relative; }
    .wadah-todo { width: 100%; height: 300px; background-color: #f9fafb; border-radius: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 2px dashed #e5e7eb; }
    
    @media (max-width: 1024px) {
        .baris-grafik { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('konten')
    <h1>Dashboard Overview</h1>
    <p class="subtitle">Monitoring data operasional perusahaan secara *real-time*.</p>

    <!-- KARTU KPI (DATA REAL-TIME) -->
    <div class="baris-kpi">
        <div class="kartu-kpi">
            <div class="kpi-teks">
                <h3>Total COA</h3>
                <p class="angka">{{ number_format($totalCoa ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="icon-box icon-gold"><i class="fas fa-certificate"></i></div>
        </div>
        
        <div class="kartu-kpi">
            <div class="kpi-teks">
                <h3>CAPA Terbuka</h3>
                <p class="angka" style="color: #ef4444;">{{ number_format($capaTerbuka ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="icon-box icon-red"><i class="fas fa-folder-open"></i></div>
        </div>
        
        <div class="kartu-kpi">
            <div class="kpi-teks">
                <h3>CAPA Selesai</h3>
                <p class="angka" style="color: #22c55e;">{{ number_format($capaSelesai ?? 0, 0, ',', '.') }}</p>
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
                <p class="angka" style="color: #7c3aed;">{{ number_format($woAktif ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="icon-box icon-purple"><i class="fas fa-industry"></i></div>
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
                    backgroundColor: 'rgba(239, 68, 68, 0.8)', // Merah
                    borderColor: '#ef4444',
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