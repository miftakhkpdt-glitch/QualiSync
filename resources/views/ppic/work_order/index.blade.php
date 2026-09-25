@extends('layouts.staff-layout')

@section('title', 'Manajemen Work Order - PT KIMPAI DYNA TUBE')

@section('konten')
<div style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    
    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 24px; color: #334155; margin: 0;">
            <i class="fas fa-industry" style="color: #0ea5e9;"></i> Manajemen Work Order (PPIC)
        </h2>
        <p style="color: #64748b; font-size: 14px; margin-top: 5px;">Daftar perintah kerja produksi berdasarkan Sales Order pelanggan.</p>
    </div>

    <!-- WIDGET ANGKA -->
    <div style="display: flex; gap: 20px; margin-bottom: 30px;">
        
        <!-- KOTAK 1: PERSIAPAN -->
        <div style="flex: 1; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 5px solid #f59e0b;">
            <div style="color: #64748b; font-size: 12px; font-weight: bold; text-transform: uppercase;">WO Pending (WH Prepare Material)</div>
            <div style="font-size: 28px; font-weight: bold; color: #1e293b; margin-top: 5px;">{{ $woPending ?? 0 }}</div>
        </div>
        
        <!-- KOTAK 2: PRODUKSI -->
        <div style="flex: 1; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 5px solid #3b82f6;">
            <div style="color: #64748b; font-size: 12px; font-weight: bold; text-transform: uppercase;">WO Sedang Diproses Produksi</div>
            <div style="font-size: 28px; font-weight: bold; color: #1e293b; margin-top: 5px;">{{ $woProses ?? 0 }}</div>
        </div>
        
        <!-- KOTAK 3: SELESAI -->
        <div style="flex: 1; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 5px solid #10b981;">
            <div style="color: #64748b; font-size: 12px; font-weight: bold; text-transform: uppercase;">WO Selesai</div>
            <div style="font-size: 28px; font-weight: bold; color: #1e293b; margin-top: 5px;">{{ $woSelesai ?? 0 }}</div>
        </div>
        
    </div>

    <!-- TABEL DATA WORK ORDER -->
    <div style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #334155; font-size: 16px;"><i class="fas fa-list"></i> Daftar Surat Perintah Kerja</h3>
            
            <!-- TOMBOL SUDAH DIPERBAIKI MENJADI LINK -->
            <a href="{{ url('/ppic/work-order/create') }}" style="background: #0ea5e9; color: white; border: none; padding: 8px 15px; border-radius: 4px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block;">
                <i class="fas fa-plus"></i> Buat WO Baru
            </a>
            
        </div>
        
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #cbd5e1;">
                    <th style="padding: 12px; color: #475569;">No. WO & Tgl Mulai</th>
                    <th style="padding: 12px; color: #475569;">Customer & PO</th>
                    <th style="padding: 12px; color: #475569;">Produk & Target Qty</th>
                    <th style="padding: 12px; color: #475569;">Status</th>
                    <th style="padding: 12px; color: #475569; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workOrders ?? [] as $wo)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 12px;">
                        <div style="font-weight: bold; color: #0284c7;">{{ $wo->no_wo }}</div>
                        <div style="font-size: 11px; color: #64748b;">Mulai: {{ date('d M Y', strtotime($wo->start_date)) }}</div>
                    </td>
                    <td style="padding: 12px;">
                        <div style="font-weight: bold; color: #1e293b;">{{ $wo->salesOrder->nama_customer ?? 'Unknown' }}</div>
                        <div style="font-size: 11px; color: #64748b;">PO: {{ $wo->salesOrder->no_po ?? '-' }}</div>
                    </td>
                    <td style="padding: 12px;">
                        <div style="font-weight: bold; color: #0f172a;">{{ $wo->salesOrder->nama_produk ?? '-' }}</div>
                        <div style="font-size: 11px; color: #0ea5e9; font-weight: bold;">Target: {{ number_format($wo->salesOrder->qty ?? 0, 0, ',', '.') }} {{ $wo->salesOrder->satuan ?? 'Pcs' }}</div>
                    </td>
                    <td style="padding: 12px;">
                        @if(strtolower($wo->status) == 'completed')
                            <span style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold;">Selesai</span>
                        @elseif(strtolower($wo->status) == 'on progress')
                            <span style="background: #dbeafe; color: #1e40af; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold;">Sedang Jalan</span>
                        @else
                            <span style="background: #fef3c7; color: #92400e; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold;">Pending</span>
                        @endif
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        <a href="{{ route('ppic.work_order.request', $wo->id) }}" style="display: inline-block; background: #0ea5e9; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 11px; font-weight: bold; transition: background 0.2s;">
                            <i class="fas fa-search"></i> Detail WO & BOM
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 25px; color: #94a3b8;">Belum ada data Work Order.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection