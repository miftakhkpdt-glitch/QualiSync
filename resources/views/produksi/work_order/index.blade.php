@extends('layouts.staff-layout')

@section('title', 'Tugas Produksi (Work Order) - PT KIMPAI DYNA TUBE')

@section('konten')
<div style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    
    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 24px; color: #334155; margin: 0;">
            <i class="fas fa-cogs" style="color: #0ea5e9;"></i> Daftar Tugas Produksi (WO Aktif)
        </h2>
        <p style="color: #64748b; font-size: 14px; margin-top: 5px;">Halaman eksekusi produksi. Silakan tarik material ke gudang jika mesin sudah siap.</p>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 4px; margin-bottom: 20px; font-weight: bold;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-top: 4px solid #f59e0b;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #cbd5e1;">
                    <th style="padding: 12px; color: #475569;">No. WO</th>
                    <th style="padding: 12px; color: #475569;">Barang Jadi & Target Qty</th>
                    <th style="padding: 12px; color: #475569;">Status WO</th>
                    <th style="padding: 12px; color: #475569; text-align: center;">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workOrders ?? [] as $wo)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 12px; font-weight: bold; color: #0284c7;">{{ $wo->no_wo }}</td>
                    <td style="padding: 12px;">
                        <div style="font-weight: bold; color: #1e293b;">{{ $wo->salesOrder->nama_produk ?? '-' }}</div>
                        <div style="font-size: 11px; color: #0ea5e9; font-weight: bold;">Target: {{ number_format($wo->salesOrder->qty ?? 0, 0, ',', '.') }} {{ $wo->salesOrder->satuan ?? 'Pcs' }}</div>
                    </td>
                    <td style="padding: 12px;">
                        @if(strtolower($wo->status) == 'on progress')
                            <span style="background: #dbeafe; color: #1e40af; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold;">Sedang Jalan</span>
                        @else
                            <span style="background: #fef3c7; color: #92400e; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold;">Belum Mulai (Pending)</span>
                        @endif
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        <a href="{{ route('produksi.work_order.request', $wo->id) }}" style="display: inline-block; background: #10b981; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 11px; font-weight: bold; transition: background 0.2s;">
                            <i class="fas fa-truck-loading"></i> Tarik Material dari WH
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 25px; color: #94a3b8;">Tidak ada Work Order yang aktif.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection