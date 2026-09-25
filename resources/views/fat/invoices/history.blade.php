@extends('layouts.staff-layout')

@section('konten')
<div style="padding: 30px; background-color: #f8fafc; min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    <!-- Tampilkan Notifikasi Sukses Jika Ada -->
    @if(session('success'))
    <div style="background-color: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0; font-weight: bold;">
        <i class="fas fa-check-circle" style="margin-right: 5px;"></i> {{ session('success') }}
    </div>
    @endif

    <!-- Header Halaman -->
    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-size: 22px; font-weight: bold; color: #1e293b; margin: 0 0 5px 0;">
                <i class="fas fa-folder-open" style="color: #6366f1; margin-right: 8px;"></i> Daftar Faktur Penjualan
            </h2>
            <p style="color: #64748b; font-size: 14px; margin: 0;">Riwayat seluruh tagihan (Invoice) yang telah diterbitkan ke customer.</p>
        </div>
        
        <!-- Tombol Menuju Antrean (Penting!) -->
        <a href="{{ route('fat.invoices.index') }}" style="background-color: #10b981; color: white; padding: 10px 18px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: bold; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);" onmouseover="this.style.backgroundColor='#059669'" onmouseout="this.style.backgroundColor='#10b981'">
            <i class="fas fa-plus" style="margin-right: 5px;"></i> Buat Invoice Baru
        </a>
    </div>

    <!-- Kotak Tabel -->
    <div style="background: white; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden; border: 1px solid #e2e8f0;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background-color: #f1f5f9; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase;">No. Invoice</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase;">Tanggal</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase;">Customer</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: right;">Total Tagihan</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center;">Status</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr style="border-bottom: 1px solid #e2e8f0; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='white'">
                    <td style="padding: 15px 20px; font-weight: bold; color: #0284c7;">
                        {{ $inv->no_invoice }}
                    </td>
                    <td style="padding: 15px 20px; font-size: 14px; color: #475569;">
                        {{ \Carbon\Carbon::parse($inv->tanggal_invoice)->format('d M Y') }}
                    </td>
                    <td style="padding: 15px 20px; font-size: 14px; font-weight: bold; color: #0f172a;">
                        {{ strtoupper($inv->nama_customer) }}
                    </td>
                    <td style="padding: 15px 20px; font-size: 14px; font-weight: bold; color: #0f172a; text-align: right;">
                        Rp {{ number_format($inv->total_tagihan, 0, ',', '.') }}
                    </td>
                    <td style="padding: 15px 20px; text-align: center;">
                        <!-- Logika Warna Status Pembayaran -->
                        @if($inv->status_pembayaran == 'Unpaid')
                            <span style="background-color: #fee2e2; color: #991b1b; padding: 5px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; border: 1px solid #fecaca;">
                                BELUM LUNAS
                            </span>
                        @elseif($inv->status_pembayaran == 'Partial')
                            <span style="background-color: #fef08a; color: #854d0e; padding: 5px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; border: 1px solid #fde047;">
                                DICICIL
                            </span>
                        @else
                            <span style="background-color: #dcfce7; color: #166534; padding: 5px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; border: 1px solid #bbf7d0;">
                                LUNAS
                            </span>
                        @endif
                    </td>
                    <td style="padding: 15px 20px; text-align: center;">
                        <!-- Tombol Aksi (Print & Detail) -->
                        <a href="{{ route('fat.invoices.print', $inv->id) }}" title="Cetak PDF" style="color: #6366f1; margin-right: 10px; font-size: 16px;"><i class="fas fa-print"></i></a>
                        <a href="{{ route('fat.invoices.show', $inv->id) }}" title="Lihat Detail" style="color: #64748b; font-size: 16px;"><i class="fas fa-eye"></i></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 30px; text-align: center; color: #94a3b8;">
                        Belum ada riwayat Faktur Penjualan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection