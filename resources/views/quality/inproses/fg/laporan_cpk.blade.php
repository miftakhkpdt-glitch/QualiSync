@extends('layouts.staff-layout')

@section('title', 'Laporan CPK Proses FG')

@push('styles')
<style>
    @media print {
        body * { visibility: hidden; }
        #area-cetak, #area-cetak * { visibility: visible; }
        #area-cetak {
            position: absolute; left: 0; top: 0; width: 100%;
            padding: 0 !important; margin: 0 !important;
        }
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
    @media screen {
        .print-only { display: none !important; }
    }
    
    .stat-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px;
        text-align: center;
        flex: 1;
        min-width: 100px;
    }
    .stat-title { font-size: 11px; font-weight: bold; color: #64748b; margin-bottom: 4px; text-transform: uppercase; }
    .stat-value { font-size: 16px; font-weight: bold; color: #0f172a; }

    .box-container {
        background: #f8fafc;
        padding: 12px 15px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        max-height: 140px;
        overflow-y: auto;
    }
    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        cursor: pointer;
        font-weight: 500;
        color: #334155;
        margin-bottom: 4px;
    }

    /* ==========================================================
       MODE PRESENTASI / FULLSCREEN (MENYEMBUNYIKAN SIDEBAR & HEADER)
       ========================================================== */
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('konten')
<div style="padding: 20px;" id="area-cetak">
    
    <!-- HEADER WEB -->
    <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <div>
            <h2 style="color: #1e293b; margin: 0;"><i class="fas fa-chart-line" style="color: #3b82f6;"></i> Analisis Kapabilitas Proses (CPK)</h2>
            <p style="color: #64748b; font-size: 14px;">Pemantauan Kestabilan Proses Berdasarkan Parameter & Pilihan Batch.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <!-- TOMBOL MODE PRESENTASI -->
            <button type="button" id="btnPresentation" onclick="togglePresentationMode()" style="background: #3b82f6; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold;">
                <i class="fas fa-expand" id="iconPresentation"></i> <span id="textPresentation">Mode Presentasi</span>
            </button>
            <a href="{{ url('/in-proses/fg/menu') }}" style="background: #64748b; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 5px;">
                <i class="fas fa-arrow-left"></i> Kembali ke Menu
            </a>
        </div>
    </div>

    <!-- FILTER FORM -->
    <div class="no-print" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 25px; border-left: 4px solid #3b82f6;">
        <form action="{{ url('/in-proses/fg/cpk') }}" method="GET" id="cpkForm">
            <div style="display: flex; gap: 15px; margin-bottom: 15px; flex-wrap: wrap;">
                <div>
                    <label style="font-weight: bold; font-size: 12px; display: block; margin-bottom: 5px;">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-control" style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;" required onchange="this.form.submit()">
                </div>
                <div>
                    <label style="font-weight: bold; font-size: 12px; display: block; margin-bottom: 5px;">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-control" style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;" required onchange="this.form.submit()">
                </div>
                <div style="flex-grow: 1; min-width: 250px;">
                    <label style="font-weight: bold; font-size: 12px; display: block; margin-bottom: 5px;">Pilih Produk (Finish Good)</label>
                    <select name="no_mm" class="form-control" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;" required onchange="this.form.submit()">
                        <option value="">-- Pilih Produk --</option>
                        @foreach($masterItems as $item)
                            <option value="{{ $item->no_mm }}" {{ (isset($no_mm) && $no_mm == $item->no_mm) ? 'selected' : '' }}>
                                {{ $item->no_mm }} - {{ $item->nama_material }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- GRID PILIHAN: BATCH (KIRI) & PARAMETER (KANAN) -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                
                <!-- KOLOM KIRI: FILTER NO BATCH DENGAN SEARCH -->
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                        <label style="font-weight: bold; font-size: 12px;">Pilih No. Batch (Default 5 Teratas)</label>
                        <input type="text" id="searchBatch" placeholder="Cari No. Batch..." style="padding: 3px 8px; font-size: 11px; border: 1px solid #cbd5e1; border-radius: 4px; width: 140px;" onkeyup="filterBatchList()">
                    </div>
                    <div class="box-container" id="batchCheckboxContainer">
                        @if(count($availableBatches) > 0)
                            @foreach($availableBatches as $index => $batch)
                                @php
                                    $isChecked = in_array($batch, $selectedBatches) || (empty($selectedBatches) && $index < 5);
                                @endphp
                                <label class="checkbox-item batch-label" data-batch="{{ strtolower($batch) }}">
                                    <input type="checkbox" name="batch_checkboxes[]" value="{{ $batch }}" {{ $isChecked ? 'checked' : '' }}>
                                    <span>{{ $batch }}</span>
                                    @if($index < 5) <small style="color: #0284c7; margin-left: auto;">(Terbaru)</small> @endif
                                </label>
                            @endforeach
                        @else
                            <div style="font-size: 12px; color: #94a3b8; text-align: center; padding: 20px;">Pilih produk terlebih dahulu untuk melihat daftar batch.</div>
                        @endif
                    </div>
                </div>

                <!-- KOLOM KANAN: PARAMETER UJI -->
                <div>
                    <label style="font-weight: bold; font-size: 12px; display: block; margin-bottom: 5px;">Pilih Parameter Uji (Bisa lebih dari 1)</label>
                    <div class="box-container">
                        @foreach($parameters as $param)
                            <label class="checkbox-item">
                                <input type="checkbox" name="parameter_ids[]" value="{{ $param->id }}" 
                                    {{ (isset($selectedParams) && in_array($param->id, $selectedParams)) ? 'checked' : '' }}>
                                {{ $param->nama_parameter }} @if($param->satuan) <small>({{ $param->satuan }})</small> @endif
                            </label>
                        @endforeach
                    </div>
                </div>

            </div>

            <div>
                <button type="submit" style="background: #3b82f6; color: white; padding: 9px 25px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                    <i class="fas fa-sync-alt"></i> Analisis Data
                </button>
                @if(count($multiCpkResults) > 0)
                    <button type="button" onclick="window.print()" style="background: #10b981; color: white; padding: 9px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; margin-left: 5px;">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </button>
                @endif
            </div>
        </form>
    </div>

    <!-- SCRIPT LIVE SEARCH & MODE PRESENTASI -->
    <script>
        function filterBatchList() {
            let input = document.getElementById('searchBatch').value.toLowerCase();
            let labels = document.getElementsByClassName('batch-label');
            
            for (let i = 0; i < labels.length; i++) {
                let batchText = labels[i].getAttribute('data-batch');
                if (batchText.includes(input)) {
                    labels[i].style.display = "flex";
                } else {
                    labels[i].style.display = "none";
                }
            }
        }

        function togglePresentationMode() {
            const body = document.body;
            body.classList.toggle('presentation-mode');
            
            const icon = document.getElementById('iconPresentation');
            const text = document.getElementById('textPresentation');
            const btn = document.getElementById('btnPresentation');

            if (body.classList.contains('presentation-mode')) {
                icon.className = 'fas fa-compress';
                text.innerText = 'Keluar Presentasi';
                btn.style.background = '#ef4444';
                
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen().catch(err => {});
                }
            } else {
                icon.className = 'fas fa-expand';
                text.innerText = 'Mode Presentasi';
                btn.style.background = '#3b82f6';
                
                if (document.fullscreenElement && document.exitFullscreen) {
                    document.exitFullscreen().catch(err => {});
                }
            }

            window.dispatchEvent(new Event('resize'));
        }

        document.addEventListener('fullscreenchange', function() {
            if (!document.fullscreenElement && document.body.classList.contains('presentation-mode')) {
                togglePresentationMode();
            }
        });
    </script>

    <!-- AREA HASIL ANALISIS -->
    @if(count($multiCpkResults) > 0)
        @foreach($multiCpkResults as $paramId => $data)
            <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; margin-bottom: 30px;">
                
                <h3 style="margin-top: 0; color: #1e293b; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
                    <span><i class="fas fa-vial" style="color: #3b82f6;"></i> Parameter: {{ $data['param_name'] }}</span>
                    <span style="font-size: 13px; background: {{ $data['color'] }}; color: white; padding: 4px 10px; border-radius: 4px;">
                        {{ $data['status'] }}
                    </span>
                </h3>

                <!-- KARTU STATISTIK -->
                <div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
                    <div class="stat-card">
                        <div class="stat-title">Sampel (n)</div>
                        <div class="stat-value">{{ $data['n'] }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title">Mean (&mu;)</div>
                        <div class="stat-value" style="color: #3b82f6;">{{ $data['mean'] }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title">Std.Dev (&sigma;)</div>
                        <div class="stat-value">{{ $data['stdDev'] }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title">USL / LSL</div>
                        <div class="stat-value" style="font-size: 14px;">{{ $data['usl'] }} / {{ $data['lsl'] }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title">Cp</div>
                        <div class="stat-value">{{ $data['cp'] }}</div>
                    </div>
                    <div class="stat-card" style="border: 2px solid {{ $data['color'] }}; background: #fff;">
                        <div class="stat-title" style="color: {{ $data['color'] }};">Cpk Index</div>
                        <div class="stat-value" style="color: {{ $data['color'] }}; font-size: 22px;">{{ $data['cpk'] }}</div>
                    </div>
                </div>

                <!-- KANVAS GRAFIK -->
                <div style="height: 320px;">
                    <canvas id="chart-{{ $paramId }}"></canvas>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const chartData = @json($data['chartData']);
                    const ctx = document.getElementById('chart-{{ $paramId }}').getContext('2d');
                    
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: chartData.labels,
                            datasets: [
                                {
                                    label: 'Aktual',
                                    data: chartData.data,
                                    borderColor: '#3b82f6',
                                    backgroundColor: '#3b82f6',
                                    borderWidth: 2,
                                    pointRadius: 3,
                                    tension: 0.1,
                                    order: 1
                                },
                                {
                                    label: 'USL',
                                    data: chartData.usl_line,
                                    borderColor: '#ef4444',
                                    borderWidth: 2,
                                    borderDash: [4, 4],
                                    pointRadius: 0,
                                    fill: false,
                                    order: 2
                                },
                                {
                                    label: 'LSL',
                                    data: chartData.lsl_line,
                                    borderColor: '#ef4444',
                                    borderWidth: 2,
                                    borderDash: [4, 4],
                                    pointRadius: 0,
                                    fill: false,
                                    order: 3
                                },
                                {
                                    label: 'Mean',
                                    data: chartData.mean_line,
                                    borderColor: '#10b981',
                                    borderWidth: 2,
                                    pointRadius: 0,
                                    fill: false,
                                    order: 4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom', labels: { boxWidth: 12 } }
                            },
                            scales: {
                                y: {
                                    suggestedMax: {{ $data['usl'] }} + ({{ $data['usl'] }} * 0.03),
                                    suggestedMin: {{ $data['lsl'] }} - ({{ $data['lsl'] }} * 0.03)
                                }
                            }
                        }
                    });
                });
            </script>
        @endforeach
    @else
        <div style="background: #fff; padding: 40px 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); text-align: center; border: 1px solid #e2e8f0;">
            <i class="fas fa-check-square" style="font-size: 48px; color: #cbd5e1; margin-bottom: 15px;"></i>
            <h3 style="color: #475569; margin-top: 0;">Silakan Tentukan Filter Analisis</h3>
            <p style="color: #64748b; max-width: 500px; margin: 0 auto;">
                Pilih produk, centang nomor batch (secara *default* 5 batch terbaru langsung tercentang), centang parameter uji, lalu klik <b>Analisis Data</b>.
            </p>
        </div>
    @endif

</div>
@endsection