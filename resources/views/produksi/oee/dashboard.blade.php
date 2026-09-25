@extends('layouts.staff-layout')

@section('title', 'Dashboard OEE Produksi')

@section('konten')
<!-- Import Library ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div style="padding: 30px; background-color: #f1f5f9; min-height: 100vh;">
    
    <!-- HEADER DASHBOARD & FILTER -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h2 style="font-size: 26px; font-weight: bold; color: #1e293b; margin: 0;">
                <i class="fas fa-chart-pie" style="color: #3b82f6; margin-right: 8px;"></i> Dashboard OEE
            </h2>
            <p style="color: #64748b; font-size: 15px; margin-top: 5px;">Rekapitulasi performa mesin, pencapaian target, dan tingkat cacat produk.</p>
        </div>

        <!-- FORM FILTER PERIODE -->
        <div style="background: white; padding: 10px 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
            <form action="{{ route('produksi.oee.dashboard') }}" method="GET" style="display: flex; align-items: center; gap: 10px; margin: 0;">
                <label style="font-weight: bold; color: #475569; font-size: 14px;"><i class="fas fa-calendar-alt text-blue-500"></i> Periode:</label>
                <select name="periode" onchange="this.form.submit()" style="padding: 8px 15px; border-radius: 5px; border: 1px solid #cbd5e1; outline: none; cursor: pointer; font-weight: bold; color: #1e293b; min-width: 150px;">
                    <option value="minggu" {{ $periode == 'minggu' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="bulan" {{ $periode == 'bulan' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="semester" {{ $periode == 'semester' ? 'selected' : '' }}>Semester Ini</option>
                    <option value="tahun" {{ $periode == 'tahun' ? 'selected' : '' }}>Tahun Ini</option>
                    <option value="semua" {{ $periode == 'semua' ? 'selected' : '' }}>Semua Waktu</option>
                </select>
            </form>
        </div>
    </div>

    <!-- METRIK UTAMA OEE -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; border-radius: 10px; padding: 25px; text-align: center; border-top: 5px solid #0ea5e9; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <h3 style="font-size: 14px; color: #64748b; margin: 0 0 10px 0;">AVAILABILITY</h3>
            <div style="font-size: 36px; font-weight: bold; color: #0ea5e9;">{{ number_format($availability, 1) }}%</div>
        </div>
        <div style="background: white; border-radius: 10px; padding: 25px; text-align: center; border-top: 5px solid #10b981; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <h3 style="font-size: 14px; color: #64748b; margin: 0 0 10px 0;">PERFORMANCE</h3>
            <div style="font-size: 36px; font-weight: bold; color: #10b981;">{{ number_format($performance, 1) }}%</div>
        </div>
        <div style="background: white; border-radius: 10px; padding: 25px; text-align: center; border-top: 5px solid #f59e0b; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <h3 style="font-size: 14px; color: #64748b; margin: 0 0 10px 0;">QUALITY</h3>
            <div style="font-size: 36px; font-weight: bold; color: #f59e0b;">{{ number_format($quality, 1) }}%</div>
        </div>
        <div style="background: #1e293b; border-radius: 10px; padding: 25px; text-align: center; border-top: 5px solid #ef4444; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
            <h3 style="font-size: 14px; color: #cbd5e1; margin: 0 0 10px 0;">FINAL OEE</h3>
            <div style="font-size: 42px; font-weight: bold; color: #fff;">{{ number_format($oee, 1) }}%</div>
        </div>
    </div>

    <!-- AREA GRAFIK TAHAP 1 -->
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px;">
        <!-- Pareto AISA -->
        <div style="background: white; border-radius: 5px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <h3 style="font-size: 15px; font-weight: bold; text-align: center; color: #475569; text-transform: uppercase;">Pareto Reject AISA</h3>
            <div id="chartAisa"></div>
        </div>

        <!-- Pareto COMBITOOL -->
        <div style="background: white; border-radius: 5px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <h3 style="font-size: 15px; font-weight: bold; text-align: center; color: #475569; text-transform: uppercase;">Pareto Reject Combitool</h3>
            <div id="chartCombi"></div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px;">
        <!-- Pareto Down Time -->
        <div style="background: white; border-radius: 5px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <h3 style="font-size: 15px; font-weight: bold; text-align: center; color: #475569; text-transform: uppercase;">Pareto Down Time</h3>
            <div id="chartDt"></div>
        </div>

        <!-- Machine Utilization (Donut) -->
        <div style="background: white; border-radius: 5px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <h3 style="font-size: 15px; font-weight: bold; text-align: center; color: #1e3a8a; text-transform: uppercase;">Machine Utilization</h3>
            <div id="chartUtil" style="display: flex; justify-content: center; align-items: center; height: 300px;"></div>
        </div>
    </div>

    <!-- AREA GRAFIK TAHAP 2 (TREND BULANAN & KPI OPERATOR) -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px;">
        <div style="background: white; border-radius: 5px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <h3 style="font-size: 15px; font-weight: bold; color: #1e3a8a; text-transform: uppercase;">Reject Rate Trend (%)</h3>
            <div id="chartTrendReject"></div>
        </div>
        <div style="background: white; border-radius: 5px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <h3 style="font-size: 15px; font-weight: bold; color: #1e3a8a; text-transform: uppercase;">Down Time Trend (Hr)</h3>
            <div id="chartTrendDt"></div>
        </div>
        <div style="background: white; border-radius: 5px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <h3 style="font-size: 15px; font-weight: bold; color: #1e3a8a; text-transform: uppercase;">Bad Printing Trend (%)</h3>
            <div id="chartTrendPrint"></div>
        </div>
    </div>

    <!-- Baris KPI Actual vs Target -->
    <div style="display: grid; grid-template-columns: 1fr; margin-bottom: 20px;">
        <div style="background: white; border-radius: 5px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <h3 style="font-size: 15px; font-weight: bold; text-align: center; color: #1e3a8a; text-transform: uppercase;">Actual vs Target (KPI) - Berdasarkan Operator</h3>
            <div id="chartKpi"></div>
        </div>
    </div>
</div>

<!-- SCRIPT KONFIGURASI GRAFIK -->
<script>
    // ==========================================
    // 1. FUNGSI TAHAP 1 (PARETO & DONUT)
    // ==========================================
    function getParetoOptions(dataLabels, dataQty, dataKumulatif, yAxisTitle) {
        return {
            series: [{ name: yAxisTitle, type: 'column', data: dataQty }, 
                     { name: 'Kumulatif %', type: 'line', data: dataKumulatif }],
            chart: { height: 320, type: 'line', toolbar: { show: false } },
            stroke: { width: [0, 4] },
            colors: ['#0f172a', '#ef4444'], // Biru Gelap (Bar) & Merah (Garis)
            xaxis: { 
                categories: dataLabels,
                labels: { style: { fontSize: '10px' }, rotate: -45, trim: true, hideOverlappingLabels: false }
            },
            yaxis: [
                { title: { text: yAxisTitle } }, 
                { opposite: true, min: 0, max: 100, tickAmount: 5, labels: { formatter: function (val) { return val + "%"; } } }
            ],
            dataLabels: {
                enabled: true,
                enabledOnSeries: [0, 1],
                formatter: function (val, opts) {
                    if(opts.seriesIndex === 1) return val + "%";
                    return val;
                },
                offsetY: -10,
                style: { fontSize: '10px', colors: ['#333'] },
                background: { enabled: false }
            },
            legend: { show: false }
        };
    }

    new ApexCharts(document.querySelector("#chartAisa"), getParetoOptions({!! json_encode($paretoAisa['labels']) !!}, {!! json_encode($paretoAisa['qty']) !!}, {!! json_encode($paretoAisa['kumulatif']) !!}, 'Qty Reject')).render();
    new ApexCharts(document.querySelector("#chartCombi"), getParetoOptions({!! json_encode($paretoCombi['labels']) !!}, {!! json_encode($paretoCombi['qty']) !!}, {!! json_encode($paretoCombi['kumulatif']) !!}, 'Qty Reject')).render();
    new ApexCharts(document.querySelector("#chartDt"), getParetoOptions({!! json_encode($paretoDt['labels']) !!}, {!! json_encode($paretoDt['qty']) !!}, {!! json_encode($paretoDt['kumulatif']) !!}, 'Menit')).render();

    var utilOptions = {
        series: [{{ round($availability, 1) }}, {{ round(100 - $availability, 1) }}],
        labels: ['Utilized', 'Downtime/Idle'],
        chart: { type: 'donut', height: 300 },
        colors: ['#f8b486', '#e6f0d8'],
        dataLabels: { 
            enabled: true, 
            formatter: function (val, opts) {
                if(opts.seriesIndex === 0) return val.toFixed(1) + "%";
                return ""; 
            },
            style: { fontSize: '24px', fontWeight: 'bold', colors: ['#994914'] },
            dropShadow: { enabled: false }
        },
        plotOptions: { pie: { donut: { size: '0%' } } }, 
        legend: { show: false }
    };
    new ApexCharts(document.querySelector("#chartUtil"), utilOptions).render();

    // ==========================================
    // 2. FUNGSI TAHAP 2 (TREND BULANAN & KPI)
    // ==========================================
    
    function getLineOptions(dataSeries, categories, yAxisTitle, targetValue) {
        return {
            series: [{ name: yAxisTitle, data: dataSeries }],
            chart: { type: 'line', height: 250, toolbar: { show: false } },
            colors: ['#0f172a'],
            stroke: { width: 3 },
            markers: { size: 5, colors: ['#0f172a'] }, 
            dataLabels: { 
                enabled: true, offsetY: -5,
                style: { colors: ['#333'], fontSize: '10px' },
                background: { enabled: false } 
            },
            xaxis: { categories: categories },
            yaxis: { labels: { formatter: function(val) { return val.toFixed(1); } } },
            annotations: {
                yaxis: [{
                    y: targetValue,
                    borderColor: '#ef4444', 
                    strokeDashArray: 5, 
                    borderWidth: 2
                }]
            }
        };
    }

    new ApexCharts(document.querySelector("#chartTrendReject"), getLineOptions({!! json_encode($trendRejectRate) !!}, {!! json_encode($trendMonths) !!}, 'Reject %', 4.0)).render();
    new ApexCharts(document.querySelector("#chartTrendDt"), getLineOptions({!! json_encode($trendDowntimeHr) !!}, {!! json_encode($trendMonths) !!}, 'Dt (Hr)', 50.0)).render();
    new ApexCharts(document.querySelector("#chartTrendPrint"), getLineOptions({!! json_encode($trendBadPrinting) !!}, {!! json_encode($trendMonths) !!}, 'Bad Print %', 3.0)).render();

    var kpiOptions = {
        series: [{ name: 'Pencapaian (%)', data: {!! json_encode($kpiValues) !!} }],
        chart: { type: 'bar', height: 350, toolbar: { show: false } },
        plotOptions: {
            bar: { 
                horizontal: true, 
                barHeight: '30%',
                dataLabels: { position: 'right' },
                colors: {
                    backgroundBarColors: ['#e2e8f0'], 
                    backgroundBarOpacity: 1,
                    backgroundBarRadius: 0,
                }
            }
        },
        colors: ['#0f172a'], 
        dataLabels: {
            enabled: true, textAnchor: 'start', offsetX: 10,
            formatter: function (val) { return val + "%"; },
            style: { colors: ['#333'], fontSize: '11px' }
        },
        xaxis: { categories: {!! json_encode($kpiLabels) !!} },
        yaxis: { labels: { style: { fontSize: '11px', colors: '#333', fontWeight: 'bold' } } },
    };
    new ApexCharts(document.querySelector("#chartKpi"), kpiOptions).render();
</script>
@endsection