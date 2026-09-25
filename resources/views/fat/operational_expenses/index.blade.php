@extends('layouts.staff-layout')

@section('konten')
<div style="padding: 30px; background-color: #f8fafc; min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    @if(session('success'))
    <div style="background-color: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0; font-weight: bold;">
        <i class="fas fa-check-circle" style="margin-right: 5px;"></i> {{ session('success') }}
    </div>
    @endif

    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-size: 22px; font-weight: bold; color: #1e293b; margin: 0 0 5px 0;">
                <i class="fas fa-wallet" style="color: #f59e0b; margin-right: 8px;"></i> Biaya Operasional (OPEX)
            </h2>
            <p style="color: #64748b; font-size: 14px; margin: 0;">Catatan pengeluaran harian/bulanan perusahaan.</p>
        </div>
        <a href="{{ route('fat.operational_expenses.create') }}" style="background-color: #f59e0b; color: white; padding: 10px 18px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: bold; transition: 0.3s; box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);" onmouseover="this.style.backgroundColor='#d97706'" onmouseout="this.style.backgroundColor='#f59e0b'">
            <i class="fas fa-plus" style="margin-right: 5px;"></i> Catat Pengeluaran Baru
        </a>
    </div>

    <div style="background: white; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden; border: 1px solid #e2e8f0;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 15px; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase;">Tanggal</th>
                    <th style="padding: 15px; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase;">Kategori</th>
                    <th style="padding: 15px; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase;">Nama Pengeluaran</th>
                    <th style="padding: 15px; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: right;">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $exp)
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 15px; font-size: 14px; color: #475569;">{{ \Carbon\Carbon::parse($exp->tanggal)->format('d M Y') }}</td>
                    <td style="padding: 15px;">
                        <span style="background-color: #f1f5f9; color: #475569; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; border: 1px solid #cbd5e1;">
                            {{ $exp->kategori }}
                        </span>
                    </td>
                    <td style="padding: 15px; font-size: 14px; font-weight: bold; color: #0f172a;">
                        {{ $exp->nama_biaya }}
                        @if($exp->keterangan)
                        <div style="font-size: 12px; color: #64748b; font-weight: normal; margin-top: 5px;">Catatan: {{ $exp->keterangan }}</div>
                        @endif
                    </td>
                    <td style="padding: 15px; font-size: 14px; font-weight: bold; color: #dc2626; text-align: right;">
                        Rp {{ number_format($exp->nominal, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="padding: 30px; text-align: center; color: #94a3b8;">Belum ada catatan pengeluaran operasional.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection