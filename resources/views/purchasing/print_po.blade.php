<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak PO - {{ $po->no_pr ?? $po->id }}</title>
    <style>
        /* CSS khusus untuk format cetak kertas (A4) */
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.5; margin: 0; padding: 20px; font-size: 14px; position: relative; }
        .print-container { max-width: 800px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; position: relative; z-index: 2; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #2c3e50; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #2c3e50; font-size: 24px; text-transform: uppercase; }
        .company-info { font-size: 12px; color: #555; }
        .po-details { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .vendor-box, .info-box { width: 48%; padding: 15px; background-color: #f9f9f9; border: 1px solid #eee; box-sizing: border-box; }
        .vendor-box h4, .info-box h4 { margin-top: 0; border-bottom: 1px solid #ccc; padding-bottom: 5px; color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #333; padding: 10px; text-align: left; }
        th { background-color: #2c3e50; color: white; text-align: center; }
        .total-row td { font-weight: bold; background-color: #f9f9f9; }
        .signatures { display: flex; justify-content: space-between; margin-top: 50px; text-align: center; }
        
        /* Width dihapus dari sini karena akan diatur dinamis via PHP */
        .sign-box { font-size: 12px; } 
        .sign-box span { border-top: 1px solid #333; display: block; padding-top: 5px; font-weight: bold; }
        
        /* CSS untuk Watermark Belum ACC */
        .watermark {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 60px;
            font-weight: bold;
            color: rgba(220, 38, 38, 0.15); /* Merah transparan */
            z-index: 1;
            pointer-events: none;
            white-space: nowrap;
            text-align: center;
        }

        /* CSS untuk Stempel Digital */
        .stempel-acc {
            color: #166534;
            border: 3px solid #166534;
            border-radius: 8px;
            padding: 5px;
            font-weight: bold;
            font-size: 14px;
            transform: rotate(-10deg);
            display: inline-block;
            margin: 10px 0;
            opacity: 0.8;
        }
        
        @media print {
            @page { size: A4 portrait; margin: 1cm; }
            .print-container { border: none; padding: 0; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()"> 

    <!-- LOGIKA DETEKSI KELULUSAN APPROVAL -->
    @php
        $isManagerACC = ($po->approval_manager_plan == 'Approved');
        $isDirekturACC = ($po->approval_direktur == 'Approved');
        $isPresdirACC = ($po->approval_presdir == 'Approved' || $po->approval_presdir == 'Tidak Perlu');
        
        // Dikatakan LULUS jika ketiga syarat di atas terpenuhi
        $isFullyApproved = ($isManagerACC && $isDirekturACC && $isPresdirACC);
    @endphp

    <!-- WATERMARK AKAN MUNCUL JIKA BELUM LULUS APPROVAL -->
    @if(!$isFullyApproved)
        <div class="watermark">
            DRAFT / BELUM ACC<br>
            <span style="font-size: 30px;">TIDAK SAH UNTUK SUPPLIER</span>
        </div>
    @endif
    
    <div class="print-container">
        <!-- KOP SURAT -->
        <div class="header">
            <div>
                <h1>PURCHASE ORDER</h1>
                <div class="company-info">
                    <strong>PT KIMPAI DYNA TUBE</strong><br>
                    Kawasan Industri, Surakarta, Jawa Tengah<br>
                    Telp: (0271) 1234567 | Email: purchasing@kimpai.co.id
                </div>
            </div>
            <div style="text-align: right;">
                <h3 style="margin:0; color:#e74c3c;">No. Dok: PO-{{ date('Ymd', strtotime($po->tanggal_po)) }}-{{ $po->id }}</h3>
                <p style="margin:5px 0 0 0;">Tanggal: {{ date('d F Y', strtotime($po->tanggal_po)) }}</p>
            </div>
        </div>

        <!-- INFO VENDOR & PO -->
        <div class="po-details">
            <div class="vendor-box">
                <h4>Yth.:</h4>
                <strong>{{ $po->vendor_name }}</strong><br>
                Alamat: {{ $po->address ?? 'Alamat belum dilengkapi di database' }}<br>
                Telp: {{ $po->no_telp ?? '-' }}<br>
            
            </div>
            
            <div class="info-box">
                <h4>Informasi Pengiriman:</h4>
                <strong>Estimasi Tiba (ETA):</strong> {{ $po->eta ? date('d F Y', strtotime($po->eta)) : 'Menunggu Konfirmasi' }}<br>
                <strong>Referensi PR:</strong> {{ $po->no_pr ?? 'PO Direct' }}<br>
                <strong>Catatan PO:</strong> {{ $po->keterangan ?? 'Sesuai kesepakatan standar' }}
            </div>
        </div>

        <!-- LOGIKA MATEMATIKA -->
        @php
            $qty = $po->qty ?? 0;
            $harga_satuan = $po->harga_satuan ?? 0;
            $subtotal = $qty * $harga_satuan;
            $diskon_persen = $po->diskon ?? 0;
            $nominal_diskon = $subtotal * ($diskon_persen / 100);
            $dpp = $subtotal - $nominal_diskon;
            $ppn_persen = $po->ppn ?? 0;
            $nominal_ppn = $dpp * ($ppn_persen / 100);
            $pph_persen = $po->pph ?? 0;
            $nominal_pph = $dpp * ($pph_persen / 100);
            $additional_charge = $po->additional_charge ?? 0;
            $grand_total = $po->total_nilai ?? ($dpp + $nominal_ppn - $nominal_pph + $additional_charge);
        @endphp

        <!-- TABEL BARANG -->
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="45%">Deskripsi Barang (Material)</th>
                    <th width="15%">Kuantitas</th>
                    <th width="15%">Harga Satuan</th>
                    <th width="20%">Total Harga</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>
                        <strong>{{ $po->no_mm }}</strong><br>
                        {{ $po->nama_material ?? 'Item Material' }}
                    </td>
                    <td style="text-align: center;">{{ number_format($qty, 2, ',', '.') }} {{ $po->satuan }}</td>
                    <td style="text-align: right;">Rp {{ number_format($harga_satuan, 0, ',', '.') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>

                <tr class="total-row">
                    <td colspan="4" style="text-align: right;">SUBTOTAL</td>
                    <td style="text-align: right;">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>

                @if($diskon_persen > 0)
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;">Diskon ({{ $diskon_persen }}%)</td>
                    <td style="text-align: right; color: #e74c3c;">- Rp {{ number_format($nominal_diskon, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($ppn_persen > 0)
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;">PPN ({{ $ppn_persen }}%)</td>
                    <td style="text-align: right;">+ Rp {{ number_format($nominal_ppn, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($pph_persen > 0)
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;">Potongan PPh ({{ $pph_persen }}%)</td>
                    <td style="text-align: right; color: #e74c3c;">- Rp {{ number_format($nominal_pph, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($additional_charge > 0)
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;">Biaya Tambahan</td>
                    <td style="text-align: right;">+ Rp {{ number_format($additional_charge, 0, ',', '.') }}</td>
                </tr>
                @endif

                <tr class="total-row">
                    <td colspan="4" style="text-align: right; font-size: 16px;">GRAND TOTAL</td>
                    <td style="text-align: right; color: #2c3e50; font-size: 16px;">Rp {{ number_format($grand_total, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- [BARU] LOGIKA PEMBAGIAN KOLOM TANDA TANGAN (WITH QR CODE) -->
        @php
            $butuhPresdir = ($po->approval_presdir !== 'Tidak Perlu');
            $lebarKolom = $butuhPresdir ? '19%' : '24%'; 

            // Fungsi Bantuan untuk render QR supaya aman di DomPDF
            function renderQr($teks) {
                return '<img src="data:image/svg+xml;base64,' . base64_encode(QrCode::format('svg')->size(70)->generate($teks)) . '" alt="QR Code">';
            }
        @endphp

        <div class="signatures">
            <!-- TTD 1: Staff Purchasing -->
            <div class="sign-box" style="width: {{ $lebarKolom }};">
                <p>Dibuat Oleh,</p>
                <div style="height: 60px; display:flex; align-items:center; justify-content:center; margin: 10px 0;">
                    {!! renderQr('Dokumen PO ini dibuat secara sah oleh Sistem Purchasing PT Kimpai Dyna Tubes.') !!}
                </div>
                <span>Purchasing</span>
            </div>

            <!-- TTD 2: Manager Plan -->
            <div class="sign-box" style="width: {{ $lebarKolom }};">
                <p>Diketahui (Manager),</p>
                <div style="height: 60px; display:flex; align-items:center; justify-content:center; margin: 10px 0;">
                    @if($isManagerACC)
                        {!! renderQr('Validasi sah: Disetujui oleh Manager Plan PT Kimpai Dyna Tubes.') !!}
                    @else
                        <span style="color:#94a3b8; font-style:italic; font-size:11px; border:none;">Menunggu...</span>
                    @endif
                </div>
                <span>Manager Plan</span>
            </div>

            <!-- TTD 3: Direktur -->
            <div class="sign-box" style="width: {{ $lebarKolom }};">
                <p>Disetujui (Direktur),</p>
                <div style="height: 60px; display:flex; align-items:center; justify-content:center; margin: 10px 0;">
                    @if($isDirekturACC)
                        {!! renderQr('Validasi sah: Disetujui oleh Direktur PT Kimpai Dyna Tubes.') !!}
                    @else
                        <span style="color:#94a3b8; font-style:italic; font-size:11px; border:none;">Menunggu...</span>
                    @endif
                </div>
                <span>Direktur</span>
            </div>

            <!-- TTD 4: Presdir (Dinamic) -->
            @if($butuhPresdir)
            <div class="sign-box" style="width: {{ $lebarKolom }};">
                <p>Mengetahui (Presdir),</p>
                <div style="height: 60px; display:flex; align-items:center; justify-content:center; margin: 10px 0;">
                    @if($po->approval_presdir == 'Approved')
                        {!! renderQr('Validasi sah: Disetujui oleh Presiden Direktur PT Kimpai Dyna Tubes.') !!}
                    @else
                        <span style="color:#94a3b8; font-style:italic; font-size:11px; border:none;">Menunggu...</span>
                    @endif
                </div>
                <span>Presiden Direktur</span>
            </div>
            @endif

            <!-- TTD 5: Vendor -->
            <div class="sign-box" style="width: {{ $lebarKolom }};">
                <p>Dikonfirmasi Oleh,</p>
                <!-- Kosong untuk TTD manual pihak luar -->
                <div style="height: 60px; margin: 10px 0;"></div>
                <span>Pihak Supplier / Vendor</span>
            </div>
        </div>
        
        <div style="margin-top: 30px; font-size: 11px; text-align: center; color: #7f8c8d;">
            Dokumen ini di-generate secara otomatis oleh Sistem Portal Staff PT Kimpai Dyna Tubes.<br>
            (Nilai Transaksi PO ini: {{ $butuhPresdir ? 'DI ATAS 50 Juta' : 'DI BAWAH 50 Juta' }})
        </div>
    </div>

</body>
</html>