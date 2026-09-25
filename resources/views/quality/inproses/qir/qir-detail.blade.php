@extends('layouts.staff-layout')

@section('title', 'Cetak Dokumen QIR')

@push('styles')
<style>
    /* Styling khusus Print yang Rapi */
    .print-container { max-width: 1100px; margin: 20px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); font-family: 'Arial', sans-serif; }
    .header-section { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #1e293b; padding-bottom: 12px; margin-bottom: 20px; }
    .header-title { text-align: center; font-size: 20px; font-weight: bold; text-decoration: underline; flex-grow: 1; margin-top: 5px; }
    .doc-info { font-size: 11px; text-align: right; line-height: 1.4; }
    .company-name { font-weight: bold; font-size: 16px; letter-spacing: 0.5px; }
    
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 13px; margin-bottom: 20px; }
    .info-row { display: flex; margin-bottom: 6px; }
    .info-label { width: 110px; font-weight: bold; color: #334155; }
    
    /* Wrapper Tabel agar bisa digeser (scroll) khusus di web */
    .table-responsive-wrapper {
        width: 100%;
        overflow-x: auto;
        margin-bottom: 20px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
    }

    .table-data { width: 100%; border-collapse: collapse; font-size: 11px; text-align: center; }
    .table-data th, .table-data td { 
        border: 1px solid #1e293b; 
        padding: 6px 3px; 
        word-wrap: break-word;
    }
    
    .table-data th { 
        background-color: #f8fafc; 
        font-weight: bold; 
        color: #0f172a; 
        white-space: normal !important; 
        vertical-align: middle;
        line-height: 1.1;
        font-size: 10px;
    }

    .std-row { background-color: #fffbeb; font-size: 10px; font-weight: bold; color: #d97706; font-style: italic; white-space: normal !important; }
    
    .btn-action { display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px; border-radius: 6px; font-weight: bold; text-decoration: none; font-size: 14px; border: none; cursor: pointer; transition: 0.2s; }
    .btn-back { background-color: #64748b; color: white; }
    .btn-print { background-color: #eab308; color: white; }
    
    /* Aturan Print Agar Rata Tengah di Kertas */
    @media print {
        body * { visibility: hidden; }
        .print-container, .print-container * { visibility: visible; }
        
        .print-container { 
            position: absolute; 
            left: 50%;
            top: 0;
            transform: translateX(-50%) scale(0.90); /* Memposisikan tepat di tengah secara horizontal & sedikit mengecilkan agar muat */
            transform-origin: top center;
            width: 100%; 
            margin: 0; 
            padding: 5px; 
            box-shadow: none; 
            border: none;
        }
        
        .no-print { display: none !important; }
        .table-responsive-wrapper { overflow: visible !important; border: none !important; }
        
        @page { 
            size: A4 landscape; 
            margin: 5mm; 
        } 
    }
</style>
@endpush

@section('konten')
<div style="max-width: 1100px; margin: 0 auto 20px auto; display: flex; gap: 10px;" class="no-print">
    <a href="{{ url('/qir') }}" class="btn-action btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Riwayat</a>
    <button onclick="window.print()" class="btn-action btn-print"><i class="fas fa-print"></i> Cetak / Print Dokumen</button>
</div>

<div class="print-container">
    <!-- KOP SURAT -->
    <div class="header-section">
        <div>
            <div class="company-name">PT KIMPAI DYNA TUBE</div>
            <div style="font-size: 13px; color: #64748b;">QA Department</div>
        </div>
        <div class="header-title">QA INSPECTION RECORD</div>
        <div class="doc-info">
            No. Dokumen : FRM/18-QA/03<br>
            No. Revisi : 01 (09.09.21)
        </div>
    </div>

    <!-- IDENTITAS -->
    <div class="info-grid">
        <div>
            <div class="info-row"><div class="info-label">Tanggal</div><div>: {{ \Carbon\Carbon::parse($qir->tanggal)->format('d F Y') }}</div></div>
            <div class="info-row"><div class="info-label">No. Batch</div><div>: <strong>{{ $qir->no_batch }}</strong></div></div>
            <div class="info-row"><div class="info-label">No. MM</div><div>: {{ $qir->no_mm }}</div></div>
        </div>
        <div>
            <div class="info-row"><div class="info-label">Item Produk</div><div>: {{ $nama_item }}</div></div>
            <div class="info-row"><div class="info-label">Shift Kerja</div><div>: {{ $qir->shift }}</div></div>
            <div class="info-row"><div class="info-label">Line Produksi</div><div>: {{ $qir->line_produksi }}</div></div>
        </div>
    </div>

    <!-- TABEL DINAMIS DENGAN WRAPPER SCROLL -->
    <div class="table-responsive-wrapper">
        <table class="table-data">
            <thead>
                <tr>
                    <th style="width: 60px;">Sample</th>
                    
                    <!-- 1. LOOPING JUDUL KOLOM PARAMETER -->
                    @foreach($parameters as $param)
                        <th>
                            {{ $param->nama_parameter }} 
                            @if($param->satuan) <br><small style="font-weight:normal;">({{ $param->satuan }})</small> @endif
                        </th>
                    @endforeach
                </tr>
                
                <!-- 2. LOOPING BARIS STANDAR KUNING -->
                <tr class="std-row">
                    <td>Standard</td>
                    @foreach($parameters as $param)
                        @if($param->tipe_input === 'Angka')
                            <td>Min: {{ $param->min_value ?? '-' }} | Max: {{ $param->max_value ?? '-' }}</td>
                        @else
                            <td>{{ $param->standar_teks ?? '-' }}</td>
                        @endif
                    @endforeach
                </tr>
            </thead>
            
            <tbody>
                <!-- 3. LOOPING BARIS DATA SAMPEL -->
                @forelse($existingData as $sampleNo => $hasil_parameter)
                    <tr>
                        <td><strong>{{ $sampleNo }}</strong></td>
                        
                        @foreach($parameters as $param)
                            @php
                                $nilai = $hasil_parameter[$param->id] ?? '-';
                                $isNG = ($nilai === 'NG');
                            @endphp
                            <td style="{{ $isNG ? 'color: red; font-weight: bold;' : '' }}">
                                {{ $nilai }}
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($parameters) + 1 }}" style="padding: 30px; font-style: italic; color: #94a3b8;">
                            Data sampel pengujian tidak ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- TANDA TANGAN -->
    <div style="margin-top: 50px; display: flex; justify-content: flex-end; text-align: center;">
        <div style="width: 220px;">
            <p style="margin-bottom: 70px; font-weight: bold;">Inspector / QA</p>
            <p style="border-bottom: 1px solid #1e293b; margin: 0; padding-bottom: 5px; font-weight: bold;">
                {{ auth()->check() ? auth()->user()->name : '_________________________' }}
            </p>
            <p style="margin-top: 8px; font-size: 11px; color: #64748b;">Tanda Tangan & Nama Terang</p>
        </div>
    </div>
</div>
@endsection