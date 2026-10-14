@extends('layouts.staff-layout')

@section('title', 'Daftar Performa Supplier')

@section('konten')
<div style="padding: 20px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="color: #1e293b; margin: 0;"><i class="fas fa-chart-line" style="color: #0ea5e9;"></i> Data Performa Supplier ({{ $tahun }})</h2>
            <p style="color: #64748b; font-size: 14px;">Rekapitulasi penilaian kualitatif dan kuantitatif supplier.</p>
        </div>
        <a href="{{ route('quality.supplier_performance.create') }}" style="background: #10b981; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: bold;">
            <i class="fas fa-plus"></i> Input Penilaian Baru
        </a>
    </div>

    @if(session('error'))
        <div style="background: #fee2e2; color: #dc2626; padding: 10px; border-radius: 6px; margin-bottom: 15px;">
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 10px; border-radius: 6px; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- KOTAK FILTER CETAK -->
    <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 25px; border-left: 4px solid #f59e0b;">
        <h4 style="margin-top: 0;"><i class="fas fa-print"></i> Filter & Cetak Laporan</h4>
        <form action="{{ route('quality.supplier_performance.print') }}" method="POST" target="_blank">
            @csrf
            
            <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="font-weight: bold; font-size: 13px;">Tahun</label>
                    <input type="number" name="tahun" value="{{ $tahun }}" class="form-control" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div style="flex: 2;">
                    <label style="font-weight: bold; font-size: 13px;">Filter Supplier (Kosongkan untuk cetak semua)</label>
                    <select name="supplier_id" class="form-control" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        <option value="">-- Semua Supplier --</option>
                        @foreach($vendors as $v)
                            <option value="{{ $v->id }}">{{ $v->vendor_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <label style="font-weight: bold; font-size: 13px; display: block; margin-bottom: 5px;">Pilih Bulan (Bisa lebih dari satu untuk Semester/Kuartal):</label>
            <div style="display: flex; flex-wrap: wrap; gap: 15px; background: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0;">
                @php $listBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']; @endphp
                @foreach($listBulan as $bln)
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 5px;">
                        <input type="checkbox" name="bulan[]" value="{{ $bln }}" style="transform: scale(1.2);"> {{ $bln }}
                    </label>
                @endforeach
            </div>

            <button type="submit" style="margin-top: 15px; background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
                <i class="fas fa-print"></i> Cetak Laporan (PDF / Print)
            </button>
        </form>
    </div>

    <!-- GRAFIK PERFORMA SUPPLIER -->
    <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; flex-wrap: wrap; margin-bottom: 16px;">
            <div>
                <h3 style="margin: 0 0 6px; color: #1e293b; font-size: 18px;">
                    <i class="fas fa-chart-bar" style="color: #0ea5e9;"></i> Perbandingan Skor Performa Supplier
                </h3>
                <p style="margin: 0; color: #64748b; font-size: 13px;">
                    Perbandingan skor kuantitatif dan kualitatif per periode.
                </p>
            </div>
            <form action="{{ route('quality.supplier_performance.index') }}" method="GET" style="display: flex; align-items: flex-end; gap: 8px; flex-wrap: wrap;">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <input type="hidden" name="supplier_name" value="{{ request('supplier_name') }}">
                <div>
                    <label for="chart_supplier_id" style="display: block; font-weight: bold; font-size: 12px; margin-bottom: 5px;">Filter Supplier Grafik</label>
                    <select id="chart_supplier_id" name="chart_supplier_id" style="min-width: 220px; padding: 9px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        <option value="">-- Semua Supplier --</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ (string) request('chart_supplier_id') === (string) $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->vendor_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" style="background: #3b82f6; color: white; padding: 9px 14px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
                    <i class="fas fa-filter"></i> Terapkan
                </button>
                <a href="{{ route('quality.supplier_performance.index', ['tahun' => $tahun, 'supplier_name' => request('supplier_name')]) }}" style="background: #e2e8f0; color: #475569; padding: 9px 14px; border-radius: 6px; text-decoration: none; font-weight: bold;">
                    Reset Grafik
                </a>
            </form>
        </div>
        @if($chartPerformances->isNotEmpty())
            <div style="overflow-x: auto;">
                <div style="position: relative; height: 280px; width: {{ max(760, $chartPerformances->count() * 90) }}px;">
                    <canvas id="supplier-performance-chart" aria-label="Grafik perbandingan skor kuantitatif dan kualitatif supplier" role="img"></canvas>
                </div>
            </div>
        @else
            <div style="padding: 35px 15px; text-align: center; color: #64748b; background: #f8fafc; border-radius: 6px;">
                Tidak ada data penilaian untuk ditampilkan pada grafik.
            </div>
        @endif
    </div>

    <!-- FILTER DAFTAR PENILAIAN -->
    <div style="background: #fff; padding: 16px 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 20px;">
        <form action="{{ route('quality.supplier_performance.index') }}" method="GET" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <div style="flex: 1; min-width: 220px;">
                <label for="supplier_name" style="display: block; font-weight: bold; font-size: 13px; margin-bottom: 5px;">Filter Nama Supplier</label>
                <input type="text" id="supplier_name" name="supplier_name" value="{{ request('supplier_name') }}" placeholder="Cari nama supplier..." style="width: 100%; padding: 9px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>
            <button type="submit" style="background: #3b82f6; color: white; padding: 9px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
                <i class="fas fa-search"></i> Cari
            </button>
            <a href="{{ route('quality.supplier_performance.index', ['tahun' => $tahun]) }}" style="background: #e2e8f0; color: #475569; padding: 9px 16px; border-radius: 6px; text-decoration: none; font-weight: bold;">
                <i class="fas fa-sync-alt"></i> Reset
            </a>
        </form>
    </div>

    <!-- TABEL DATA -->
    <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
            <thead>
                <tr style="background: #f1f5f9; border-bottom: 2px solid #cbd5e1;">
                    <th style="padding: 10px;">Periode</th>
                    <th style="padding: 10px;">Supplier</th>
                    <th style="padding: 10px;">Skor Kuantitatif</th>
                    <th style="padding: 10px;">Status Kuantitatif</th>
                    <th style="padding: 10px;">Skor Kualitatif</th>
                    <th style="padding: 10px;">Status Kualitatif</th>
                    @if(strtolower(auth()->user()->role ?? '') === 'admin')
                        <th style="padding: 10px;">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($performances as $p)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 10px; font-weight: bold;">{{ $p->periode_bulan }} {{ $p->periode_tahun }}</td>
                    <td style="padding: 10px;">{{ $p->vendor_name }}</td>
                    <td style="padding: 10px;">{{ number_format($p->skor_kuantitatif, 2) }}</td>
                    <td style="padding: 10px;">
                        <span style="padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;
                            {{ $p->status_kuantitatif == 'EXCELLENCE' ? 'background: #dcfce7; color: #16a34a;' : ($p->status_kuantitatif == 'POOR' ? 'background: #fee2e2; color: #dc2626;' : 'background: #fef3c7; color: #d97706;') }}">
                            {{ $p->status_kuantitatif }}
                        </span>
                    </td>
                    <td style="padding: 10px;">{{ number_format($p->skor_kualitatif, 2) }}</td>
                    <td style="padding: 10px;">
                        <span style="padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;
                            {{ $p->status_kualitatif == 'EXCELLENCE' ? 'background: #dcfce7; color: #16a34a;' : ($p->status_kualitatif == 'POOR' ? 'background: #fee2e2; color: #dc2626;' : 'background: #fef3c7; color: #d97706;') }}">
                            {{ $p->status_kualitatif }}
                        </span>
                    </td>
                    @if(strtolower(auth()->user()->role ?? '') === 'admin')
                    <td style="padding: 10px;">
                        <form action="{{ route('quality.supplier_performance.destroy', $p->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Yakin ingin menghapus penilaian {{ $p->vendor_name }} untuk periode {{ $p->periode_bulan }} {{ $p->periode_tahun }}? Data yang dihapus tidak dapat dikembalikan.');">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="tahun" value="{{ $tahun }}">
                            <input type="hidden" name="supplier_name" value="{{ request('supplier_name') }}">
                            <button type="submit" style="background: #ef4444; color: #fff; border: 0; border-radius: 4px; padding: 7px 10px; cursor: pointer; font-weight: bold;">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="{{ strtolower(auth()->user()->role ?? '') === 'admin' ? 7 : 6 }}" style="text-align: center; padding: 20px;">Belum ada data di tahun ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection

@if($chartPerformances->isNotEmpty())
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const supplierChartLabels = @json($chartLabels).map(([month, supplier]) => {
                const lines = [month];
                let currentLine = '';

                supplier.split(/\s+/).forEach(word => {
                    if (currentLine && `${currentLine} ${word}`.length > 16) {
                        lines.push(currentLine);
                        currentLine = word;
                    } else {
                        currentLine = currentLine ? `${currentLine} ${word}` : word;
                    }
                });

                if (currentLine) lines.push(currentLine);
                return lines;
            });

            new Chart(document.getElementById('supplier-performance-chart'), {
                type: 'bar',
                data: {
                    labels: supplierChartLabels,
                    datasets: [
                        {
                            label: 'Skor Kuantitatif',
                            data: @json($chartQuantitativeScores),
                            backgroundColor: 'rgba(14, 165, 233, 0.75)',
                            borderColor: '#0284c7',
                            borderWidth: 1
                        },
                        {
                            label: 'Skor Kualitatif',
                            data: @json($chartQualitativeScores),
                            backgroundColor: 'rgba(245, 158, 11, 0.75)',
                            borderColor: '#d97706',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                stepSize: 20,
                                font: {
                                    size: 10
                                }
                            },
                            title: {
                                display: true,
                                text: 'Skor',
                                font: {
                                    size: 11
                                }
                            }
                        },
                        x: {
                            ticks: {
                                autoSkip: false,
                                font: {
                                    size: 9
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: context => `${context.dataset.label}: ${Number(context.raw).toFixed(2)}`
                            }
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        </script>
    @endpush
@endif