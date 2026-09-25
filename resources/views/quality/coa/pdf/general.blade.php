<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CERTIFICATE OF ANALYSIS - {{ $coa->no_coa ?? ($savedCoa->no_coa ?? '-') }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #525659;
        }
        @page {
            size: A4;
            margin: 10mm 15mm;
        }
        .page-container {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 40px 50px;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            box-sizing: border-box;
        }

        .print-btn-wrapper { text-align: center; margin-bottom: 20px; }
        .btn-print { background: #4e73df; color: white; padding: 10px 20px; border: none; border-radius: 4px; font-size: 14px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block; }

        /* 1. Header Tanpa Grid Outer Box */
        .header-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .header-table td { vertical-align: top; border: none; padding: 0; }
        .header-left { font-weight: bold; font-size: 11px; line-height: 1.4; width: 30%; }
        .header-center { text-align: center; width: 40%; }
        .header-center .doc-title { font-size: 14px; font-weight: bold; text-transform: uppercase; }
        .header-center .doc-no { font-size: 11px; font-weight: bold; margin-top: 4px; }
        .header-right { text-align: right; font-size: 11px; line-height: 1.4; width: 30%; }

        /* 2. General Information Grid Table */
        .info-grid { width: 100%; border-collapse: collapse; border: 1.5px solid #000; margin-bottom: 12px; }
        .info-grid td { border: 1px solid #000; padding: 7px 10px; font-size: 11px; vertical-align: middle; }
        .info-grid-title { font-weight: bold; font-size: 11.5px; background-color: #fff; padding: 8px 10px !important; }
        .info-label { width: 220px; font-weight: bold; }
        .info-colon-val { width: auto; }

        .expire-note { font-size: 11px; font-weight: bold; margin-bottom: 20px; margin-top: 6px; }
        .expire-note span { font-weight: normal; }

        /* 3. Analysis Table */
        .analysis-table { width: 100%; border-collapse: collapse; border: 1.5px solid #000; margin-bottom: 18px; }
        .analysis-table th, .analysis-table td { border: 1px solid #000; padding: 8px 10px; font-size: 11px; }
        .analysis-table th { font-weight: bold; text-align: center; }
        .analysis-sec-title { text-align: left !important; font-size: 11.5px; padding: 8px 10px !important; }

        /* 4. Conclusion Box */
        .conclusion-box { border: 1.5px solid #000; padding: 10px 12px; font-size: 11px; margin-bottom: 15px; }
        
        /* 5. Remark & Signature Box */
        .remark-signature-box { border: 1.5px solid #000; padding: 12px 15px; min-height: 130px; position: relative; font-size: 11px; }
        .signature-wrapper { position: absolute; right: 15px; bottom: 12px; text-align: right; }
        .signature-group { display: inline-block; text-align: center; margin-left: 15px; vertical-align: bottom; }
        
        /* Box TTD dengan Nama di Dalamnya */
        .signature-box { 
            border: 1px solid #000; 
            width: 105px; 
            height: 65px; 
            margin-top: 4px; 
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding-bottom: 5px;
            box-sizing: border-box;
            font-size: 10px;
            font-weight: bold;
        }

        @media print {
            .no-print, .print-btn-wrapper { display: none !important; }
            .page-container { margin: 0; padding: 0; box-shadow: none; max-width: 100%; width: 100%; }
            body { background: transparent; -webkit-print-color-adjust: exact; }
        }
        
    </style>
</head>
<body>

    <div class="print-btn-wrapper no-print">
    <button onclick="window.print()" class="btn-print">🖨️ Cetak / Download PDF</button>
</div>

<div class="page-container">

    <!-- 1. HEADER SECTION -->
    <table class="header-table" style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
        <!-- Baris Top Header (Kiri & Kanan) -->
        <tr>
            <td class="header-left" style="width: 50%; vertical-align: top; text-align: left;">
                <strong>PT KIMPAI DYNA TUBE</strong><br>
                QA Department
            </td>
            <td class="header-right" style="width: 50%; vertical-align: top; text-align: right;">
                No. Dokumen : FRM/18-QA/07<br>
                No. Revisi : 01 (01.01.22)
            </td>
        </tr>
        <!-- Baris Judul COA + Garis Bawah -->
        <tr>
            <td colspan="2" class="header-center" style="text-align: center; padding-top: 15px; padding-bottom: 15px; border-bottom: 1px solid #000;">
                <div class="doc-title" style="font-weight: bold; font-size: 16px;">CERTIFICATE OF ANALYSIS</div>
                <div class="doc-no" style="font-weight: bold; font-size: 13px; margin-top: 4px;">
                    NO. : {{ $coa->no_coa ?? ($savedCoa->no_coa ?? ($autoNoCoa ?? 'COA/09/2026/0001')) }}
                </div>
            </td>
        </tr>
    </table>

    <!-- 2. GENERAL INFORMATION TITLE (DIBUNGKUS GARIS KOTAK) -->
    <div class="info-title-standalone" style="font-weight: bold; font-size: 13px; margin-top: 15px; margin-bottom: 12px; border: 1px solid #000; padding: 6px 10px; width: 100%; box-sizing: border-box;">
        General Information
    </div>

    <!-- 3. GENERAL INFORMATION TABLE -->
    <table class="info-grid" style="width: 100%; border-collapse: collapse; border: none;">
        <tr>
            <td class="info-label" style="border: none; padding: 4px 8px 4px 0; font-weight: bold; width: 25%;">Supplier Name</td>
            <td class="info-colon-val" style="border: none; padding: 4px 0;">: PT KIMPAI DYNA TUBE</td>
        </tr>
        <tr>
            <td class="info-label" style="border: none; padding: 4px 8px 4px 0; font-weight: bold;">Company Address</td>
            <td class="info-colon-val" style="border: none; padding: 4px 0;">: Jl. Jababeka IX Blok E No 9-17 Cikarang Utara Bekasi</td>
        </tr>
        <tr>
            <td class="info-label" style="border: none; padding: 4px 8px 4px 0; font-weight: bold;">Item Name</td>
            <td class="info-colon-val" style="border: none; padding: 4px 0;">: {{ $master->nama_material ?? ($coa->item_name ?? '-') }}</td>
        </tr>
        <tr>
            <td class="info-label" style="border: none; padding: 4px 8px 4px 0; font-weight: bold;">MM Number</td>
            <td class="info-colon-val" style="border: none; padding: 4px 0;">: {{ $coa->no_mm ?? ($savedCoa->no_mm ?? ($mm ?? '-')) }}</td>
        </tr>
        <tr>
            <td class="info-label" style="border: none; padding: 4px 8px 4px 0; font-weight: bold;">Item Code Customer</td>
            <td class="info-colon-val" style="border: none; padding: 4px 0;">: {{ $coa->item_code_customer ?? ($savedCoa->item_code_customer ?? '-') }}</td>
        </tr>
        <tr>
            <td class="info-label" style="border: none; padding: 4px 8px 4px 0; font-weight: bold;">PO Number</td>
            <td class="info-colon-val" style="border: none; padding: 4px 0;">: {{ $coa->po_number ?? ($savedCoa->po_number ?? '-') }}</td>
        </tr>
        <tr>
            <td class="info-label" style="border: none; padding: 4px 8px 4px 0; font-weight: bold;">Batch Number</td>
            <td class="info-colon-val" style="border: none; padding: 4px 0;">: {{ $coa->no_batch ?? ($savedCoa->no_batch ?? ($batch ?? '-')) }}</td>
        </tr>
        <tr>
            <td class="info-label" style="border: none; padding: 4px 8px 4px 0; font-weight: bold;">Cavity / Mandrel</td>
            <td class="info-colon-val" style="border: none; padding: 4px 0;">: {{ $coa->cavity_mandrel ?? '1 - 4 / 1 - 32' }}</td>
        </tr>
        <tr>
            <td class="info-label" style="border: none; padding: 4px 8px 4px 0; font-weight: bold;">Quantity</td>
            <td class="info-colon-val" style="border: none; padding: 4px 0;">: {{ isset($coa->delivery_quantity) ? number_format((float)$coa->delivery_quantity, 0, ',', '.') : ($savedCoa->delivery_quantity ?? '-') }}</td>
        </tr>
        <tr>
            <td class="info-label" style="border: none; padding: 4px 8px 4px 0; font-weight: bold;">Delivery Date</td>
            <td class="info-colon-val" style="border: none; padding: 4px 0;">: {{ isset($coa->delivery_date) ? \Carbon\Carbon::parse($coa->delivery_date)->format('m/d/Y') : ($savedCoa->delivery_date ?? '-') }}</td>
        </tr>
    </table>

        <div class="expire-note">
            Supplier Expire Date/Best Before Use Date: <span>1 Year after date of manufacture</span>
        </div>

        <!-- 3. ANALYSIS SECTION TABLE -->
        <table class="analysis-table">
            <thead>
                <tr>
                    <th colspan="5" class="analysis-sec-title">ANALYSIS SECTION</th>
                </tr>
                <tr>
                    <th style="width: 35%;">CRITICAL PROPERTY</th>
                    <th style="width: 12%;">UOM</th>
                    <th style="width: 18%;">MINIMUM</th>
                    <th style="width: 18%;">MAXIMUM</th>
                    <th style="width: 17%;">TEST RESULT (AVG)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $listParameters = $parameters ?? ($coa->details ?? (is_string($coa->parameters ?? null) ? json_decode($coa->parameters, true) : ($coa->parameters ?? [])));
                @endphp

                @forelse($listParameters as $param)
                    @php $item = (object) $param; @endphp
                    <tr>
                        <td style="text-align: left; padding-left: 8px;">{{ $item->nama_parameter ?? $item->parameter_name ?? '-' }}</td>
                        <td style="text-align: center;">{{ $item->uom ?? $item->satuan ?? '-' }}</td>
                        
                        @if(!empty($item->min_val) || !empty($item->min_value))
                            <td style="text-align: center;">{{ $item->min_val ?? $item->min_value }}</td>
                            <td style="text-align: center;">{{ $item->max_val ?? $item->max_value }}</td>
                        @else
                            <td colspan="2" style="text-align: center;">{{ $item->standar_text ?? $item->standar_teks ?? '-' }}</td>
                        @endif

                        <td style="text-align: center; font-weight: bold;">{{ $item->result_avg ?? $item->hasil ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 12px; color: #666;">Tidak ada data parameter inspeksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- 4. CONCLUSION BOX -->
        <div class="conclusion-box">
            <strong>CONCLUSION :</strong> &nbsp;&nbsp;&nbsp;&nbsp;
            [ <strong>{{ (isset($coa->status_decision) && $coa->status_decision == 'PASSED') ? 'v' : 'v' }}</strong> ] PASSED &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </div>

        <!-- 5. REMARK & SIGNATURE BOX -->
        <div class="remark-signature-box">
            <div style="font-weight: bold; margin-bottom: 6px;">Remark :</div>
            <div style="margin-bottom: 25px; font-size: 11px; white-space: pre-line;">{{ $coa->remark ?? ($savedCoa->remark ?? 'Catatan tambahan...') }}</div>

            <div style="font-style: italic; font-size: 10px; color: #333; margin-bottom: 12px;">
                *This is a computer-generated document, no signature is required.
            </div>

            <div style="font-weight: bold;">
                Quality Responsible person: {{ $coa->prepared_by ?? 'Miftakh' }} (Mr.)
            </div>

            @php
                // Cek ketersediaan library DNS2D
                $hasDns2d = class_exists('\Milon\Barcode\DNS2D');
                if ($hasDns2d) {
                    $d2 = new \Milon\Barcode\DNS2D();
                }
                
                // Variabel fallback untuk text QR code
                $prepByName = $coa->prepared_by ?? 'Miftakh';
                $apprByName = $coa->approved_by ?? 'Rajib';
                $docNo      = $coa->no_coa ?? '-';
            @endphp

            <!-- DUA KOLOM TANDA TANGAN DENGAN NAMA DI DALAM KOTAK -->
            <div class="signature-wrapper">
                
                <!-- KOLOM PREPARED BY -->
                <div class="signature-group">
                    <div style="margin-bottom: 5px; text-align: center;">Prepared By :</div>
                    <!-- class="signature-box" DIHAPUS dan ditambah border: none; -->
                    <div style="width: 90px; height: 90px; text-align: center; margin: 0 auto; padding-top: 5px; box-sizing: border-box; display: block; border: none;">
                        @php
                            $textPrepared = "Digital Signature Validation\n"
                                          . "Signer: " . $prepByName . "\n"
                                          . "Role: QA Department\n"
                                          . "Company: PT KIMPAI DYNA TUBE\n"
                                          . "Doc No: " . $docNo;
                        @endphp

                        @if($hasDns2d)
                            <img src="data:image/png;base64,{{ $d2->getBarcodePNG($textPrepared, 'QRCODE', 6, 6) }}" 
                                 alt="QR Prepared" 
                                 style="width: 50px; height: 50px; object-fit: contain; display: block; margin: 0 auto;" />
                        @else
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($textPrepared) }}" 
                                 width="50" height="50" alt="QR Prepared" 
                                 style="display: block; margin: 0 auto;" />
                        @endif
                        
                        <div style="display: block; font-size: 10px; margin-top: 5px; white-space: nowrap;">( {{ $prepByName }} )</div>
                    </div>
                </div>

                <!-- KOLOM APPROVED BY -->
                <div class="signature-group">
                    <div style="margin-bottom: 5px; text-align: center;">Approved By :</div>
                    <!-- class="signature-box" DIHAPUS dan ditambah border: none; -->
                    <div style="width: 90px; height: 90px; text-align: center; margin: 0 auto; padding-top: 5px; box-sizing: border-box; display: block; border: none;">
                        @php
                            $textApproved = "Digital Signature Validation\n"
                                          . "Signer: " . $apprByName . "\n"
                                          . "Role: QA Department\n"
                                          . "Company: PT KIMPAI DYNA TUBE\n"
                                          . "Doc No: " . $docNo;
                        @endphp

                        @if($hasDns2d)
                            <img src="data:image/png;base64,{{ $d2->getBarcodePNG($textApproved, 'QRCODE', 6, 6) }}" 
                                 alt="QR Approved" 
                                 style="width: 50px; height: 50px; object-fit: contain; display: block; margin: 0 auto;" />
                        @else
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($textApproved) }}" 
                                 width="50" height="50" alt="QR Approved" 
                                 style="display: block; margin: 0 auto;" />
                        @endif
                        
                        <div style="display: block; font-size: 10px; margin-top: 5px; white-space: nowrap;">( {{ $apprByName }} )</div>
                    </div>
                </div>

            </div>
        </div>

    </div>

</body>
</html>