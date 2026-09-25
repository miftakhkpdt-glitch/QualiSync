@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 1200px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">

    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 20px;">
        <div>
            <h3 style="margin: 0; color: #1e293b; font-size: 20px;">
                <i class="fas fa-truck-loading" style="color: #0ea5e9;"></i> Monitoring Outstanding PO & Balance Stok
            </h3>
            <span style="font-size: 13px; color: #64748b;">Pantauan otomatis barang masuk dari gudang (Real-time).</span>
        </div>
    </div>

    <div style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 6px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #1e293b; color: white;">
                    <th style="padding: 12px 10px;">No. PO / Vendor</th>
                    <th style="padding: 12px 10px;">Material (MM)</th>
                    <th style="padding: 12px 10px; text-align: right;">Qty Pesanan</th>
                    <th style="padding: 12px 10px; text-align: right;">Sudah Datang</th>
                    <th style="padding: 12px 10px; text-align: right;">Balance (Sisa)</th>
                    <th style="padding: 12px 10px; text-align: center;">Status</th>
                    <!-- [BARU] KOLOM TINDAKAN PEMBAYARAN -->
                    <th style="padding: 12px 10px; text-align: center;">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pos as $po)
                @php 
                    $balance = $po->qty - $po->qty_diterima; 
                    $isLate = ($po->eta && strtotime($po->eta) < time() && $po->status_barang != 'Sudah Diterima');
                @endphp
                
                <tr style="border-bottom: 1px solid #e2e8f0; background: {{ $isLate ? '#fef2f2' : 'white' }};">
                    <td style="padding: 10px;">
                        <span style="font-size: 11px; font-weight: bold; color: #0284c7;">PO-{{ date('Ymd', strtotime($po->tanggal_po)) }}-{{ $po->id }}</span><br>
                        <strong>{{ $po->vendor_name }}</strong>
                    </td>
                    <td style="padding: 10px;">
                        <b>{{ $po->no_mm }}</b><br>
                        <small style="color: #64748b;">{{ $po->nama_material ?? '-' }}</small>
                    </td>
                    <td style="padding: 10px; font-weight: bold; text-align: right;">
                        {{ number_format($po->qty, 0, ',', '.') }} {{ $po->satuan }}
                    </td>
                    <td style="padding: 10px; font-weight: bold; color: #15803d; text-align: right;">
                        {{ number_format($po->qty_diterima, 0, ',', '.') }} {{ $po->satuan }}
                    </td>
                    <td style="padding: 10px; font-weight: bold; color: #b91c1c; text-align: right;">
                        {{ number_format($balance, 0, ',', '.') }} {{ $po->satuan }}
                    </td>
                    <td style="padding: 10px; text-align: center;">
                        @if($po->status_barang == 'Sudah Diterima')
                            <span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">Lunas</span>
                        @elseif($po->status_barang == 'Parsial')
                            <span style="background: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">Parsial</span>
                        @else
                            <span style="background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">Belum Datang</span>
                        @endif
                        
                        @if($isLate)
                            <br><span style="font-size: 10px; color: #dc2626; font-weight: bold;">(Terlambat ETA)</span>
                        @endif
                    </td>
                    
                    <!-- [BARU] LOGIKA TOMBOL NEED TO PAY -->
                    <td style="padding: 10px; text-align: center;">
                        @if($balance <= 0)
                            <!-- Tombol Dimatikan Sementara (Disabled) -->
                            <button type="button" disabled title="Fitur sedang dibangun (Belum konek ke modul FAT)" style="background: #0ea5e9; color: white; border: none; padding: 6px 12px; border-radius: 4px; font-size: 11px; font-weight: bold; cursor: not-allowed; opacity: 0.6; box-shadow: none;">
                                <i class="fas fa-file-invoice-dollar"></i> Need to Pay
                            </button>
                        @else
                            <!-- Tombol Terkunci Jika Balance > 0 -->
                            <button disabled style="background: #cbd5e1; color: white; border: none; padding: 6px 12px; border-radius: 4px; font-size: 11px; font-weight: bold; cursor: not-allowed;" title="Barang belum datang sepenuhnya">
                                <i class="fas fa-lock"></i> Blm Lengkap
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <!-- [REVISI] Colspan diubah dari 6 menjadi 7 agar rapi -->
                    <td colspan="7" style="text-align: center; padding: 30px; color: #64748b; font-style: italic;">
                        Data tidak ditemukan atau semua PO sudah lunas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection