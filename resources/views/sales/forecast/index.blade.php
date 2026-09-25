@extends('layouts.staff-layout')

@section('title', 'Daftar Forecast Customer - PT KIMPAI DYNA TUBE')

@section('konten')
<div style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 style="font-size: 24px; color: #334155; margin: 0;">
                <i class="fas fa-list-alt" style="color: #0ea5e9;"></i> Daftar Forecast Aktif
            </h2>
            <p style="color: #64748b; font-size: 14px; margin-top: 5px;">Pantauan target pesanan pelanggan per periode bulan.</p>
        </div>
        <a href="{{ route('sales.forecast.create') }}" style="background: #0ea5e9; color: white; padding: 10px 15px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 13px;">
            <i class="fas fa-plus"></i> Input Forecast Baru
        </a>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 4px; margin-bottom: 20px; font-weight: bold;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- TABEL FORECAST MENYAMPING (HORIZONTAL) -->
    <!-- STYLE KHUSUS TABEL FORECAST -->
    <style>
        .table-container {
            overflow-x: auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-top: 4px solid #0ea5e9; /* Warna aksen biru di atas */
        }
        .forecast-table {
            width: 100%;
            min-width: 1400px; /* Lebar minimum agar bulan tidak tergencet */
            border-collapse: separate; /* Memungkinkan efek sticky lebih mulus */
            border-spacing: 0;
            font-size: 13px;
        }
        .forecast-table th {
            background: #f8fafc;
            padding: 14px 10px;
            color: #334155;
            font-weight: 600;
            text-align: center;
            border-bottom: 2px solid #cbd5e1;
            white-space: nowrap; /* Mencegah teks turun ke baris baru */
        }
        .forecast-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            background: #fff;
        }
        /* Efek Sorot (Hover) pada baris */
        .forecast-table tbody tr:hover td {
            background-color: #f0f9ff !important; /* Warna biru sangat muda saat disorot */
            transition: background 0.2s;
        }
        /* Freeze Pane untuk Identitas */
        .col-sticky {
            position: sticky;
            left: 0;
            z-index: 2;
            border-right: 2px solid #e2e8f0;
            max-width: 350px;
            text-align: left !important;
        }
        .th-sticky {
            position: sticky;
            left: 0;
            z-index: 3;
            border-right: 2px solid #e2e8f0;
            text-align: left !important;
        }
        /* Bayangan tipis di sebelah kanan freeze pane */
        .col-sticky::after, .th-sticky::after {
            content: ''; position: absolute; right: -5px; top: 0; bottom: 0;
            width: 5px; box-shadow: inset 5px 0 5px -5px rgba(0,0,0,0.1); pointer-events: none;
        }
        /* Style untuk kolom bulan */
        .col-month {
            text-align: center !important;
            border-left: 1px dashed #e2e8f0;
            min-width: 85px;
        }
    </style>

    <!-- TABEL FORECAST MENYAMPING (HORIZONTAL) -->
    <div class="table-container">
        <table class="forecast-table">
            <thead>
                <tr>
                    <th class="th-sticky">Identitas Pelanggan & Produk</th>
                    <th>Tahun</th>
                    
                    @for($i = 1; $i <= 12; $i++)
                        <th>Bln {{ $i }}</th>
                    @endfor
                    
                    <th style="border-left: 2px solid #e2e8f0;">Versi / Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($groupedForecasts ?? [] as $key => $items)
                    @php
                        $info = explode('|||', $key);
                        $customer = $info[0];
                        $mm = $info[1];
                        $produk = $info[2];
                        $tahun = $info[3];
                        $versi = $info[4];

                        $qtyPerBulan = [];
                        foreach($items as $item) {
                            $qtyPerBulan[$item->bulan] = $item->qty_forecast;
                        }
                    @endphp
                    <tr>
                        <!-- Kolom Identitas (Freeze Kiri) -->
                        <td class="col-sticky">
                            <div style="font-weight: 700; color: #0f172a; font-size: 14px;">{{ $customer }}</div>
                            <div style="font-weight: 600; color: #0284c7; margin-top: 4px; line-height: 1.3;">{{ $produk }}</div>
                            <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                                <i class="fas fa-barcode"></i> MM: {{ $mm }}
                            </div>
                        </td>
                        
                        <!-- Kolom Tahun -->
                        <td style="text-align: center; font-weight: bold; color: #475569;">
                            {{ $tahun }}
                        </td>

                        <!-- Looping Angka Bulan 1 sampai 12 -->
                        @for($i = 1; $i <= 12; $i++)
                            <td class="col-month">
                                @if(isset($qtyPerBulan[$i]))
                                    <div style="font-weight: 700; color: #059669; font-size: 13px;">
                                        {{ number_format($qtyPerBulan[$i], 0, ',', '.') }}
                                        <span style="font-size: 10px; color: #94a3b8; font-weight: normal; margin-left: 2px;">Pcs</span>
                                    </div>
                                @else
                                    <div style="color: #cbd5e1; font-weight: bold;">-</div>
                                @endif
                            </td>
                        @endfor

                        <!-- Kolom Aksi -->
                        <td style="text-align: center; border-left: 2px solid #e2e8f0;">
                            <div style="margin-bottom: 8px;">
                                <span style="background: #e2e8f0; color: #475569; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold;">
                                    v{{ $versi }}
                                </span>
                            </div>
                            
                            <!-- Langsung ambil ID dari data pertama di barisan ini ($items->first()->id) -->
                            <a href="{{ route('sales.forecast.revisi', $items->first()->id) }}" style="display: inline-block; background: #f59e0b; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 11px; font-weight: bold; box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);">
                                <i class="fas fa-edit"></i> Revisi
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="15" style="text-align: center; padding: 40px; color: #94a3b8; font-style: italic; background: #fff;">
                            <i class="fas fa-folder-open" style="font-size: 32px; color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                            Belum ada data forecast yang diinput.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection