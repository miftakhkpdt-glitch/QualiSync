@extends('layouts.staff-layout')

@section('title', 'Laporan Weekly Pareto FG')

@push('styles')
<style>
    /* PENGATURAN KHUSUS UNTUK MODE CETAK (PRINT) */
    @media print {
        body * { visibility: hidden; }
        #area-cetak, #area-cetak * { visibility: visible; }
        #area-cetak { position: absolute; left: 0; top: 0; width: 100%; padding: 0 !important; margin: 0 !important; }
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    }

    @media screen {
        .print-only { display: none !important; }
    }

    /* ==========================================================
       MODE PRESENTASI / FULLSCREEN (DIJAMIN HILANGKAN SIDEBAR)
       ========================================================== */
    
    /* 1. Sembunyikan elemen sidebar, nav, atau panel kiri berdasarkan berbagai kemungkinan nama kelas template */
    body.presentation-mode nav,
    body.presentation-mode aside,
    body.presentation-mode header,
    body.presentation-mode .sidebar,
    body.presentation-mode #sidebar,
    body.presentation-mode .main-sidebar,
    body.presentation-mode [class*="sidebar"],
    body.presentation-mode .no-print-presentation {
        display: none !important;
        width: 0 !important;
    }

    /* 2. Paksa area konten utama dan wrapper melebar penuh 100% tanpa sisa margin kiri */
    body.presentation-mode .content-wrapper,
    body.presentation-mode .main-content,
    body.presentation-mode .content,
    body.presentation-mode div[class*="content"] {
        margin-left: 0 !important;
        padding: 15px !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    body.presentation-mode #area-cetak {
        max-width: 100% !important;
        width: 100% !important;
    }
</style>
@endpush

@section('konten')
<div style="padding: 20px;" id="area-cetak">
    
    <!-- HEADER WEB & TOMBOL KONTROL -->
    <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <div>
            <h2 style="color: #1e293b; margin: 0;"><i class="fas fa-chart-pie" style="color: #8b5cf6;"></i> Laporan Defect (Pareto) per Line</h2>
            <p style="color: #64748b; font-size: 14px;">Analisis cacat produk In-Proses Finished Goods.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <!-- TOMBOL MODE PRESENTASI -->
            <button type="button" id="btnPresentation" onclick="togglePresentationMode()" style="background: #3b82f6; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold;">
                <i class="fas fa-expand" id="iconPresentation"></i> <span id="textPresentation">Mode Presentasi</span>
            </button>
            <a href="{{ url('/in-proses/fg/menu') }}" style="background: #64748b; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-size: 13px;">
                <i class="fas fa-arrow-left"></i> Kembali ke Menu
            </a>
        </div>
    </div>

    <!-- FILTER TANGGAL, LINE, ITEM, DAN SHIFT -->
<div class="no-print" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 25px; border-left: 4px solid #8b5cf6;">
    <form action="{{ route('in_proses.fg.laporan_weekly') }}" method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
        
        <!-- Dari Tanggal -->
        <div>
            <label style="font-weight: bold; font-size: 13px; display: block; margin-bottom: 5px;">Dari Tanggal</label>
            <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="form-control" style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;" required>
        </div>

        <!-- Sampai Tanggal -->
        <div>
            <label style="font-weight: bold; font-size: 13px; display: block; margin-bottom: 5px;">Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="form-control" style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;" required>
        </div>

        <!-- Filter Line -->
        <div>
            <label style="font-weight: bold; font-size: 13px; display: block; margin-bottom: 5px;">Line Produksi</label>
            <select name="line" class="form-control" style="padding: 9px; border: 1px solid #cbd5e1; border-radius: 4px; min-width: 140px;">
                <option value="">-- Semua Line --</option>
                @isset($listLines)
                    @foreach($listLines as $line)
                        <option value="{{ $line }}" {{ (isset($selectedLine) && $selectedLine == $line) ? 'selected' : '' }}>{{ $line }}</option>
                    @endforeach
                @endisset
            </select>
        </div>

        <!-- Filter Item / Material -->
        <div>
            <label style="font-weight: bold; font-size: 13px; display: block; margin-bottom: 5px;">Item / Material</label>
            <select name="item" class="form-control" style="padding: 9px; border: 1px solid #cbd5e1; border-radius: 4px; min-width: 180px;">
                <option value="">-- Semua Item --</option>
                @isset($listItems)
                    @foreach($listItems as $item)
                        <option value="{{ $item->no_mm ?? $item }}" {{ (isset($selectedItem) && $selectedItem == ($item->no_mm ?? $item)) ? 'selected' : '' }}>
                            {{ $item->nama_material ?? $item }}
                        </option>
                    @endforeach
                @endisset
            </select>
        </div>

        <!-- Filter Shift -->
        <div>
            <label style="font-weight: bold; font-size: 13px; display: block; margin-bottom: 5px;">Shift</label>
            <select name="shift" class="form-control" style="padding: 9px; border: 1px solid #cbd5e1; border-radius: 4px; min-width: 110px;">
                <option value="">-- Semua --</option>
                <option value="1" {{ (isset($selectedShift) && $selectedShift == '1') ? 'selected' : '' }}>Shift 1</option>
                <option value="2" {{ (isset($selectedShift) && $selectedShift == '2') ? 'selected' : '' }}>Shift 2</option>
                <option value="3" {{ (isset($selectedShift) && $selectedShift == '3') ? 'selected' : '' }}>Shift 3</option>
            </select>
        </div>

        <!-- Tombol Aksi -->
        <div>
            <button type="submit" style="background: #8b5cf6; color: white; padding: 9px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                <i class="fas fa-sync-alt"></i> Filter
            </button>
            <button type="button" onclick="window.print()" style="background: #10b981; color: white; padding: 9px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; margin-left: 5px;">
                <i class="fas fa-print"></i> Cetak
            </button>
        </div>

    </form>
</div>

    <!-- JUDUL CETAK -->
    <div class="print-only" style="text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px;">
        <h2 style="margin: 0; font-family: Arial, sans-serif; font-size: 22px;">LAPORAN MINGGUAN PARETO DEFECT - FINISHED GOODS</h2>
        <p style="margin: 5px 0 0 0; font-family: Arial, sans-serif; font-size: 14px;">
            Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s.d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
        </p>
    </div>

    @if(count($chartData) > 0)
        @foreach($chartData as $lineName => $data)
            @php 
                $chartId = 'chart-' . \Illuminate\Support\Str::slug($lineName);
            @endphp
            <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 40px; page-break-inside: avoid;">
                
                <!-- JUDUL -->
                <h4 style="text-align: center; margin-top: 0; color: #1e293b; font-weight: bold; font-family: Arial, sans-serif;">
                    Pareto Diagram: {{ $lineName }} <br>
                    <small style="color: #64748b; font-size: 14px; font-weight: normal;">
                        (Total Cacat: <strong>{{ $data['totalDefects'] }} Pcs</strong>) <br>
                        <span style="color: #ef4444; font-size: 13px;">
                            <i class="fas fa-exclamation-triangle"></i> 
                            Item Terbanyak Cacat: <strong>{{ $data['topItem'] }}</strong> ({{ $data['topItemQty'] }} Pcs)
                        </span>
                    </small>
                </h4>
                
                <!-- KANVAS GRAFIK -->
                <div style="position: relative; height: 45vh; width: 100%; margin-bottom: 30px;">
                    <canvas id="{{ $chartId }}"></canvas>
                </div>

                <!-- TABEL DATA -->
                <div style="overflow-x: auto; margin-top: 20px;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; font-family: Arial, sans-serif; border: 1px solid #e2e8f0;">
                        <thead>
                            <tr style="background-color: #f8fafc; border-top: 2px solid #cbd5e1; border-bottom: 2px solid #cbd5e1;">
                                <th style="padding: 10px; border: 1px solid #e2e8f0; text-align: center; width: 5%;">No</th>
                                <th style="padding: 10px; border: 1px solid #e2e8f0; width: 25%;">No MM & Nama Item (Material)</th>
                                <th style="padding: 10px; border: 1px solid #e2e8f0;">Jenis Cacat (Defect)</th>
                                <th style="padding: 10px; border: 1px solid #e2e8f0; text-align: center; width: 12%;">Jumlah (Pcs)</th>
                                <th style="padding: 10px; border: 1px solid #e2e8f0; text-align: center; width: 12%;">% Reject</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['details'] as $index => $detail)
                            <tr>
                                <td style="padding: 10px; border: 1px solid #e2e8f0; text-align: center;">{{ $index + 1 }}</td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0;">
                                    <strong>{{ $detail['no_mm'] }}</strong><br>
                                    <span style="color: #64748b; font-size: 12px;">{{ $detail['nama_material'] }}</span>
                                </td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0;">{{ $detail['defect'] }}</td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0; text-align: center;">{{ $detail['total_qty'] }}</td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0; text-align: center;">
                                    {{ $data['totalDefects'] > 0 ? round(($detail['total_qty'] / $data['totalDefects']) * 100, 2) : 0 }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #f1f5f9; font-weight: bold;">
                                <td colspan="3" style="padding: 10px; border: 1px solid #e2e8f0; text-align: right;">Total Keseluruhan:</td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0; text-align: center;">{{ $data['totalDefects'] }}</td>
                                <td style="padding: 10px; border: 1px solid #e2e8f0; text-align: center;">100%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        @endforeach
    @else
        <div style="background: #fef2f2; color: #dc2626; padding: 20px; border-radius: 8px; text-align: center;">
            <i class="fas fa-exclamation-circle" style="font-size: 24px; margin-bottom: 10px;"></i>
            <p style="margin: 0;">Tidak ada data defect (cacat) yang ditemukan pada rentang tanggal tersebut.</p>
        </div>
    @endif

</div>

<!-- SCRIPT CHART.JS & TOGGLE PRESENTASI -->
@if(count($chartData) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Fungsi untuk mengaktifkan / menonaktifkan Mode Presentasi (Sembunyikan Sidebar)
    function togglePresentationMode() {
        const body = document.body;
        body.classList.toggle('presentation-mode');
        
        const icon = document.getElementById('iconPresentation');
        const text = document.getElementById('textPresentation');
        const btn = document.getElementById('btnPresentation');

        if (body.classList.contains('presentation-mode')) {
            // Ubah tampilan tombol menjadi mode keluar
            icon.className = 'fas fa-compress';
            text.innerText = 'Keluar Presentasi';
            btn.style.background = '#ef4444'; // Warna merah tanda keluar
            
            // Masuk ke fullscreen browser otomatis (opsional agar lebih bersih)
            if (document.documentElement.requestFullscreen) {
                document.documentElement.requestFullscreen().catch(err => {});
            }
        } else {
            // Kembalikan ke tampilan semula
            icon.className = 'fas fa-expand';
            text.innerText = 'Mode Presentasi';
            btn.style.background = '#3b82f6'; // Warna biru semula
            
            // Keluar dari fullscreen browser
            if (document.fullscreenElement && document.exitFullscreen) {
                document.exitFullscreen().catch(err => {});
            }
        }

        // Trigger resize chart agar ukurannya menyesuaikan ulang secara otomatis
        window.dispatchEvent(new Event('resize'));
    }

    // Listener jika pengguna keluar mode presentation menggunakan tombol ESC di keyboard
    document.addEventListener('fullscreenchange', function() {
        if (!document.fullscreenElement && document.body.classList.contains('presentation-mode')) {
            togglePresentationMode();
        }
    });

    // Inisialisasi Chart.js
    document.addEventListener("DOMContentLoaded", function() {
        const allChartData = {!! json_encode($chartData) !!};
        
        Object.keys(allChartData).forEach(function(lineName) {
            const data = allChartData[lineName];
            const slugLineName = lineName.toLowerCase()
                                         .replace(/[^\w\s-]/g, '')
                                         .replace(/\s+/g, '-');
            const canvasId = 'chart-' + slugLineName;
            
            const canvasElement = document.getElementById(canvasId);
            if (!canvasElement) return;
            
            const ctx = canvasElement.getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Persentase Kumulatif (%)',
                            data: data.dataCumulative,
                            type: 'line', 
                            borderColor: '#ed7d31', 
                            backgroundColor: '#ed7d31',
                            borderWidth: 2,
                            pointBackgroundColor: '#ed7d31',
                            pointBorderColor: '#fff',
                            pointRadius: 4,
                            yAxisID: 'y-cumulative',
                            tension: 0 
                        },
                        {
                            label: 'Jumlah Defect (Qty)',
                            data: data.dataQty,
                            type: 'bar', 
                            backgroundColor: '#5b9bd5', 
                            borderColor: '#41719c',
                            borderWidth: 1,
                            yAxisID: 'y-qty',
                            maxBarThickness: 50 
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: { display: false }
                        },
                        'y-qty': {
                            type: 'linear',
                            position: 'left',
                            beginAtZero: true,
                            max: data.totalDefects > 0 ? data.totalDefects : 10, 
                            title: { display: true, text: 'Jumlah (Pcs)' }
                        },
                        'y-cumulative': {
                            type: 'linear',
                            position: 'right',
                            beginAtZero: true,
                            max: 100,
                            title: { display: true, text: 'Kumulatif (%)' },
                            grid: { display: false }, 
                            ticks: {
                                callback: function(value) {
                                    return value + '%'; 
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: { mode: 'index', intersect: false }
                    }
                }
            });
        });
    });
</script>
@endif
@endsection