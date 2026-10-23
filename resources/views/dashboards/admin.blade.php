@extends('layouts.staff-layout')

@section('title', 'Dashboard Admin - PT KIMPAI DYNA TUBE')

@push('styles')
<style>
    .dashboard-admin {
        padding: 28px;
        border: 1px solid #eee8dd;
        border-radius: 28px;
        background: linear-gradient(145deg, #f4f8f1, #fff4eb 58%, #f5f0f8);
    }
    body:has(.dashboard-admin) .sidebar {
        background: linear-gradient(180deg, #f1f6ef, #e7efe8 52%, #f4eee5);
        color: #665b52;
        box-shadow: 5px 0 22px rgba(107, 91, 72, .10);
        border-right: 1px solid #e2e8dc;
    }
    body:has(.dashboard-admin) .sidebar-judul {
        padding: 25px 20px;
        border-bottom: 1px solid #e0e8dc;
        background: rgba(255, 253, 249, .72);
    }
    body:has(.dashboard-admin) .sidebar-judul h2 { color: #5b5048; }
    body:has(.dashboard-admin) .sidebar .menu-list { scrollbar-color: #c7d7c8 transparent; }
    body:has(.dashboard-admin) .sidebar .menu-list a {
        color: #70645b;
        border-radius: 12px;
        transition: background .18s ease, color .18s ease, transform .18s ease;
    }
    body:has(.dashboard-admin) .sidebar .menu-list a i { color: #9a8879; }
    body:has(.dashboard-admin) .sidebar .menu-list > li:first-child > a {
        background: #fffdf9;
        color: #665348;
        box-shadow: 0 4px 10px rgba(107, 91, 72, .08);
    }
    body:has(.dashboard-admin) .sidebar .menu-list > li:first-child > a i { color: #df987e; }
    body:has(.dashboard-admin) .sidebar .menu-list a:hover {
        background: #fff6ed;
        color: #665348;
        transform: translateX(2px);
    }
    body:has(.dashboard-admin) .sidebar .menu-list a:hover i { color: #cb8c72; }
    body:has(.dashboard-admin) .sidebar .sub-menu li a {
        color: #83766d;
        background: transparent;
    }
    body:has(.dashboard-admin) .sidebar .sub-menu li a:hover {
        color: #665348;
        background: #fff6ed;
    }
    .dashboard-admin h1 { font-size: 26px; color: #51443d; margin: 0 0 8px; }
    .dashboard-admin .subtitle { color: #887b73; font-size: 14px; margin: 0 0 24px; }
    .dashboard-admin .welcome-banner {
        background: linear-gradient(115deg, #f8dfd4, #fff0df 62%, #f7e7d6);
        color: #51443d;
        padding: 25px 30px;
        border-radius: 22px;
        box-shadow: 0 10px 24px rgba(107, 91, 72, .09);
        margin-bottom: 24px;
        border-left: 6px solid #e9947c;
    }
    .dashboard-admin .welcome-banner h2 {
        margin: 0 0 6px;
        font-size: 22px;
    }
    .dashboard-admin .welcome-banner p {
        margin: 0;
        font-size: 13px;
        color: #887b73;
        line-height: 1.5;
    }
    .dashboard-admin .welcome-banner .waktu-dashboard {
        display: block;
        margin-top: 8px;
        color: #74665d;
        font-size: 12px;
    }

    .dashboard-admin .baris-kpi {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }
    .dashboard-admin .kartu-kpi {
        min-height: 104px;
        box-sizing: border-box;
        background: #fffdf9;
        padding: 14px;
        border-radius: 20px;
        box-shadow: 0 8px 18px rgba(107, 91, 72, .07);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        border: 1px solid #efe7dc;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .dashboard-admin .kartu-kpi:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(107, 91, 72, .12);
    }
    .dashboard-admin .kpi-teks { min-width: 0; }
    .dashboard-admin .kpi-teks h3 {
        font-size: 11px;
        color: #82766c;
        font-weight: 600;
        line-height: 1.4;
        margin: 0 0 6px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .dashboard-admin .kpi-teks .angka {
        font-size: 28px;
        font-weight: 700;
        color: #51443d;
        line-height: 1;
        margin: 0;
    }

    .dashboard-admin .icon-box {
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
    }
    .dashboard-admin .icon-blue { background: #e4edf8; color: #6689ba; }
    .dashboard-admin .icon-red { background: #f8e1dc; color: #c87568; }
    .dashboard-admin .icon-green { background: #dceee5; color: #5a9278; }
    .dashboard-admin .icon-orange { background: #f8e8c9; color: #b88748; }
    .dashboard-admin .baris-kpi .kartu-kpi:nth-child(1) { background: #fffdf9; }
    .dashboard-admin .baris-kpi .kartu-kpi:nth-child(2) { background: #fff5f1; }
    .dashboard-admin .baris-kpi .kartu-kpi:nth-child(3) { background: #f3faf5; }
    .dashboard-admin .baris-kpi .kartu-kpi:nth-child(4) { background: #fff9ed; }
    .dashboard-admin .baris-kpi .kartu-kpi:nth-child(5) { background: #f2f7fc; }
    .dashboard-admin .baris-kpi .kartu-kpi:nth-child(2) .angka { color: #c87568 !important; }
    .dashboard-admin .baris-kpi .kartu-kpi:nth-child(3) .angka { color: #5a9278 !important; }
    .dashboard-admin .baris-kpi .kartu-kpi:nth-child(4) .angka { color: #b88748 !important; }
    .dashboard-admin .baris-kpi .kartu-kpi:nth-child(5) .angka { color: #6689ba !important; }

    .dashboard-admin .baris-grafik {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr);
        gap: 20px;
    }
    .dashboard-admin .kartu-grafik {
        min-width: 0;
        background: #fffdf9;
        padding: 20px;
        border-radius: 20px;
        box-shadow: 0 8px 18px rgba(107, 91, 72, .07);
        border: 1px solid #efe7dc;
    }
    .dashboard-admin .kartu-grafik h3 {
        font-size: 15px;
        margin: 0 0 16px;
        color: #51443d;
        border-bottom: 1px solid #eee7dc;
        padding-bottom: 12px;
    }
    .dashboard-admin .wadah-gambar {
        width: 100%;
        height: 300px;
        background: #fffdf9;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .dashboard-admin .wadah-todo {
        width: 100%;
        min-height: 300px;
        box-sizing: border-box;
        background: #f7faf6;
        border-radius: 15px;
        border: 1px dashed #d8e5da;
        padding: 8px 16px;
    }
    .dashboard-admin .daftar-tindakan {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .dashboard-admin .item-tindakan {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 0;
        border-bottom: 1px solid #eee7dc;
    }
    .dashboard-admin .item-tindakan:last-child { border-bottom: 0; }
    .dashboard-admin .link-tindakan {
        color: #51443d;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
    }
    .dashboard-admin .link-tindakan:hover { color: #c87862; }
    .dashboard-admin .jumlah-tindakan {
        min-width: 32px;
        padding: 5px 10px;
        border-radius: 999px;
        background: #e4edf8;
        color: #6689ba;
        font-size: 13px;
        font-weight: 700;
        text-align: center;
    }
    .dashboard-admin .tindakan-kosong {
        min-height: 260px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #82766c;
        font-size: 14px;
        text-align: center;
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
        .dashboard-admin { padding: 18px 12px; border-radius: 20px; }
        .dashboard-admin .baris-kpi { grid-template-columns: 1fr; }
        .dashboard-admin .kartu-kpi { min-height: 100px; }
        .dashboard-admin .kartu-grafik { padding: 16px; }
    }
</style>
@endpush

@section('konten')
<div class="dashboard-admin">
    <div class="welcome-banner">
        <h2>Halo, {{ $user->name }}! 👋</h2>
        <p>Selamat datang di Dashboard Admin. Semoga aktivitas hari ini berjalan lancar dan produktif!</p>
        <span class="waktu-dashboard">{{ $waktuDashboard }}</span>
    </div>

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
            <h3>Top 5 Defect Hasil Pemeriksaan Quality</h3>
            <p class="subtitle" style="margin-top: -8px; margin-bottom: 12px;">Berdasarkan jumlah temuan Critical, Major, dan Minor pada inspeksi produk jadi.</p>
            <div class="wadah-gambar">
                <canvas id="paretoChart"></canvas>
            </div>
        </div>
        
        <!-- TO-DO LIST / NOTIFIKASI -->
        <div class="kartu-grafik">
            <h3>Perlu Perhatian</h3>
            <div class="wadah-todo" aria-live="polite">
                @if (($capaTerbuka ?? 0) > 0 || ($prMenunggu ?? 0) > 0 || ($woAktif ?? 0) > 0)
                    <ul class="daftar-tindakan">
                        @if (($capaTerbuka ?? 0) > 0)
                            <li class="item-tindakan">
                                <a class="link-tindakan" href="{{ url('/capa-8d/customer') }}">CAPA customer terbuka</a>
                                <span class="jumlah-tindakan">{{ number_format($capaTerbuka, 0, ',', '.') }}</span>
                            </li>
                        @endif
                        @if (($prMenunggu ?? 0) > 0)
                            <li class="item-tindakan">
                                <a class="link-tindakan" href="{{ url('/purchasing/purchase-request') }}">Purchase request menunggu</a>
                                <span class="jumlah-tindakan">{{ number_format($prMenunggu, 0, ',', '.') }}</span>
                            </li>
                        @endif
                        @if (($woAktif ?? 0) > 0)
                            <li class="item-tindakan">
                                <a class="link-tindakan" href="{{ url('/ppic/work-order') }}">Work order sedang berjalan</a>
                                <span class="jumlah-tindakan">{{ number_format($woAktif, 0, ',', '.') }}</span>
                            </li>
                        @endif
                    </ul>
                @else
                    <div class="tindakan-kosong">
                        <i class="fas fa-inbox" style="font-size: 36px; color: #cbd5e1; margin-bottom: 12px;" aria-hidden="true"></i>
                        <p style="margin: 0;">Tidak ada item yang perlu ditindaklanjuti saat ini.</p>
                    </div>
                @endif
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

        // 2. Beri label informatif saat belum ada catatan reject
        if(chartLabels.length === 0) {
            chartLabels = ['Belum ada data'];
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
                    backgroundColor: 'rgba(233, 148, 124, 0.75)',
                    borderColor: '#df987e',
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