<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $invoice->no_invoice }}</title>
    <!-- Gunakan font yang formal -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; color: #333; line-height: 1.5; margin: 0; padding: 0; background-color: #525659; }
        .invoice-box { max-width: 800px; margin: 30px auto; padding: 40px; border: 1px solid #eee; background: #fff; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #1e293b; padding-bottom: 20px; margin-bottom: 30px; }
        .company-info h1 { margin: 0; font-size: 24px; color: #1e293b; letter-spacing: 1px; }
        .company-info p { margin: 5px 0 0 0; font-size: 13px; color: #64748b; }
        .invoice-title { text-align: right; }
        .invoice-title h2 { margin: 0; font-size: 28px; color: #3b82f6; text-transform: uppercase; letter-spacing: 2px; }
        .invoice-title p { margin: 5px 0 0 0; font-size: 14px; font-weight: bold; }
        .info-section { display: flex; justify-content: space-between; margin-bottom: 30px; font-size: 14px; }
        .info-box { width: 45%; }
        .info-box h4 { margin: 0 0 10px 0; color: #94a3b8; text-transform: uppercase; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table th { background: #f1f5f9; padding: 12px; text-align: left; font-size: 13px; color: #1e293b; border-bottom: 2px solid #cbd5e1; }
        table td { padding: 12px; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        .totals { width: 50%; float: right; margin-bottom: 30px; }
        .totals-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; }
        .totals-row.grand-total { font-weight: bold; font-size: 18px; color: #1e293b; border-top: 2px solid #1e293b; padding-top: 10px; margin-top: 5px; }
        .payment-info { clear: both; background: #f8fafc; padding: 15px; border-radius: 8px; font-size: 13px; border: 1px dashed #cbd5e1; }
        .signatures { display: flex; justify-content: flex-end; margin-top: 50px; text-align: center; }
        .signature-box { width: 200px; }
        .signature-line { border-top: 1px solid #000; margin-top: 70px; padding-top: 5px; font-weight: bold; font-size: 14px; }
        
        /* Gaya Tombol Print (Hanya tampil di layar, hilang saat dicetak) */
        .print-btn-container { text-align: center; margin: 20px 0; }
        .btn-print { background: #10b981; color: white; border: none; padding: 10px 20px; font-size: 16px; font-weight: bold; border-radius: 5px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
        .btn-back { background: #64748b; color: white; border: none; padding: 10px 20px; font-size: 16px; font-weight: bold; border-radius: 5px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; margin-right: 10px; }
        
        @media print {
            body { background: white; margin: 0; padding: 0; }
            .invoice-box { box-shadow: none; border: none; margin: 0; padding: 0; max-width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <!-- Tombol Aksi (Tidak akan ikut ter-print) -->
    <div class="print-btn-container no-print">
        <a href="{{ route('fat.invoices.history') }}" class="btn-back">⬅ Kembali ke Riwayat</a>
        <button onclick="window.print()" class="btn-print">🖨️ Cetak Dokumen</button>
    </div>

    <!-- Area Kertas Dokumen -->
    <div class="invoice-box">
        
        <!-- Header Perusahaan -->
        <div class="header">
            <div class="company-info">
                <h1>KIMPAI DYNA TUBES</h1>
                <p>Kawasan Industri Kimpai Blok A-1, Jawa Barat</p>
                <p>Telp: (021) 1234-5678 | Email: finance@kimpai.co.id</p>
            </div>
            <div class="invoice-title">
                <h2>INVOICE</h2>
                <p># {{ $invoice->no_invoice }}</p>
                <!-- Badge Status -->
                <p style="color: {{ $invoice->status_pembayaran == 'Unpaid' ? '#dc2626' : '#16a34a' }};">
                    {{ $invoice->status_pembayaran == 'Unpaid' ? 'BELUM LUNAS' : 'LUNAS' }}
                </p>
            </div>
        </div>

        <!-- Info Customer & Referensi -->
        <div class="info-section">
            <div class="info-box">
                <h4>Ditagihkan Kepada:</h4>
                <p style="margin:0; font-size: 16px; font-weight: bold; color: #1e293b;">{{ strtoupper($invoice->nama_customer) }}</p>
            </div>
            <div class="info-box" style="text-align: right;">
                <p style="margin: 0 0 5px 0;"><strong>Tanggal Terbit:</strong> {{ \Carbon\Carbon::parse($invoice->tanggal_invoice)->format('d F Y') }}</p>
                <p style="margin: 0 0 5px 0; color: #dc2626;"><strong>Jatuh Tempo:</strong> {{ \Carbon\Carbon::parse($invoice->jatuh_tempo)->format('d F Y') }}</p>
                <p style="margin: 0 0 5px 0;"><strong>No. Surat Jalan:</strong> SJ-{{ str_pad($invoice->sj_id, 4, '0', STR_PAD_LEFT) }}</p>
                <p style="margin: 0;"><strong>No. PO:</strong> {{ $invoice->no_po }}</p>
            </div>
        </div>

        <!-- Tabel Rincian -->
        <table>
            <thead>
                <tr>
                    <th>DESKRIPSI BARANG</th>
                    <th style="text-align: center;">QTY</th>
                    <th style="text-align: right;">HARGA SATUAN</th>
                    <th style="text-align: right;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>{{ $invoice->nama_produk }}</strong></td>
                    <td style="text-align: center;">{{ number_format($invoice->qty_kirim, 0, ',', '.') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($invoice->price, 0, ',', '.') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($invoice->qty_kirim * $invoice->price, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Total Perhitungan -->
        <div class="totals">
            <div class="totals-row">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="totals-row">
                <span>PPN (11%):</span>
                <span>Rp {{ number_format($invoice->ppn, 0, ',', '.') }}</span>
            </div>
            <div class="totals-row grand-total">
                <span>TOTAL TAGIHAN:</span>
                <span>Rp {{ number_format($invoice->total_tagihan, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Instruksi Pembayaran -->
        <div class="payment-info">
            <h4 style="margin: 0 0 5px 0; color: #1e293b;">Instruksi Pembayaran:</h4>
            <p style="margin: 0;">Mohon lakukan pembayaran secara penuh sebelum tanggal jatuh tempo ke rekening berikut:</p>
            <p style="margin: 5px 0 0 0; font-weight: bold; font-size: 14px;">Bank BCA: 1234-5678-90 a.n PT Kimpai Dyna Tubes</p>
        </div>

        <!-- Kolom Tanda Tangan -->
        <div class="signatures">
            <div class="signature-box">
                <p style="margin: 0;">Hormat Kami,</p>
                <div class="signature-line">Departemen Keuangan</div>
            </div>
        </div>

    </div>
</body>
</html>