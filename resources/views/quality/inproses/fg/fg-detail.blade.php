@extends('layouts.staff-layout')

@push('styles')
<style>
    /* =========================================
       STYLE UNTUK TOMBOL MODERN (TIDAK DICETAK)
       ========================================= */
    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto 20px auto;
    }
    
    .btn-modern {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.2s ease;
        text-decoration: none;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .btn-back {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }
    
    .btn-back:hover {
        background-color: #e2e8f0;
        color: #1e293b;
        text-decoration: none;
    }
    
    .btn-print {
        background-color: #3b82f6;
        color: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
    }
    
    .btn-print:hover {
        background-color: #2563eb;
        box-shadow: 0 6px 8px -1px rgba(59, 130, 246, 0.4);
        transform: translateY(-2px);
    }

    /* =========================================
       STYLE KHUSUS UNTUK DOKUMEN & CETAK
       ========================================= */
    .document-container {
        background-color: #ffffff;
        padding: 40px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        margin: 20px auto;
        max-width: 1200px;
        color: #000;
        font-family: 'Arial', sans-serif;
    }
    
    .doc-header-table {
        width: 100%;
        border-bottom: 2px solid #000;
        margin-bottom: 20px;
        padding-bottom: 10px;
    }
    
    .doc-title {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        text-decoration: underline;
        margin-bottom: 20px;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
        font-size: 14px;
    }
    
    .info-table td {
        padding: 4px 8px;
        vertical-align: top;
    }
    
    .info-table td:first-child {
        font-weight: bold;
        width: 130px;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        text-align: center;
    }
    
    .data-table th, .data-table td {
        border: 1px solid #000;
        padding: 8px;
    }
    
    .data-table th {
        background-color: #f8f9fa;
        font-weight: bold;
    }

    /* Hilangkan elemen yang tidak perlu saat di-print */
    @media print {
        body * {
            visibility: hidden;
        }
        .document-container, .document-container * {
            visibility: visible;
        }
        .document-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 0;
            box-shadow: none;
        }
        .no-print {
            display: none !important;
        }
        .navbar, .sidebar {
            display: none !important;
        }
    }
</style>
@endpush

@section('konten')
<!-- Tombol Aksi (Tidak ikut tercetak) -->
<div class="action-bar no-print">
    <a href="{{ url('/in-proses/fg/riwayat') }}" class="btn-modern btn-back">
        <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
    </a>
    <button onclick="window.print()" class="btn-modern btn-print">
        <i class="fas fa-print"></i> Cetak Laporan
    </button>
</div>

<div class="document-container">
    <!-- KOP SURAT / HEADER DOKUMEN -->
    <table class="doc-header-table">
        <tr>
            <td style="width: 60%;">
                <h4 style="margin: 0; font-weight: bold;">PT KIM PAI DYNA TUBE</h4>
                <p style="margin: 0; font-size: 14px;">QA Department</p>
            </td>
            <td style="width: 40%; text-align: right; font-size: 12px;">
                <table style="float: right;">
                    <tr><td>No. Dokumen</td><td>: FRM/18-QA/05</td></tr>
                    <tr><td>No. Revisi</td><td>: 06 (17.04.23)</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="doc-title">
        DAILY FINISH GOOD QUALITY INSPECTION RECORD
    </div>

    <!-- INFORMASI HEADER -->
    <div class="info-grid">
        <div>
            <table class="info-table">
                <tr><td>Tanggal</td><td>: {{ \Carbon\Carbon::parse($header->tanggal)->format('d-m-Y') }}</td></tr>
                <tr><td>No. Batch</td><td>: {{ $header->no_batch }}</td></tr>
                <tr><td>No. MM</td><td>: {{ $header->no_mm }}</td></tr>
                <tr><td>Item</td><td>: {{ $namaItem }}</td></tr>
            </table>
        </div>
        <div>
            <table class="info-table">
                <tr><td>Shift Kerja</td><td>: {{ $header->shift }}</td></tr>
                <tr><td>Line Produksi</td><td>: {{ $header->line_produksi }}</td></tr>
                <tr>
                    <td colspan="2" style="padding-top: 15px;">
                        Tipe Inspeksi : <br>
                        [ {{ $header->inspection_type == 'Normal Inspection' ? 'X' : ' ' }} ] Normal Inspection &nbsp;&nbsp;&nbsp;
                        [ {{ $header->inspection_type == 'Tightened Inspection' ? 'X' : ' ' }} ] Tightened Inspection
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- TABEL DETAIL TEMUAN -->
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2">Nama PIC<br><small>(QA Inspector)</small></th>
                <th rowspan="2">NO. BOX</th>
                <th rowspan="2">DEFECT</th>
                <th colspan="3">AQL (Jumlah Temuan)</th>
                <th rowspan="2">Decision<br>(OK/Reject)</th>
                <th colspan="3">Remark</th>
            </tr>
            <tr>
                <th>Critical<br><small>0.65</small></th>
                <th>Major<br><small>2.5</small></th>
                <th>Minor<br><small>4.1</small></th>
                <th>Jumlah Total</th>
                <th>Hasil Sortir OK</th>
                <th>Hasil Sortir NG</th>
            </tr>
        </thead>
        <tbody>
            @forelse($details as $row)
                <tr>
                    <td>{{ $row->pic }}</td>
                    <td>{{ $row->no_box ?: '-' }}</td>
                    <td>{{ $row->defect ?: '-' }}</td>
                    <td>{{ $row->critical }}</td>
                    <td>{{ $row->major }}</td>
                    <td>{{ $row->minor }}</td>
                    <td>
                        @if(strtoupper($row->decision) == 'REJECT')
                            <strong style="color: red;">REJECT</strong>
                        @else
                            <strong>OK</strong>
                        @endif
                    </td>
                    <td>{{ $row->jml_box ?: '-' }}</td>
                    <td>{{ $row->sortir_ok ?: '-' }}</td>
                    <td>{{ $row->sortir_ng ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="padding: 20px; font-style: italic;">
                        Tidak ada data detail temuan untuk inspeksi ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <!-- Kolom Tanda Tangan (Opsional, biasa ada di dokumen QA) -->
    <div style="margin-top: 50px; width: 100%; display: flex; justify-content: flex-end;">
        <div style="text-align: center; width: 200px;">
            <p style="margin-bottom: 60px;">Diperiksa Oleh,</p>
            <p style="text-decoration: underline; font-weight: bold; margin: 0;">( QA Staff )</p>
        </div>
    </div>

</div>
@endsection