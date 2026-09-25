@extends('layouts.staff-layout')

@section('title', 'Dashboard Development - PT KIMPAI DYNA TUBE')

@push('styles')
<style>
    h1 { font-size: 24px; color: #1e293b; margin-bottom: 8px; }
    p.subtitle { color: #64748b; font-size: 14px; margin-bottom: 30px; margin-top: 0; }
    
    /* Grid KPI */
    .baris-kpi { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px; margin-bottom: 30px; }
    .kartu-kpi { background: #fff; padding: 24px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; border: 1px solid #f1f5f9; transition: transform 0.2s; }
    .kartu-kpi:hover { transform: translateY(-5px); }
    .kpi-teks h3 { font-size: 13px; color: #64748b; font-weight: 600; margin: 0 0 8px 0; text-transform: uppercase; }
    .kpi-teks .angka { font-size: 32px; font-weight: bold; color: #1e293b; margin: 0; }
    
    .icon-box { width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
    
    /* Grid Utama (Kiri Grafik, Kanan Pintasan) */
    .grid-utama { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }
    .kartu-panel { background: #fff; padding: 24px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; }
    .kartu-panel h3 { margin: 0 0 20px 0; font-size: 16px; color: #1e293b; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; }
    
    /* Tombol Pintasan */
    .menu-pintasan { display: flex; flex-direction: column; gap: 12px; }
    .btn-pintasan { display: flex; align-items: center; gap: 15px; padding: 15px; background: #f8fafc; border-radius: 10px; text-decoration: none; color: #334155; font-weight: 600; transition: 0.2s; border: 1px solid #e2e8f0; }
    .btn-pintasan:hover { background: #3b82f6; color: #fff; border-color: #3b82f6; }
    .btn-pintasan i { font-size: 20px; width: 30px; text-align: center; color: #3b82f6; transition: 0.2s; }
    .btn-pintasan:hover i { color: #fff; }

    @media (max-width: 1024px) {
        .grid-utama { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('konten')
    <h1><i class="fas fa-flask" style="color: #3b82f6; margin-right: 10px;"></i> Dashboard Riset & Development</h1>
    <p class="subtitle">Pantau progres proyek uji coba material dan pengelolaan data master pabrik.</p>

    <!-- 4 KARTU KPI DEVELOPMENT (REAL-TIME) -->
    <div class="baris-kpi">
        <div class="kartu-kpi" style="border-top: 4px solid #3b82f6;">
            <div class="kpi-teks">
                <h3>Total Master Material</h3>
                <!-- Mengambil data asli dari database -->
                <p class="angka">{{ number_format($totalMaterial ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="icon-box" style="background: #eff6ff; color: #3b82f6;"><i class="fas fa-boxes"></i></div>
        </div>
        
        <div class="kartu-kpi" style="border-top: 4px solid #f59e0b;">
            <div class="kpi-teks">
                <h3>Proyek Riset (Aktif)</h3>
                <!-- Menghitung proyek yang statusnya bukan 'Completed' atau 'Cancelled' dari variabel $projects -->
                <p class="angka">{{ $projects ? $projects->whereNotIn('status', ['Completed', 'Cancelled'])->count() : 0 }}</p>
            </div>
            <div class="icon-box" style="background: #fef3c7; color: #d97706;"><i class="fas fa-microscope"></i></div>
        </div>
        
        <div class="kartu-kpi" style="border-top: 4px solid #8b5cf6;">
            <div class="kpi-teks">
                <h3>BOM Draft / Review</h3>
                <!-- Sementara di-set 0 sampai Anda membuat tabel BOM nanti -->
                <p class="angka">0</p>
            </div>
            <div class="icon-box" style="background: #ede9fe; color: #7c3aed;"><i class="fas fa-clipboard-list"></i></div>
        </div>

        <div class="kartu-kpi" style="border-top: 4px solid #10b981;">
            <div class="kpi-teks">
                <h3>Uji Coba Berhasil (Bulan Ini)</h3>
                <!-- Sementara statis 0% sampai modul input hasil trial dibuat -->
                <p class="angka">0<span style="font-size: 16px;">%</span></p>
            </div>
            <div class="icon-box" style="background: #dcfce7; color: #15803d;"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>

    <!-- AREA GRAFIK & PINTASAN -->
    <div class="grid-utama">
        
        <!-- BAGIAN KIRI: GRAFIK PROYEK RISET -->
        <div class="kartu-panel">
            <h3>Statistik Uji Coba Material Baru (Trial)</h3>
            <div style="height: 300px; position: relative;">
                <canvas id="devChart"></canvas>
            </div>
        </div>
        
        <!-- BAGIAN KANAN: PINTASAN MENU MASTER -->
        <div class="kartu-panel">
            <h3><i class="fas fa-bolt" style="color: #f59e0b;"></i> Pintasan Master Data</h3>
            <div class="menu-pintasan">
                <!-- Sesuaikan URL href dengan route Anda -->
                <a href="{{ url('/development/master-material') }}" class="btn-pintasan">
                    <i class="fas fa-box-open"></i> Data Master Material (MM)
                </a>
                
                <a href="{{ url('/development/bom') }}" class="btn-pintasan">
                    <i class="fas fa-sitemap"></i> Bill of Materials (BOM)
                </a>
                
                <a href="{{ url('/development/master-reject') }}" class="btn-pintasan">
                    <i class="fas fa-times-circle"></i> Standar Master Reject
                </a>
                
                <a href="{{ url('/development/master-downtime') }}" class="btn-pintasan">
                    <i class="fas fa-clock"></i> Standar Master Downtime
                </a>
            </div>
        </div>
        
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('devChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                // Data dummy, nanti bisa ditarik dari Database
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [
                    {
                        label: 'Trial Sukses',
                        data: [4, 6, 5, 8, 7, 10],
                        backgroundColor: '#10b981',
                        borderRadius: 4
                    },
                    {
                        label: 'Trial Gagal / Revisi',
                        data: [2, 1, 3, 1, 2, 0],
                        backgroundColor: '#ef4444',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 2 } } }
            }
        });
    });
</script>
@endpush