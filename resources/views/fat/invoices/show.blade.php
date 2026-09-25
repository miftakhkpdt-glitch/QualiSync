@extends('layouts.staff-layout')

@section('konten')
<div style="padding: 30px; background-color: #f8fafc; min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    <!-- Header & Tombol Kembali -->
    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-size: 22px; font-weight: bold; color: #1e293b; margin: 0 0 5px 0;">
                <i class="fas fa-file-invoice" style="color: #64748b; margin-right: 8px;"></i> Detail Faktur Penjualan
            </h2>
            <p style="color: #64748b; font-size: 14px; margin: 0;">Rincian tagihan <strong>#{{ $invoice->no_invoice }}</strong></p>
        </div>
        <div>
            <a href="{{ route('fat.invoices.history') }}" style="background-color: #e2e8f0; color: #475569; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold; margin-right: 10px;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('fat.invoices.print', $invoice->id) }}" target="_blank" style="background-color: #6366f1; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold;">
                <i class="fas fa-print"></i> Cetak Dokumen
            </a>
        </div>
    </div>

    <!-- Kotak Putih Utama -->
    <div style="background: white; border-radius: 10px; padding: 30px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        
        <!-- Status Pembayaran -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 20px; color: #0f172a;">KIMPAI DYNA TUBES</h3>
            
            @if($invoice->status_pembayaran == 'Unpaid')
                <span style="background-color: #fee2e2; color: #b91c1c; padding: 8px 15px; border-radius: 6px; font-size: 14px; font-weight: bold; border: 1px solid #fca5a5;">BELUM LUNAS</span>
            @else
                <span style="background-color: #dcfce7; color: #15803d; padding: 8px 15px; border-radius: 6px; font-size: 14px; font-weight: bold; border: 1px solid #86efac;">LUNAS</span>
            @endif
        </div>

        <!-- Info Customer & Dokumen -->
        <div style="display: flex; gap: 30px; margin-bottom: 30px;">
            <div style="flex: 1;">
                <p style="font-size: 12px; color: #64748b; font-weight: bold; margin: 0 0 5px 0; text-transform: uppercase;">Ditagihkan Kepada:</p>
                <p style="font-size: 18px; color: #0f172a; font-weight: bold; margin: 0 0 10px 0;">{{ strtoupper($invoice->nama_customer) }}</p>
                
                <p style="font-size: 12px; color: #64748b; font-weight: bold; margin: 0 0 5px 0; text-transform: uppercase;">Referensi Surat Jalan:</p>
                <p style="font-size: 14px; color: #0f172a; margin: 0;">SJ-{{ str_pad($invoice->sj_id, 4, '0', STR_PAD_LEFT) }} (Dikirim: {{ \Carbon\Carbon::parse($invoice->tanggal_pengiriman)->format('d M Y') }})</p>
            </div>
            
            <div style="flex: 1; background-color: #f8fafc; padding: 15px; border-radius: 8px; border: 1px dashed #cbd5e1;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span style="color: #64748b; font-size: 14px;">Tanggal Terbit:</span>
                    <strong style="color: #0f172a;">{{ \Carbon\Carbon::parse($invoice->tanggal_invoice)->format('d M Y') }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span style="color: #64748b; font-size: 14px;">Jatuh Tempo:</span>
                    <strong style="color: #dc2626;">{{ \Carbon\Carbon::parse($invoice->jatuh_tempo)->format('d M Y') }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748b; font-size: 14px;">Nomor PO:</span>
                    <strong style="color: #0f172a;">{{ $invoice->no_po }}</strong>
                </div>
            </div>
        </div>

        <!-- Tabel Rincian Harga -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background-color: #f1f5f9; border-bottom: 2px solid #cbd5e1;">
                    <th style="padding: 12px; text-align: left; font-size: 13px; color: #1e293b;">Deskripsi Barang</th>
                    <th style="padding: 12px; text-align: center; font-size: 13px; color: #1e293b;">Qty</th>
                    <th style="padding: 12px; text-align: right; font-size: 13px; color: #1e293b;">Harga Satuan</th>
                    <th style="padding: 12px; text-align: right; font-size: 13px; color: #1e293b;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 15px 12px; font-size: 14px; font-weight: bold; color: #0f172a;">{{ $invoice->nama_produk }}</td>
                    <td style="padding: 15px 12px; text-align: center; font-size: 14px;">{{ number_format($invoice->qty_kirim, 0, ',', '.') }}</td>
                    <td style="padding: 15px 12px; text-align: right; font-size: 14px;">Rp {{ number_format($invoice->price, 0, ',', '.') }}</td>
                    <td style="padding: 15px 12px; text-align: right; font-size: 14px; font-weight: bold;">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Total -->
        <div style="display: flex; justify-content: flex-end;">
            <div style="width: 300px;">
                <div style="display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; color: #64748b;">
                    <span>Subtotal</span>
                    <span style="color: #0f172a; font-weight: bold;">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; color: #64748b;">
                    <span>PPN (11%)</span>
                    <span style="color: #0f172a; font-weight: bold;">Rp {{ number_format($invoice->ppn, 0, ',', '.') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 15px 0 0 0; margin-top: 10px; border-top: 2px solid #e2e8f0; font-size: 18px; font-weight: 900; color: #1e293b;">
                    <span>TOTAL TAGIHAN</span>
                    <span style="color: #2563eb;">Rp {{ number_format($invoice->total_tagihan, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;">

        <!-- Tombol Aksi Pembayaran -->
        <div style="text-align: right;">
            @if($invoice->status_pembayaran == 'Unpaid')
                
                <!-- Ganti menjadi Form POST -->
                <form action="{{ route('fat.invoices.pay', $invoice->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin dana untuk tagihan ini sudah masuk ke rekening perusahaan? Aksi ini akan mengubah status menjadi LUNAS.');">
                    @csrf
                    <button type="submit" style="background-color: #10b981; color: white; border: none; padding: 12px 20px; border-radius: 6px; font-size: 15px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2); transition: 0.3s;" onmouseover="this.style.backgroundColor='#059669'" onmouseout="this.style.backgroundColor='#10b981'">
                        <i class="fas fa-hand-holding-usd" style="margin-right: 5px;"></i> Catat Penerimaan Pembayaran
                    </button>
                </form>

            @else
                <!-- Jika sudah lunas, tampilkan label saja -->
                <div style="display: inline-block; background-color: #f0fdf4; color: #15803d; padding: 12px 20px; border-radius: 6px; font-size: 15px; font-weight: bold; border: 1px solid #bbf7d0;">
                    <i class="fas fa-check-circle" style="margin-right: 5px;"></i> Tagihan Ini Sudah Lunas
                </div>
            @endif
        </div>

    </div>
</div>
@endsection