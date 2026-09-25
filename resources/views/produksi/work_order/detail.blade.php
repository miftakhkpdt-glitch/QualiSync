@extends('layouts.staff-layout')

@section('title', 'Detail Work Order')

@section('konten')
<div style="padding: 30px; background-color: #f8fafc; min-height: 100vh;">
    
    <!-- Tombol Kembali & Judul -->
    <div style="margin-bottom: 25px;">
        <a href="{{ route('produksi.proses.index') }}" style="color: #64748b; text-decoration: none; font-size: 14px; margin-bottom: 10px; display: inline-block; font-weight: bold;">
            <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Kembali
        </a>
        <h2 style="font-size: 24px; font-weight: bold; color: #1e293b; margin: 5px 0;">
            <i class="fas fa-chart-line" style="color: #0ea5e9; margin-right: 8px;"></i> Monitor Detail Work Order
        </h2>
        <p style="color: #64748b; font-size: 15px; margin: 0;">
            Pantau progress produksi dan riwayat operator untuk SPK ini.
        </p>
    </div>

    <!-- KARTU INFORMASI UTAMA -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <!-- Identitas Produk -->
        <div style="background: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; border-left: 4px solid #3b82f6;">
            <div style="font-size: 12px; color: #64748b; font-weight: bold; text-transform: uppercase;">Produk (MM)</div>
            <div style="font-size: 16px; font-weight: bold; color: #1e293b; margin-top: 5px;">{{ $wo->nama_material ?? 'Nama Produk' }}</div>
            <div style="font-size: 13px; color: #94a3b8; margin-top: 2px;">No SPK: {{ $wo->nomor_wo ?? '-' }}</div>
        </div>

        <!-- Progress Target -->
        <div style="background: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; border-left: 4px solid #f59e0b;">
            <div style="font-size: 12px; color: #64748b; font-weight: bold; text-transform: uppercase;">Pencapaian (Progress)</div>
            <div style="font-size: 22px; font-weight: bold; color: #b45309; margin-top: 5px;">
                {{ number_format($wo->qty_good ?? 0) }} <span style="font-size: 14px; color: #94a3b8; font-weight: normal;">/ {{ number_format($wo->qty_target) }} Pcs</span>
            </div>
            
            <!-- Progress Bar -->
            @php $persen = ($wo->qty_target > 0) ? round((($wo->qty_good ?? 0) / $wo->qty_target) * 100) : 0; @endphp
            <div style="background: #e2e8f0; border-radius: 10px; height: 8px; width: 100%; margin-top: 10px; overflow: hidden;">
                <div style="background: #f59e0b; height: 100%; width: {{ $persen > 100 ? 100 : $persen }}%;"></div>
            </div>
        </div>

        <!-- Akumulasi Reject -->
        <div style="background: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; border-left: 4px solid #ef4444;">
            <div style="font-size: 12px; color: #64748b; font-weight: bold; text-transform: uppercase;">Akumulasi Reject</div>
            <div style="font-size: 22px; font-weight: bold; color: #b91c1c; margin-top: 5px;">
                {{ number_format($wo->qty_reject ?? 0) }} <span style="font-size: 14px; font-weight: normal;">Pcs</span>
            </div>
            <div style="font-size: 12px; color: #991b1b; margin-top: 5px; background: #fee2e2; display: inline-block; padding: 2px 8px; border-radius: 4px;">
                Tingkat Cacat: {{ $wo->qty_good > 0 ? round(($wo->qty_reject / ($wo->qty_good + $wo->qty_reject)) * 100, 2) : 0 }}%
            </div>
        </div>
    </div>

    <!-- TABEL LOG TRACEABILITY (VIEW ONLY) -->
    <div style="background: white; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden; border: 1px solid #e2e8f0;">
        <div style="padding: 15px 20px; background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
            <h3 style="margin: 0; font-size: 16px; color: #334155;"><i class="fas fa-history" style="color: #64748b; margin-right: 8px;"></i> Riwayat Traceability Operator</h3>
        </div>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background-color: #f1f5f9; color: #475569;">
                    <th style="padding: 12px 20px; font-size: 13px; font-weight: bold;">Tanggal & Shift</th>
                    <th style="padding: 12px 20px; font-size: 13px; font-weight: bold;">Operator</th>
                    <th style="padding: 12px 20px; font-size: 13px; font-weight: bold;">Web Terpakai</th>
                    <th style="padding: 12px 20px; font-size: 13px; font-weight: bold;">CAP Terpakai</th>
                </tr>
            </thead>
            <tbody>
                @forelse($traceability_logs as $log)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 12px 20px; font-size: 13px; color: #1e293b;">
                        <strong>{{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}</strong><br>
                        <span style="color: #64748b;">Shift {{ $log->shift }} (Grup {{ $log->grup ?? '-' }})</span>
                    </td>
                    <td style="padding: 12px 20px; font-size: 14px; color: #334155; font-weight: bold;">
                        <i class="fas fa-user-circle" style="color: #cbd5e1;"></i> {{ $log->operator }}
                    </td>
                    <td style="padding: 12px 20px; font-size: 13px; color: #0284c7;">
                        {{ number_format($log->web_qty) }} Pcs <br>
                        <span style="font-size: 11px; color: #94a3b8;">Lot: {{ $log->web_lot_num ?? '-' }}</span>
                    </td>
                    <td style="padding: 12px 20px; font-size: 13px; color: #166534;">
                        {{ number_format($log->cap_qty) }} Pcs <br>
                        <span style="font-size: 11px; color: #94a3b8;">Lot: {{ $log->cap_lot_num ?? '-' }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="padding: 20px; text-align: center; color: #94a3b8;">Belum ada log material untuk WO ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection