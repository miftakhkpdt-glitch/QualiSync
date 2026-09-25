@extends('layouts.staff-layout')

@section('title', 'Certificate of Analysis - PT KIMPAI DYNA TUBE')

@push('styles')
<style>
    .coa-container { max-width: 900px; margin: 0 auto; background: #fff; padding: 30px; border: 1px solid #000; font-family: Arial, sans-serif; font-size: 12px; color: #000; }
    .header-table, .info-table, .analysis-table, .footer-table { width: 100%; border-collapse: collapse; }
    .header-table td { padding: 4px; vertical-align: top; }
    .title-section { text-align: center; font-weight: bold; font-size: 16px; margin: 15px 0; text-transform: uppercase; }
    
    .section-title { background: #f1f5f9; font-weight: bold; padding: 5px 8px; border: 1px solid #000; font-size: 12px; margin-top: 10px; }
    .info-table td { padding: 4px 6px; border: 1px solid #000; vertical-align: middle; }
    .info-label { width: 25%; font-weight: bold; }
    .info-val { width: 75%; }
    
    .analysis-table th, .analysis-table td { border: 1px solid #000; padding: 6px; text-align: center; font-size: 11px; }
    .analysis-table th { background: #f8fafc; font-weight: bold; text-transform: uppercase; }
    
    .box-container { border: 1px solid #000; padding: 8px; margin-top: 10px; }
    .form-input { width: 100%; padding: 4px 6px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 12px; box-sizing: border-box; }
    .form-input[readonly] { background-color: #f1f5f9; color: #334155; border: none; outline: none; }
    
    .btn-action-container { margin-bottom: 20px; display: flex; gap: 10px; justify-content: flex-end; }
    .btn-save { background: #10b981; color: white; padding: 8px 16px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; }
    .btn-print { background: #3b82f6; color: white; padding: 8px 16px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; }
    .btn-dashboard { background: #64748b; color: white; padding: 8px 16px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; }

    @media print {
        /* Hide everything by default outside the container if needed, but keeping flow is better */
        body { background: white; margin: 0; }
        .no-print { display: none !important; }
        
        /* Ensures table borders and backgrounds print correctly */
        * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        
        .coa-container { 
            border: none; 
            padding: 0; 
            margin: 0; 
            max-width: 100%; 
            box-shadow: none;
        }
    }
</style>
@endpush

@section('konten')

<div class="no-print btn-action-container">
    <a href="{{ url('/coa') }}" class="btn-dashboard"><i class="fas fa-home"></i> Dashboard COA</a>
    @if(!isset($savedCoa))
        <button type="submit" form="coaForm" class="btn-save"><i class="fas fa-save"></i> Simpan Data COA</button>
    @endif
    <button onclick="window.print()" class="btn-print"><i class="fas fa-print"></i> Cetak / Print PDF</button>
</div>

@if(session('success'))
    <div class="no-print" style="background: #d1fae5; color: #065f46; padding: 10px; border-radius: 6px; margin-bottom: 15px;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<div class="coa-container">
    <!-- Header Dokumen -->
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <strong>PT KIMPAI DYNA TUBE</strong><br>
                QA Department
            </td>
            <td style="width: 40%; text-align: right;">
                No. Dokumen : FRM/18-QA/07<br>
                No. Revisi &nbsp;&nbsp;&nbsp;&nbsp;: 01 (01.01.22)
            </td>
        </tr>
    </table>

    <div class="title-section">
        CERTIFICATE OF ANALYSIS<br>
        <span style="font-size: 13px; font-weight: normal;">No. : {{ $savedCoa->no_coa ?? $autoNoCoa }}</span>
    </div>

    <!-- Form & General Information -->
    <form id="coaForm" action="{{ url('/store-coa') }}" method="POST">
        @csrf
        <input type="hidden" name="no_batch" value="{{ $batch }}">
        <input type="hidden" name="no_mm" value="{{ $mm }}">

        <div class="section-title">General Information</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Supplier Name</td>
                <td class="info-val">: PT KIMPAI DYNA TUBE</td>
            </tr>
            <tr>
                <td class="info-label">Company Address</td>
                <td class="info-val">: Jl. Jababeka IX Blok E No 9-17 Cikarang Utara Bekasi</td>
            </tr>
            <tr>
                <td class="info-label">Item Name</td>
                <td class="info-val">: {{ $master->nama_material ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">MM Number</td>
                <td class="info-val">: {{ $mm }}</td>
            </tr>
            <tr>
                <td class="info-label">Item Code Customer</td>
                <td class="info-val">
                    : <input type="text" name="item_code_customer" class="form-input" value="{{ $savedCoa->item_code_customer ?? '' }}" placeholder="Masukkan Item Code Customer..." required {{ isset($savedCoa) ? 'readonly' : '' }} style="width: 90%; display: inline-block;">
                </td>
            </tr>
            <tr>
                <td class="info-label">PO Number</td>
                <td class="info-val">
                    : <input type="text" name="po_number" class="form-input" value="{{ $savedCoa->po_number ?? '' }}" placeholder="Masukkan No. PO..." {{ isset($savedCoa) ? 'readonly' : '' }} style="width: 90%; display: inline-block;">
                </td>
            </tr>
            <tr>
                <td class="info-label">Batch Number</td>
                <td class="info-val">: {{ $batch }}</td>
            </tr>
            <tr>
                <td class="info-label">Cavity / Mandrel</td>
                <td class="info-val">: 1 - 4 / 1 - 32</td>
            </tr>
            <tr>
                <td class="info-label">Quantity</td>
                <td class="info-val">
                    : <input type="text" name="quantity" class="form-input" value="{{ $savedCoa->quantity ?? '' }}" placeholder="Jumlah produk..." {{ isset($savedCoa) ? 'readonly' : '' }} style="width: 90%; display: inline-block;">
                </td>
            </tr>
            <tr>
                <td class="info-label">Delivery Date</td>
                <td class="info-val">
                    : <input type="date" name="delivery_date" class="form-input" value="{{ $savedCoa->delivery_date ?? date('Y-m-d') }}" {{ isset($savedCoa) ? 'readonly' : '' }} style="width: 200px; display: inline-block;">
                </td>
            </tr>
        </table>

        <div style="margin-top: 6px; font-size: 11px;">
            <strong>Supplier Expire Date/Best Before Use Date:</strong> 1 Year after date of manufacture
        </div>

        <!-- Analysis Section -->
        <div class="section-title" style="margin-top: 15px;">ANALYSIS SECTION</div>
        <table class="analysis-table">
            <thead>
                <tr>
                    <th style="width: 30%;">CRITICAL PROPERTY</th>
                    <th style="width: 15%;">UOM</th>
                    <th style="width: 15%;">MINIMUM</th>
                    <th style="width: 15%;">MAXIMUM</th>
                    <th style="width: 25%;">TEST RESULT (AVG)</th>
                </tr>
            </thead>
            <tbody>
                <!-- NOTE: Consider moving this @php calculation block to your Controller -->
                @php
    $paramResults = [];
    foreach($parameters as $param) {
        $total = 0;
        $count = 0;
        $status = 'OK';
        
        // Cek apakah tipe input mengandung kata 'Angka'
        $isAngka = str_contains($param->tipe_input, 'Angka');
        
        foreach($rekapData as $batchNo => $samples) {
            foreach($samples as $sampleNo => $hasil) {
                if(isset($hasil[$param->id])) {
                    $val = $hasil[$param->id];
                    
                    if($isAngka) {
                        $total += (float) $val;
                        $count++;
                    } else {
                        $count++;
                        if(strtoupper($val) === 'NG') {
                            $status = 'NG';
                        }
                    }
                }
            }
        }
        
        // Tampilkan hasil berdasarkan tipe
        if($isAngka) {
            $paramResults[$param->id] = $count > 0 ? number_format($total / $count, 2) : '-';
        } else {
            $paramResults[$param->id] = $count > 0 ? $status : 'OK';
        }
    }
@endphp

                @if(count($rekapData) > 0)
                    @foreach($parameters as $param)
                        <tr>
                            <td style="text-align: left; padding-left: 10px;">{{ $param->nama_parameter }}</td>
                            <td>{{ $param->satuan ?? '-' }}</td>
                            
                            @if($param->tipe_input === 'Angka')
                                <td>{{ $param->min_value ?? '-' }}</td>
                                <td>{{ $param->max_value ?? '-' }}</td>
                            @else
                                <td colspan="2" style="text-align: center;">{{ $param->standar_teks ?? '-' }}</td>
                            @endif
                            
                            <td style="{{ ($paramResults[$param->id] === 'NG') ? 'color:red; font-weight:bold;' : 'font-weight:bold;' }}">
                                {{ $paramResults[$param->id] }}
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" style="text-align: center; color: #64748b; padding: 15px;">Tidak ada data pengujian QIR yang ditemukan untuk Batch dan MM ini.</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Conclusion -->
        <div class="box-container" style="border-top: none;">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 15%; font-weight: bold;">CONCLUSION :</td>
                    <td style="width: 15%;">[&nbsp;<strong>v</strong>&nbsp;] PASSED</td>
                    <td style="width: 70%;">[&nbsp;&nbsp;&nbsp;] PASSED WITH NOTE: __________________________________________</td>
                </tr>
            </table>
        </div>

        <!-- Remark & Signature -->
        <div class="box-container" style="border-top: none; position: relative; min-height: 90px;">
            <div style="font-weight: bold; margin-bottom: 5px;">Remark :</div>
            <textarea name="remark" class="form-input" rows="2" placeholder="Catatan tambahan..." {{ isset($savedCoa) ? 'readonly' : '' }}>{{ $savedCoa->remark ?? '' }}</textarea>
            
            <div style="margin-top: 8px; font-style: italic; font-size: 11px; color: #334155;">
                *This is a computer-generated document, no signature is required.
            </div>

            <div style="position: absolute; right: 10px; bottom: 10px; text-align: center;">
                Signature :
                <div style="border: 1px solid #000; width: 80px; height: 60px; margin-top: 5px;"></div>
            </div>

            <div style="margin-top: 15px; font-weight: bold;">
                {{-- Suggestion: Make this dynamic based on the logged-in user --}}
                Quality Responsible person: {{ Auth::check() ? Auth::user()->name : 'Miftakh (Mr.)' }}
            </div>
        </div>
    </form>
</div>
@endsection