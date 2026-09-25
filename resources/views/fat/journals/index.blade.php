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
                <i class="fas fa-book" style="color: #6366f1; margin-right: 8px;"></i> Jurnal Umum (General Ledger)
            </h2>
            <p style="color: #64748b; font-size: 14px; margin: 0;">Pencatatan akuntansi double-entry untuk penyesuaian dan mutasi.</p>
        </div>
        <a href="{{ route('fat.journals.create') }}" style="background-color: #6366f1; color: white; padding: 10px 18px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: bold; transition: 0.3s; box-shadow: 0 2px 4px rgba(99, 102, 241, 0.3);" onmouseover="this.style.backgroundColor='#4f46e5'" onmouseout="this.style.backgroundColor='#6366f1'">
            <i class="fas fa-plus" style="margin-right: 5px;"></i> Buat Jurnal Baru
        </a>
    </div>

    @forelse($journals as $journal)
    <div style="background: white; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden; border: 1px solid #e2e8f0; margin-bottom: 25px;">
        <!-- Header Jurnal -->
        <div style="background-color: #f1f5f9; padding: 15px 20px; border-bottom: 2px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <span style="background-color: #1e293b; color: white; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; margin-right: 10px;">{{ $journal->nomor_jurnal }}</span>
                <span style="font-size: 14px; font-weight: bold; color: #0f172a;">{{ \Carbon\Carbon::parse($journal->tanggal)->format('d F Y') }}</span>
                <p style="margin: 5px 0 0 0; font-size: 14px; color: #475569;">{{ $journal->keterangan }}</p>
            </div>
            <div style="text-align: right;">
                <p style="margin: 0 0 5px 0; font-size: 12px; color: #64748b; font-weight: bold; text-transform: uppercase;">Total Transaksi</p>
                <h3 style="margin: 0; font-size: 18px; color: #0f172a;">Rp {{ number_format($journal->total_debit, 0, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Detail Akun Jurnal -->
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background-color: white; border-bottom: 1px solid #e2e8f0;">
                    <th style="padding: 12px 20px; font-size: 12px; color: #64748b;">KODE AKUN</th>
                    <th style="padding: 12px 20px; font-size: 12px; color: #64748b;">NAMA AKUN</th>
                    <th style="padding: 12px 20px; font-size: 12px; color: #64748b; text-align: right;">DEBIT (Rp)</th>
                    <th style="padding: 12px 20px; font-size: 12px; color: #64748b; text-align: right;">KREDIT (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($journal->details as $detail)
                <tr style="border-bottom: 1px solid #f8fafc;">
                    <td style="padding: 12px 20px; font-size: 13px; font-weight: bold; color: #3b82f6;">{{ $detail->kode_akun }}</td>
                    <td style="padding: 12px 20px; font-size: 14px; color: #0f172a;">{{ $detail->nama_akun }}</td>
                    <td style="padding: 12px 20px; font-size: 14px; text-align: right; font-weight: {{ $detail->debit > 0 ? 'bold' : 'normal' }}; color: {{ $detail->debit > 0 ? '#10b981' : '#cbd5e1' }};">
                        {{ number_format($detail->debit, 0, ',', '.') }}
                    </td>
                    <td style="padding: 12px 20px; font-size: 14px; text-align: right; font-weight: {{ $detail->kredit > 0 ? 'bold' : 'normal' }}; color: {{ $detail->kredit > 0 ? '#ef4444' : '#cbd5e1' }};">
                        {{ number_format($detail->kredit, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @empty
    <div style="background: white; border-radius: 10px; padding: 40px; text-align: center; border: 1px dashed #cbd5e1;">
        <i class="fas fa-book-open" style="font-size: 40px; color: #94a3b8; margin-bottom: 15px;"></i>
        <h3 style="margin: 0 0 10px 0; color: #475569;">Belum ada Jurnal Umum</h3>
        <p style="margin: 0; color: #94a3b8;">Klik tombol di kanan atas untuk membuat jurnal pertama Anda.</p>
    </div>
    @endforelse
</div>
@endsection