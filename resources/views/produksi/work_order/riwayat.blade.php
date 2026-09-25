@extends('layouts.staff-layout')
@section('title', 'Riwayat Produksi (WO Selesai)')
@section('konten')
<div style="padding: 20px;">
    <h2 style="font-size: 22px; color: #1e293b; margin-bottom: 20px;"><i class="fas fa-history" style="color: #64748b;"></i> Riwayat Work Order (Selesai)</h2>

    <div style="background: #fff; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                <tr>
                    <th style="padding: 15px;">Tgl Selesai</th>
                    <th style="padding: 15px;">Produk (MM)</th>
                    <th style="padding: 15px; text-align: center;">Target WO</th>
                    <th style="padding: 15px; text-align: center;">Total Baik (FG)</th>
                    <th style="padding: 15px; text-align: center;">Total Reject</th>
                    <th style="padding: 15px; text-align: center;">Status</th>
                    <th style="padding: 15px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workOrders as $wo)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <!-- Tanggal Terakhir diupdate (Tanggal Closing) -->
                    <td style="padding: 15px;">{{ \Carbon\Carbon::parse($wo->updated_at)->format('d M Y - H:i') }}</td>
                    
                    <td style="padding: 15px;">
                        <div style="font-weight: bold; color: #0284c7;">{{ $wo->nama_material ?? 'Material Tidak Diketahui' }}</div>
                        <div style="color: #64748b; font-size: 11px;">MM: {{ $wo->no_mm }}</div>
                    </td>
                    
                    <td style="padding: 15px; text-align: center; color: #475569;">{{ number_format($wo->qty_target) }}</td>
                    <td style="padding: 15px; text-align: center; font-weight: bold; color: #166534;">{{ number_format($wo->qty_good) }} Pcs</td>
                    <td style="padding: 15px; text-align: center; font-weight: bold; color: #991b1b;">{{ number_format($wo->qty_reject) }} Pcs</td>
                    
                    <td style="padding: 15px; text-align: center;">
                        <span style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold;">
                            <i class="fas fa-check-circle"></i> Selesai
                        </span>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <!-- Tombol untuk melihat ulang Traceability -->
                        <a href="{{ route('produksi.wo.proses', $wo->id) }}" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; font-weight: bold; text-decoration: none; display: inline-block; font-size: 11px;">
                            <i class="fas fa-eye"></i> Detail Log
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">Belum ada riwayat WO yang diselesaikan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection