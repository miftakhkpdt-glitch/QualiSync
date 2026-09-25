@extends('layouts.staff-layout')
@section('title', 'Daftar Proses Produksi')
@section('konten')
<div style="padding: 20px;">
    <h2 style="font-size: 22px; color: #1e293b; margin-bottom: 20px;"><i class="fas fa-cogs" style="color: #10b981;"></i> WO Sedang Berjalan (On Progress)</h2>
    
    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 10px; border-radius: 5px; margin-bottom: 20px;">{{ session('success') }}</div>
    @endif

    <div style="background: #fff; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                <tr><th style="padding: 15px;">Start Date</th><th style="padding: 15px;">Produk (MM)</th><th style="padding: 15px; text-align: center;">Target WO</th><th style="padding: 15px; text-align: center;">Status</th><th style="padding: 15px; text-align: center;">Aksi Lanjutan</th></tr>
            </thead>
            <tbody>
                @forelse($workOrders as $wo)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 15px;">{{ \Carbon\Carbon::parse($wo->start_date)->format('d M Y') }}</td>
                    <td style="padding: 15px;">
                        <div style="font-weight: bold; color: #0284c7;">{{ $wo->nama_material ?? 'Material Tidak Diketahui' }}</div>
                        <div style="color: #64748b; font-size: 11px;">Progress: {{ number_format($wo->qty_good ?? 0) }} / {{ number_format($wo->qty_target) }} Pcs</div>
                    </td>
                    <td style="padding: 15px; text-align: center; font-weight: bold; color: #b45309;">{{ number_format($wo->qty_target) }} Pcs</td>
                    <td style="padding: 15px; text-align: center;">
                        <span style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold;">
                            <i class="fas fa-spinner fa-spin"></i> On Progress
                        </span>
                    </td>
                    <td style="padding: 15px 20px; text-align: center;">
    <a href="{{ route('produksi.wo.detail', $wo->id) }}" style="background-color: #0ea5e9; color: white; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold; display: inline-block; box-shadow: 0 2px 4px rgba(14, 165, 233, 0.3); transition: 0.2s;">
        <i class="fas fa-search-chart" style="margin-right: 5px;"></i> Pantau Detail
    </a>
</td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8;">Belum ada WO yang sedang berjalan. Buka menu Tugas Produksi untuk memulai.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection