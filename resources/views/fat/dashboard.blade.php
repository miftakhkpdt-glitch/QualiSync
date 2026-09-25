@extends('layouts.staff-layout')

@section('konten')
<div style="padding: 30px; background-color: #f8fafc; min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 24px; font-weight: bold; color: #1e293b; margin: 0 0 5px 0;">
            <i class="fas fa-chart-pie" style="color: #2563eb; margin-right: 8px;"></i> Dashboard Keuangan (FAT)
        </h2>
        <p style="color: #64748b; font-size: 14px; margin: 0;">Ringkasan performa keuangan dan arus kas perusahaan secara real-time.</p>
    </div>

    <!-- TIER 1: HIGHLIGHT UTAMA (Kas, Laba, Opex) -->
    <div style="display: flex; gap: 20px; margin-bottom: 40px; flex-wrap: wrap;">
        <!-- Saldo Kas -->
        <div style="flex: 1.2; min-width: 250px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);">
            <p style="font-size: 13px; font-weight: 800; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 1px; color: #bfdbfe;">Total Saldo Kas & Bank</p>
            <h3 style="font-size: 32px; font-weight: 900; margin: 0;">Rp {{ number_format($saldoKasBank, 0, ',', '.') }}</h3>
            <p style="margin: 10px 0 0 0; font-size: 12px; color: #93c5fd;"><i class="fas fa-wallet"></i> Dana riil tersedia di rekening</p>
        </div>

        <!-- Laba Bersih Bulan Ini -->
        <div style="flex: 1; min-width: 250px; background: linear-gradient(135deg, #14532d, #16a34a); color: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(22, 163, 74, 0.3);">
            <p style="font-size: 13px; font-weight: 800; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 1px; color: #bbf7d0;">Laba Bersih Bulan Ini</p>
            <h3 style="font-size: 28px; font-weight: 900; margin: 0;">Rp {{ number_format($labaBersihBulanIni, 0, ',', '.') }}</h3>
            <p style="margin: 10px 0 0 0; font-size: 12px; color: #86efac;"><i class="fas fa-chart-line"></i> Estimasi profit {{ $namaBulanIni }}</p>
        </div>

        <!-- Biaya Operasional (OPEX) -->
        <div style="flex: 1; min-width: 250px; background: white; padding: 25px; border-radius: 12px; border-left: 6px solid #f59e0b; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <p style="color: #64748b; font-size: 13px; font-weight: 800; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 1px;">Biaya Operasional (OPEX)</p>
            <h3 style="color: #0f172a; font-size: 26px; font-weight: 900; margin: 0;">Rp {{ number_format($opexBulanIni, 0, ',', '.') }}</h3>
            <p style="margin: 10px 0 0 0; font-size: 12px; color: #94a3b8;"><i class="fas fa-file-invoice-dollar"></i> Pengeluaran {{ $namaBulanIni }}</p>
        </div>
    </div>

    <!-- TIER 2: PIUTANG (Uang Masuk) -->
    <div style="margin: 30px 0 20px 0; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
        <h3 style="font-size: 18px; font-weight: bold; color: #475569; margin: 0;">
            <i class="fas fa-hand-holding-usd" style="color: #10b981; margin-right: 8px;"></i> Ringkasan Piutang (Accounts Receivable)
        </h3>
    </div>
    <div style="display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 220px; background: white; padding: 20px; border-radius: 10px; border-left: 5px solid #10b981; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <p style="color: #64748b; font-size: 12px; font-weight: bold; margin: 0 0 5px 0;">TOTAL PIUTANG DIBAYAR</p>
            <h3 style="color: #0f172a; font-size: 22px; margin: 0;">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</h3>
        </div>
        <div style="flex: 1; min-width: 220px; background: white; padding: 20px; border-radius: 10px; border-left: 5px solid #ef4444; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <p style="color: #64748b; font-size: 12px; font-weight: bold; margin: 0 0 5px 0;">PIUTANG BELUM LUNAS</p>
            <h3 style="color: #0f172a; font-size: 22px; margin: 0;">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</h3>
        </div>
        <div style="flex: 1; min-width: 220px; background: white; padding: 20px; border-radius: 10px; border-left: 5px solid #f59e0b; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <p style="color: #64748b; font-size: 12px; font-weight: bold; margin: 0 0 5px 0;">INVOICE CUSTOMER PENDING</p>
            <h3 style="color: #0f172a; font-size: 22px; margin: 0;">{{ $invoicePending }} Dokumen</h3>
        </div>
    </div>

    <!-- TIER 3: HUTANG (Uang Keluar) -->
    <div style="margin: 30px 0 20px 0; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
        <h3 style="font-size: 18px; font-weight: bold; color: #475569; margin: 0;">
            <i class="fas fa-file-invoice" style="color: #6d28d9; margin-right: 8px;"></i> Ringkasan Hutang (Accounts Payable)
        </h3>
    </div>
    <div style="display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 220px; background: white; padding: 20px; border-radius: 10px; border-left: 5px solid #6d28d9; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <p style="color: #64748b; font-size: 12px; font-weight: bold; margin: 0 0 5px 0;">HUTANG DIBAYAR (LUNAS)</p>
            <h3 style="color: #0f172a; font-size: 22px; margin: 0;">Rp {{ number_format($totalHutangDibayar, 0, ',', '.') }}</h3>
        </div>
        <div style="flex: 1; min-width: 220px; background: white; padding: 20px; border-radius: 10px; border-left: 5px solid #ea580c; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <p style="color: #64748b; font-size: 12px; font-weight: bold; margin: 0 0 5px 0;">KEWAJIBAN BELUM DIBAYAR</p>
            <h3 style="color: #0f172a; font-size: 22px; margin: 0;">Rp {{ number_format($totalHutangPending, 0, ',', '.') }}</h3>
        </div>
        <div style="flex: 1; min-width: 220px; background: white; padding: 20px; border-radius: 10px; border-left: 5px solid #64748b; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <p style="color: #64748b; font-size: 12px; font-weight: bold; margin: 0 0 5px 0;">TAGIHAN VENDOR MASUK</p>
            <h3 style="color: #0f172a; font-size: 22px; margin: 0;">{{ $apPending }} Dokumen</h3>
        </div>
    </div>

</div>
@endsection