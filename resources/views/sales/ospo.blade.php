@extends('layouts.staff-layout') <!-- Sesuaikan dengan nama layout Anda -->

@section('konten')
<div style="max-width: 1400px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px;">
        <h3 style="margin: 0; color: #334155;"><i class="fas fa-clipboard-list"></i> Monitor OSPO (Outstanding PO)</h3>
    </div>

    <div style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
        <p style="margin: 0; color: #1e3a8a; font-size: 13px;">
            <i class="fas fa-info-circle"></i> Halaman ini memantau sisa barang (OSPO) yang belum dikirim ke customer. Data pengiriman di-update otomatis dari inputan bagian Gudang (Warehouse Outgoing).
        </p>
    </div>

    <!-- TABEL DATA OSPO -->
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #1e293b; color: white;">
                    <th style="padding: 12px 10px;">Tgl PO</th>
                    <th style="padding: 12px 10px;">No. PO & Customer</th>
                    <th style="padding: 12px 10px;">Item / Produk</th>
                    <th style="padding: 12px 10px; text-align: center;">Qty PO</th>
                    <th style="padding: 12px 10px; text-align: center;">Terkirim</th>
                    <th style="padding: 12px 10px; text-align: center; background: #b91c1c;">Sisa (OSPO)</th>
                    <th style="padding: 12px 10px; text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data_ospo as $po)
                    @php
                        // Menghitung Qty Terkirim secara dinamis
                        $qty_po = $po->qty ?? 0;
                        $ospo = $po->ospo ?? $qty_po; // Jika ospo null, anggap belum dikirim sama sekali
                        $terkirim = $qty_po - $ospo;
                        
                        // Menentukan persentase untuk indikator warna
                        $persentase = $qty_po > 0 ? ($terkirim / $qty_po) * 100 : 0;
                    @endphp
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <!-- Tanggal -->
                        <td style="padding: 10px;">
                            {{ \Carbon\Carbon::parse($po->tanggal_po)->format('d M Y') }}
                        </td>

                        <!-- PO & Customer -->
                        <td style="padding: 10px;">
                            <b style="color: #0369a1;">{{ $po->no_po }}</b><br>
                            <span style="color: #475569;">{{ $po->nama_customer }}</span>
                        </td>

                        <!-- Produk -->
                        <td style="padding: 10px;">
                            <b style="color: #1e293b;">{{ $po->no_mm }}</b><br>
                            <span style="color: #64748b; font-size: 11px;">{{ $po->nama_produk }}</span>
                        </td>

                        <!-- Qty PO -->
                        <td style="padding: 10px; text-align: center; font-weight: bold; color: #334155;">
                            {{ number_format($qty_po, 0, ',', '.') }} {{ $po->satuan }}
                        </td>

                        <!-- Terkirim -->
                        <td style="padding: 10px; text-align: center; color: #10b981; font-weight: bold;">
                            {{ number_format($terkirim, 0, ',', '.') }} {{ $po->satuan }}
                        </td>

                        <!-- Sisa OSPO -->
                        <td style="padding: 10px; text-align: center;">
                            <span style="background: #fee2e2; color: #b91c1c; padding: 4px 8px; border-radius: 4px; font-weight: bold;">
                                {{ number_format($ospo, 0, ',', '.') }} {{ $po->satuan }}
                            </span>
                        </td>

                        <!-- Status -->
                        <td style="padding: 10px; text-align: center;">
                            @if($ospo <= 0)
                                <span style="background: #d1fae5; color: #047857; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;">CLOSED</span>
                            @elseif($terkirim > 0)
                                <span style="background: #fef08a; color: #854d0e; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;">PARSIAL</span>
                            @else
                                <span style="background: #e2e8f0; color: #475569; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;">OPEN</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: #64748b;">
                            Belum ada data Sales Order (PO).
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection