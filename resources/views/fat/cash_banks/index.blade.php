@extends('layouts.staff-layout')

@section('konten')
<div style="padding: 30px; background-color: #f8fafc; min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    @if(session('success'))
    <div style="background-color: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0; font-weight: bold;">
        <i class="fas fa-check-circle" style="margin-right: 5px;"></i> {{ session('success') }}
    </div>
    @endif

    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 22px; font-weight: bold; color: #1e293b; margin: 0 0 5px 0;">
            <i class="fas fa-money-check-alt" style="color: #3b82f6; margin-right: 8px;"></i> Buku Kas & Bank
        </h2>
        <p style="color: #64748b; font-size: 14px; margin: 0;">Pantau arus kas riil perusahaan (Uang Masuk & Keluar).</p>
    </div>

    <!-- Ringkasan Saldo -->
    <div style="display: flex; gap: 20px; margin-bottom: 30px;">
        <!-- Saldo Akhir (Biru) -->
        <div style="flex: 2; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);">
            <p style="margin: 0 0 10px 0; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; color: #bfdbfe;">Total Saldo Aktif Rekening</p>
            <h2 style="margin: 0; font-size: 32px; font-weight: 900;">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</h2>
        </div>
        
        <!-- Total Masuk -->
        <div style="flex: 1; background: white; padding: 20px; border-radius: 12px; border-left: 5px solid #10b981; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <p style="margin: 0 0 5px 0; font-size: 12px; color: #64748b; font-weight: bold; text-transform: uppercase;">Total Uang Masuk</p>
            <h3 style="margin: 0; font-size: 20px; color: #059669;">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</h3>
        </div>

        <!-- Total Keluar -->
        <div style="flex: 1; background: white; padding: 20px; border-radius: 12px; border-left: 5px solid #ef4444; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <p style="margin: 0 0 5px 0; font-size: 12px; color: #64748b; font-weight: bold; text-transform: uppercase;">Total Uang Keluar</p>
            <h3 style="margin: 0; font-size: 20px; color: #dc2626;">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</h3>
        </div>
    </div>

    <!-- Dua Kolom: Form & Tabel -->
    <div style="display: flex; gap: 30px; align-items: flex-start;">
        
        <!-- Form Input Transaksi -->
        <div style="flex: 1; background: white; padding: 25px; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <h3 style="margin: 0 0 20px 0; font-size: 16px; color: #1e293b; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">Catat Transaksi Manual</h3>
            <form action="{{ route('fat.cash_banks.store') }}" method="POST">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 12px; font-weight: bold; color: #64748b;">Tanggal *</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 5px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 12px; font-weight: bold; color: #64748b;">Tipe Transaksi *</label>
                    <select name="tipe" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 5px;">
                        <option value="Masuk">Uang Masuk (Debit)</option>
                        <option value="Keluar">Uang Keluar (Kredit)</option>
                    </select>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 12px; font-weight: bold; color: #64748b;">Keterangan *</label>
                    <input type="text" name="keterangan" placeholder="Contoh: Pembayaran ATK, Tarik Tunai" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 5px;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="font-size: 12px; font-weight: bold; color: #64748b;">Nominal (Rp) *</label>
                    <input type="number" name="nominal" placeholder="0" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 5px; font-weight: bold;">
                </div>
                <button type="submit" style="width: 100%; background-color: #3b82f6; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: bold; cursor: pointer;">Simpan Transaksi</button>
            </form>
        </div>

        <!-- Tabel Riwayat -->
        <div style="flex: 2; background: white; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 15px; font-size: 12px; color: #475569;">TANGGAL</th>
                        <th style="padding: 15px; font-size: 12px; color: #475569;">KETERANGAN</th>
                        <th style="padding: 15px; font-size: 12px; color: #475569; text-align: center;">TIPE</th>
                        <th style="padding: 15px; font-size: 12px; color: #475569; text-align: right;">NOMINAL</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ledgers as $ledger)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 15px; font-size: 13px; color: #64748b;">{{ \Carbon\Carbon::parse($ledger->tanggal)->format('d/m/Y') }}</td>
                        <td style="padding: 15px; font-size: 14px; font-weight: bold; color: #0f172a;">{{ $ledger->keterangan }}</td>
                        <td style="padding: 15px; text-align: center;">
                            @if($ledger->tipe == 'Masuk')
                                <span style="color: #10b981; background: #ecfdf5; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">+ MASUK</span>
                            @else
                                <span style="color: #ef4444; background: #fef2f2; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">- KELUAR</span>
                            @endif
                        </td>
                        <td style="padding: 15px; font-size: 14px; font-weight: bold; text-align: right; color: {{ $ledger->tipe == 'Masuk' ? '#10b981' : '#ef4444' }};">
                            Rp {{ number_format($ledger->nominal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="padding: 20px; text-align: center; color: #94a3b8;">Belum ada riwayat transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection