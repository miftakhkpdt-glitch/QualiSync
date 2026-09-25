@extends('layouts.staff-layout')

@section('title', 'Monitor Permintaan Material - Gudang')

@section('konten')
<div style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    
    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 24px; color: #334155; margin: 0;">
            <i class="fas fa-boxes" style="color: #f59e0b;"></i> Monitor Permintaan Material (Gudang)
        </h2>
        <p style="color: #64748b; font-size: 14px; margin-top: 5px;">Daftar Work Order dari PPIC yang menunggu pengeluaran bahan baku (Issue Material).</p>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #15803d; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #cbd5e1;">
                    <th style="padding: 12px; color: #475569;">No. WO & Waktu Request</th>
                    <th style="padding: 12px; color: #475569;">Barang yang akan Diproduksi</th>
                    <th style="padding: 12px; color: #475569;">Status</th>
                    <th style="padding: 12px; color: #475569; text-align: center;">Aksi (Keluarkan Barang)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workOrders as $wo)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 12px;">
                        <div style="font-weight: bold; color: #0284c7;">{{ $wo->no_wo }}</div>
                        <div style="font-size: 11px; color: #ef4444; font-weight: bold;"><i class="fas fa-clock"></i> Diminta: {{ \Carbon\Carbon::parse($wo->updated_at)->diffForHumans() }}</div>
                    </td>
                    <td style="padding: 12px;">
                        <div style="font-weight: bold; color: #1e293b;">[{{ $wo->salesOrder->no_mm ?? '-' }}] {{ $wo->salesOrder->nama_produk ?? '-' }}</div>
                        <div style="font-size: 11px; color: #64748b;">Target Produksi: {{ number_format($wo->salesOrder->qty ?? 0, 0, ',', '.') }} {{ $wo->salesOrder->satuan ?? 'Pcs' }}</div>
                    </td>
                    <td style="padding: 12px;">
                        <span style="background: #fef3c7; color: #92400e; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold;">
                            <i class="fas fa-hourglass-half"></i> Menunggu Material
                        </span>
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        <form action="{{ url('/warehouse/material-request/'.$wo->id.'/process') }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Apakah Anda yakin sudah menyiapkan & mengeluarkan material untuk WO ini?')" style="background: #10b981; color: white; border: none; padding: 8px 15px; border-radius: 4px; font-weight: bold; cursor: pointer; transition: background 0.2s;">
                                <i class="fas fa-truck-loading"></i> Issue Material
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 40px; color: #94a3b8; font-size: 14px;">
                        <i class="fas fa-check-circle" style="font-size: 30px; color: #cbd5e1; display: block; margin-bottom: 10px;"></i>
                        Mantap! Saat ini tidak ada antrean permintaan material dari PPIC. Gudang aman!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection