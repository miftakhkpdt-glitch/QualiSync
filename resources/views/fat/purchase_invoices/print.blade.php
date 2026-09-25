<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Verifikasi Hutang - {{ $invoice->no_invoice_supplier }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; color: #333; line-height: 1.5; margin: 0; padding: 0; background-color: #525659; }
        .invoice-box { max-width: 800px; margin: 30px auto; padding: 40px; border: 1px solid #eee; background: #fff; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #1e293b; padding-bottom: 20px; margin-bottom: 30px; }
        .company-info h1 { margin: 0; font-size: 24px; color: #1e293b; letter-spacing: 1px; }
        .invoice-title { text-align: right; }
        .invoice-title h2 { margin: 0; font-size: 20px; color: #6d28d9; text-transform: uppercase; letter-spacing: 1px; }
        .invoice-title p { margin: 5px 0 0 0; font-size: 14px; font-weight: bold; }
        .info-section { display: flex; justify-content: space-between; margin-bottom: 30px; font-size: 14px; }
        .info-box { width: 45%; }
        .info-box h4 { margin: 0 0 10px 0; color: #64748b; text-transform: uppercase; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table th { background: #f8fafc; padding: 12px; text-align: left; font-size: 13px; color: #1e293b; border-bottom: 2px solid #cbd5e1; }
        table td { padding: 12px; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        .totals { width: 50%; float: right; margin-bottom: 30px; }
        .totals-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; }
        .totals-row.grand-total { font-weight: bold; font-size: 18px; color: #1e293b; border-top: 2px solid #1e293b; padding-top: 10px; margin-top: 5px; }
        .signatures { display: flex; justify-content: space-between; margin-top: 80px; text-align: center; clear: both; }
        .signature-box { width: 180px; }
        .signature-line { border-top: 1px solid #000; margin-top: 70px; padding-top: 5px; font-weight: bold; font-size: 13px; }
        
        .print-btn-container { text-align: center; margin: 20px 0; }
        .btn-print { background: #8b5cf6; color: white; border: none; padding: 10px 20px; font-size: 16px; font-weight: bold; border-radius: 5px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
        .btn-back { background: #64748b; color: white; border: none; padding: 10px 20px; font-size: 16px; font-weight: bold; border-radius: 5px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; margin-right: 10px; }
        
        @media print {
            body { background: white; margin: 0; padding: 0; }
            .invoice-box { box-shadow: none; border: none; margin: 0; padding: 0; max-width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="print-btn-container no-print">
        <a href="{{ route('fat.purchase_invoices.history') }}" class="btn-back">⬅ Kembali ke Riwayat</a>
        <button onclick="window.print()" class="btn-print">🖨️ Cetak Dokumen</button>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div class="company-info">
                <h1>PT KIMPAI DYNA TUBES</h1>
                <p style="margin: 5px 0 0 0; font-size: 13px; color: #64748b;">Kawasan Industri Kimpai Blok A-1</p>
                <p style="margin: 0; font-size: 13px; color: #64748b;">(Formulir Verifikasi Tagihan Internal)</p>
            </div>
            <div class="invoice-title">
                <h2>BUKTI TAGIHAN SUPPLIER</h2>
                <p>Status: {{ $invoice->status_pembayaran == 'Unpaid' ? 'BELUM DIBAYAR' : 'LUNAS' }}</p>
            </div>
        </div>

        <div class="info-section">
            <div class="info-box">
                <h4>Informasi Vendor:</h4>
                <p style="margin:0; font-size: 16px; font-weight: bold; color: #1e293b; text-transform: uppercase;">{{ $invoice->vendor_name }}</p>
                <p style="margin: 5px 0 0 0;"><strong>No. Invoice Vendor:</strong> {{ $invoice->no_invoice_supplier }}</p>
                <p style="margin: 5px 0 0 0;"><strong>No. Surat Jalan:</strong> {{ $invoice->sj_supplier_number ?? '-' }}</p>
            </div>
            <div class="info-box" style="text-align: right;">
                <p style="margin: 0 0 5px 0;"><strong>Tgl. Terima Barang:</strong> {{ \Carbon\Carbon::parse($invoice->tanggal_terima)->format('d F Y') }}</p>
                <p style="margin: 0 0 5px 0;"><strong>Tgl. Invoice:</strong> {{ \Carbon\Carbon::parse($invoice->tanggal_invoice)->format('d F Y') }}</p>
                <p style="margin: 0 0 5px 0; color: #dc2626;"><strong>Jatuh Tempo:</strong> {{ \Carbon\Carbon::parse($invoice->jatuh_tempo)->format('d F Y') }}</p>
                <p style="margin: 0;"><strong>No. PO Internal:</strong> {{ $invoice->po_kpdt_number }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>DESKRIPSI BARANG</th>
                    <th style="text-align: center;">QTY TERIMA</th>
                    <th style="text-align: right;">HARGA PO</th>
                    <th style="text-align: right;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>{{ $invoice->item_name }}</strong></td>
                    <td style="text-align: center;">{{ number_format($invoice->quantity, 0, ',', '.') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($invoice->harga_satuan, 0, ',', '.') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-row">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="totals-row">
                <span>PPN Masukan:</span>
                <span>Rp {{ number_format($invoice->ppn, 0, ',', '.') }}</span>
            </div>
            <div class="totals-row grand-total">
                <span>TOTAL DIBAYARKAN:</span>
                <span>Rp {{ number_format($invoice->total_tagihan, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="signatures">
            <div class="signature-box">
                <p style="margin: 0;">Dibuat Oleh,</p>
                <div class="signature-line">Staff FAT</div>
            </div>
            <div class="signature-box">
                <p style="margin: 0;">Diperiksa Oleh,</p>
                <div class="signature-line">Manager FAT</div>
            </div>
            <div class="signature-box">
                <p style="margin: 0;">Disetujui Oleh,</p>
                <div class="signature-line">Direktur Keuangan</div>
            </div>
        </div>
    </div>
</body>
</html>