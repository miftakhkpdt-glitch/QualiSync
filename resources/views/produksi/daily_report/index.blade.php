@extends('layouts.staff-layout')

@section('title', 'Input Daily Report')

@section('konten')
<div style="padding: 30px; background-color: #f8fafc; min-height: 100vh;">
    
    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 24px; font-weight: bold; color: #1e293b; margin: 0 0 5px 0;">
            <i class="fas fa-clipboard-check" style="color: #f59e0b; margin-right: 8px;"></i> Input Daily Report (Check Sheet)
        </h2>
        <p style="color: #64748b; font-size: 14px; margin: 0;">Pilih Work Order (WO) yang sedang berjalan untuk mengisi Laporan Produksi & Reject.</p>
    </div>

    <div style="background: white; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden; border: 1px solid #e2e8f0;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background-color: #f1f5f9; color: #475569;">
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: bold;">Work Order</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: bold;">Target Produksi</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: bold; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wo_berjalan as $wo)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 15px 20px;">
                        <div style="font-weight: bold; color: #1e293b; font-size: 14px;">{{ $wo->nama_material ?? 'Nama Produk' }}</div>
                        <div style="font-size: 12px; color: #64748b;">MM: {{ $wo->no_mm ?? '-' }}</div>
                    </td>
                    <td style="padding: 15px 20px; font-size: 14px; font-weight: bold; color: #d97706;">
                        {{ number_format($wo->qty_target ?? 0, 0, ',', '.') }} Pcs
                    </td>
                    <td style="padding: 15px 20px; text-align: center;">
                        <a href="{{ route('produksi.daily_report.form', $wo->id) }}" style="background-color: #f59e0b; color: white; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold; display: inline-block;">
                            <i class="fas fa-edit"></i> Isi Daily Report
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" style="padding: 20px; text-align: center; color: #94a3b8;">Tidak ada WO yang sedang berjalan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection