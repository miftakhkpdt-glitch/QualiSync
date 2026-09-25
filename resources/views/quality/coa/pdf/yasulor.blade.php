<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>COA YASULOR - {{ $coa->no_coa }}</title>
    <style>
        /* --- 1. PENGATURAN UNTUK TAMPILAN DI LAYAR BROWSER --- */
        html {
            background-color: #525659;
        }
        
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 8pt; 
            color: #000; 
            line-height: 1.2; 
            
            /* MEMBUAT EFEK KERTAS A4 PORTRAIT DI LAYAR */
            max-width: 210mm;
            margin: 20px auto;
            padding: 10mm;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.5);
        }

        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 10px; }
        .header h3 { margin: 0; font-size: 14pt; text-transform: uppercase; }
        .header p { margin: 2px 0 0; font-size: 8.5pt; }

        .meta-grid { width: 100%; margin-bottom: 10px; border-collapse: collapse; }
        .meta-grid td { padding: 3px 4px; vertical-align: top; font-size: 8pt; }
        .meta-grid td.fw { font-weight: bold; width: 14%; } 

        .data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 15px; 
            table-layout: fixed; 
            word-wrap: break-word; 
        }
        .data-table th, .data-table td { 
            border: 1px solid #000; 
            padding: 4px 2px; 
            font-size: 7.5pt; 
            text-align: center; 
        }
        .data-table th { background-color: #e9ecef; font-weight: bold; text-transform: uppercase; }
        .data-table td.text-left { text-align: left; padding-left: 4px; }

        .footer-container { width: 100%; page-break-inside: avoid; }
        .ttd-table { width: 100%; border-collapse: collapse; text-align: center; margin-top: 10px; }
        .ttd-table td { width: 50%; vertical-align: bottom; height: 60px; }
        .ttd-line { border-bottom: 1px solid #000; width: 50%; margin: 0 auto 3px auto; }

        .no-print { margin-bottom: 15px; }
        
        /* --- 2. PENGATURAN KHUSUS SAAT TOMBOL PRINT DITEKAN --- */
        @media print { 
            @page { 
                size: A4 portrait; 
                margin: 10mm; 
            }
            html {
                background-color: #ffffff;
            }
            body {
                max-width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
            .no-print { display: none; } 
        }
    </style>
</head>
<body>

    <!-- TOMBOL CETAK -->
    <div class="no-print">
        <button onclick="window.print()" style="padding: 6px 12px; background: #198754; color: #fff; border: none; cursor: pointer; border-radius: 4px;">
            🖨️ Cetak / Download PDF (Portrait)
        </button>
    </div>
    
    <!-- HEADER DOKUMEN -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px; font-family: Arial, sans-serif; font-size: 10px; border: none;">
        <tr>
            <td style="text-align: left; vertical-align: top; border: none; padding: 0;">
                <strong style="font-size: 12px; color: #000;">PT KIMPAI DYNA TUBE</strong><br>
                <span style="color: #000;">QA Department</span>
            </td>
            <td style="text-align: right; vertical-align: top; border: none; padding: 0;">
                <span style="color: #000;">No. Dokumen : FRM/18-QA/07</span><br>
                <span style="color: #000;">No. Revisi : 01 (01.01.22)</span>
            </td>
        </tr>
    </table>

    <!-- HEADER YASULOR -->
    <div class="header">
        <h3>CERTIFICATE OF ANALYSIS</h3>
        <p>COA Ref No: <strong>{{ $coa->no_coa }}</strong></p>
    </div>

    <!-- METADATA EKSTRA YASULOR -->
    <table class="meta-grid">
        <tr>
            <td class="fw">Customer Name</td>
            <td>: {{ $coa->customer_name ?? 'PT YASULOR INDONESIA' }}</td>
            <td class="fw">Item Code Cust.</td>
            <td>: {{ $coa->item_code_customer ?? '-' }}</td>
            <td class="fw">Machine No.</td>
            <td>: {{ $coa->machine_no ?? '-' }}</td>
        </tr>
        <tr>
            <td class="fw">Material Name</td>
            <td>: {{ $master->nama_material ?? $master->nama_item ?? '-' }}</td>
            <td class="fw">PO Number</td>
            <td>: {{ $coa->po_number ?? '-' }}</td>
            <td class="fw">Cavity / Mandrel</td>
            <td>: {{ $coa->cavity_mandrel ?? '1-4/1-32' }}</td>
        </tr>
        <tr>
            <td class="fw">Material Code</td>
            <td>: {{ $coa->no_mm }}</td>
            <td class="fw">Delivery Qty</td>
            <td>: {{ $coa->delivery_quantity ? number_format($coa->delivery_quantity) . ' Pcs' : '-' }}</td>
            <td class="fw">Expire Date</td>
            <td>: {{ $coa->expire_date ?? '1 Year after date of manufacture' }}</td>
        </tr>
        <tr>
            <td class="fw">Batch No.</td>
            <td>: {{ $coa->no_batch }}</td>
            <td class="fw">Sample Qty DS</td>
            <td>: {{ $coa->sample_quantity ?? '-' }} Pcs</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <!-- TABEL PARAMETER KHUSUS YASULOR -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 17%;">Parameter</th>
                <th style="width: 19%;">Standard Specification</th>
                <th style="width: 11%;">Control Method</th>
                <th style="width: 7%;">Insp. Level</th>
                <th style="width: 5%;">AQL</th>
                <th style="width: 6%;">Freq.</th>
                <th style="width: 6%;">n Sampl</th>
                <th style="width: 8%;">Result</th>
                <th style="width: 8%;">Decision</th>
                <th style="width: 9%;">Remark</th>
            </tr>
        </thead>
        <tbody>
            @forelse($coa->details as $key => $detail)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td class="text-left">{{ $detail->nama_parameter }}</td>
                <td class="text-left">
                    @if(!empty($detail->standar_text) && $detail->standar_text != '0 - 0')
                        {{ $detail->standar_text }}
                    @elseif(!empty($detail->min_val) && !empty($detail->max_val))
                        {{ $detail->min_val }} - {{ $detail->max_val }} {{ $detail->uom ?? '' }}
                    @elseif(!empty($detail->min_val))
                        Min. {{ $detail->min_val }} {{ $detail->uom ?? '' }}
                    @elseif(!empty($detail->max_val))
                        Max. {{ $detail->max_val }} {{ $detail->uom ?? '' }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $detail->control_method ?? '-' }}</td>
                <td>{{ $detail->insp_level ?? '-' }}</td>
                <td>{{ $detail->aql ?? '-' }}</td>
                <td>{{ $detail->frequency ?? '-' }}</td>
                <td>{{ $detail->n_sampling ?? '-' }}</td>
                <td><strong>{{ $detail->result_avg ?? '-' }}</strong></td>
                <td>
                    <span style="font-weight: bold; color: {{ $detail->decision === 'OK' ? 'green' : 'red' }};">
                        {{ $detail->decision ?? 'OK' }}
                    </span>
                </td>
                <td>{{ $detail->remark ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="11">Tidak ada detail parameter.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- FOOTER & 2 TANDA TANGAN (SOLUSI PRAKTIS: QR TEKS) -->
    <div class="footer-container">
        <table style="width: 100%; font-size: 8pt;">
            <tr>
                <td style="width: 55%; vertical-align: top;">
    <strong>Decision:</strong> {{ $coa->status_decision }}<br>
    <strong>Box Quantity Note:</strong> {{ $coa->box_qty_note ?? '-' }}<br>
    <strong>General Note:</strong> {{ $coa->remark ?? '-' }}<br><br>
    <span style="font-style: italic; font-size: 9px; color: #333;">
        *This is a computer-generated document, no signature is required.
    </span>
</td>
                <td style="width: 45%;">
                    <table class="ttd-table" style="width: 100%; text-align: center;">
                        <tr>
                            <!-- PREPARED BY -->
                            <td style="vertical-align: top; width: 50%;">
                                <p style="margin-bottom: 5px;">Prepared By,</p>
                                
                                <div style="margin: 5px 0;">
                                    @php
                                        // Isi QR Code berupa teks validasi
                                        $textPrepared = "Digital Signature Validation\n"
                                                      . "Signer: " . $coa->prepared_by . "\n"
                                                      . "Role: QA Department\n"
                                                      . "Company: PT KIMPAI DYNA TUBE\n"
                                                      . "Doc No: " . $coa->no_coa;
                                                      
                                        $hasDns2d = class_exists('\Milon\Barcode\DNS2D');
                                        if ($hasDns2d) {
                                            $d2 = new \Milon\Barcode\DNS2D();
                                        }
                                    @endphp

                                    @if($hasDns2d)
                                        <img src="data:image/png;base64,{{ $d2->getBarcodePNG($textPrepared, 'QRCODE', 6, 6) }}" 
                                             alt="QR Prepared" 
                                             style="width: 70px; height: 70px; object-fit: contain; image-rendering: pixelated;" />
                                    @else
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($textPrepared) }}" 
                                             width="70" height="70" alt="QR Prepared" />
                                    @endif
                                </div>

                                <div class="ttd-line"></div>
                                <strong>{{ $coa->prepared_by }}</strong>
                            </td>

                            <!-- APPROVED BY -->
                            <td style="vertical-align: top; width: 50%;">
                                <p style="margin-bottom: 5px;">Approved By,</p>
                                
                                <div style="margin: 5px 0;">
                                    @php
                                        // Isi QR Code berupa teks validasi
                                        $textApproved = "Digital Signature Validation\n"
                                                      . "Signer: " . $coa->approved_by . "\n"
                                                      . "Role: QA Department\n"
                                                      . "Company: PT KIMPAI DYNA TUBE\n"
                                                      . "Doc No: " . $coa->no_coa;
                                    @endphp

                                    @if($hasDns2d)
                                        <img src="data:image/png;base64,{{ $d2->getBarcodePNG($textApproved, 'QRCODE', 6, 6) }}" 
                                             alt="QR Approved" 
                                             style="width: 70px; height: 70px; object-fit: contain; image-rendering: pixelated;" />
                                    @else
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($textApproved) }}" 
                                             width="70" height="70" alt="QR Approved" />
                                    @endif
                                </div>

                                <div class="ttd-line"></div>
                                <strong>{{ $coa->approved_by }}</strong>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>