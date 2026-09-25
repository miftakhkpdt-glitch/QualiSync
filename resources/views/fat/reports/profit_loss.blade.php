@extends('layouts.staff-layout')

@section('konten')
<div style="padding: 30px; background-color: #f8fafc; min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    <!-- Header -->
    <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-size: 24px; font-weight: bold; color: #1e293b; margin: 0 0 5px 0;">
                <i class="fas fa-chart-bar" style="color: #2563eb; margin-right: 8px;"></i> Laporan Laba Rugi
            </h2>
            <p style="color: #64748b; font-size: 14px; margin: 0;">Konsolidasi Pendapatan dan Beban Perusahaan (Profit & Loss Statement)</p>
        </div>
        
        <!-- BAGIAN TOMBOL EXPORT & PRINT -->
        <div class="no-print" style="display: flex; gap: 10px;">
            <a href="{{ route('fat.reports.profit_loss.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" style="background-color: #10b981; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-size: 14px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 8px; text-decoration: none;">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <button onclick="window.print()" style="background-color: #1e293b; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-size: 14px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Kotak Filter Tanggal (Tidak ikut tercetak saat di-print) -->
    <div class="no-print" style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 25px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
        <form action="{{ route('fat.reports.profit_loss') }}" method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <label style="font-size: 12px; font-weight: bold; color: #64748b; margin-bottom: 8px; display: block;">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; font-family: inherit;">
            </div>
            <div style="flex: 1; min-width: 200px;">
                <label style="font-size: 12px; font-weight: bold; color: #64748b; margin-bottom: 8px; display: block;">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; font-family: inherit;">
            </div>
            <div>
                <button type="submit" style="background-color: #2563eb; color: white; border: none; padding: 11px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; transition: 0.2s;" onmouseover="this.style.backgroundColor='#1d4ed8'" onmouseout="this.style.backgroundColor='#2563eb'">
                    <i class="fas fa-filter" style="margin-right: 5px;"></i> Terapkan
                </button>
            </div>
        </form>
    </div>

    <!-- Kertas Laporan -->
    <div style="background: white; max-width: 800px; margin: 0 auto; padding: 40px; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
        
        <div style="text-align: center; margin-bottom: 40px; border-bottom: 2px solid #1e293b; padding-bottom: 20px;">
            <h1 style="margin: 0; font-size: 22px; color: #0f172a; text-transform: uppercase; letter-spacing: 1px;">PT KIMPAI DYNA TUBES</h1>
            <h2 style="margin: 5px 0 0 0; font-size: 18px; color: #475569; font-weight: normal;">LAPORAN LABA RUGI (PROFIT & LOSS)</h2>
            <p style="margin: 5px 0 0 0; font-size: 14px; color: #64748b;">
                Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</strong>
            </p>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 15px;">
            
            <!-- PENDAPATAN -->
            <tr>
                <td colspan="2" style="font-weight: bold; color: #0f172a; padding: 10px 0; font-size: 16px;">PENDAPATAN (REVENUE)</td>
            </tr>
            <tr>
                <td style="padding: 8px 0 8px 30px; color: #334155;">Pendapatan Penjualan Barang</td>
                <td style="text-align: right; padding: 8px 0; color: #334155;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #cbd5e1;">
                <td style="padding: 10px 0; font-weight: bold; color: #0f172a;">TOTAL PENDAPATAN</td>
                <td style="text-align: right; padding: 10px 0; font-weight: bold; color: #0f172a;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
            </tr>

            <!-- JARAK -->
            <tr><td colspan="2" style="padding: 15px 0;"></td></tr>

            <!-- BEBAN POKOK -->
            <tr>
                <td colspan="2" style="font-weight: bold; color: #0f172a; padding: 10px 0; font-size: 16px;">HARGA POKOK PENJUALAN (COGS)</td>
            </tr>
            <tr>
                <td style="padding: 8px 0 8px 30px; color: #334155;">Pembelian Bahan Baku (Vendor)</td>
                <td style="text-align: right; padding: 8px 0; color: #334155;">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #cbd5e1;">
                <td style="padding: 10px 0; font-weight: bold; color: #0f172a;">TOTAL HARGA POKOK PENJUALAN</td>
                <td style="text-align: right; padding: 10px 0; font-weight: bold; color: #0f172a;">( Rp {{ number_format($totalPembelian, 0, ',', '.') }} )</td>
            </tr>

            <!-- JARAK -->
            <tr><td colspan="2" style="padding: 15px 0;"></td></tr>

            <!-- LABA KOTOR -->
            <tr style="border-top: 2px solid #1e293b; border-bottom: 4px double #1e293b; background-color: #f8fafc;">
                <td style="padding: 15px 10px; font-weight: 900; color: #0f172a; font-size: 18px;">LABA KOTOR (GROSS PROFIT)</td>
                <td style="text-align: right; padding: 15px 10px; font-weight: 900; font-size: 18px; color: {{ $labaKotor >= 0 ? '#16a34a' : '#dc2626' }};">
                    Rp {{ number_format($labaKotor, 0, ',', '.') }}
                </td>
            </tr>
            
            <!-- JARAK -->
            <tr><td colspan="2" style="padding: 15px 0;"></td></tr>

            <!-- BIAYA OPERASIONAL -->
            <tr>
                <td colspan="2" style="font-weight: bold; color: #0f172a; padding: 10px 0; font-size: 16px;">BIAYA OPERASIONAL (OPEX)</td>
            </tr>
            <tr>
                <td style="padding: 8px 0 8px 30px; color: #334155;">Total Pengeluaran (Gaji, Listrik, Internet, dll)</td>
                <td style="text-align: right; padding: 8px 0; color: #334155;">Rp {{ number_format($totalBiayaOperasional, 0, ',', '.') }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #cbd5e1;">
                <td style="padding: 10px 0; font-weight: bold; color: #0f172a;">TOTAL BIAYA OPERASIONAL</td>
                <td style="text-align: right; padding: 10px 0; font-weight: bold; color: #0f172a;">( Rp {{ number_format($totalBiayaOperasional, 0, ',', '.') }} )</td>
            </tr>

            <!-- JARAK -->
            <tr><td colspan="2" style="padding: 15px 0;"></td></tr>

            <!-- LABA BERSIH (NET PROFIT) -->
            <tr style="background-color: {{ $labaBersih >= 0 ? '#16a34a' : '#dc2626' }}; color: white;">
                <td style="padding: 15px 10px; font-weight: 900; font-size: 20px;">LABA BERSIH (NET PROFIT)</td>
                <td style="text-align: right; padding: 15px 10px; font-weight: 900; font-size: 20px;">
                    Rp {{ number_format($labaBersih, 0, ',', '.') }}
                </td>
            </tr>
        </table>

        <div style="margin-top: 40px; font-size: 13px; color: #64748b; text-align: center;">
            <p>* Laporan ini dihasilkan secara otomatis oleh Sistem ERP Kimpai Dyna Tubes.</p>
            <p>* Nilai yang tercantum tidak termasuk Pajak Pertambahan Nilai (PPN 11%).</p>
        </div>

    </div>
</div>

<style>
    @media print {
        body { background: white; margin: 0; padding: 0; }
        .sidebar, .navbar, header, button { display: none !important; }
        .konten-wrapper { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        div[style*="background-color: #f8fafc"] { background-color: white !important; padding: 0 !important; }
        div[style*="max-width: 800px"] { box-shadow: none !important; border: none !important; padding: 0 !important; }
        .no-print { display: none !important; }
    }
</style>
@endsection