@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 1200px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">

    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 20px;">
        <h3 style="margin: 0; color: #1e293b; font-size: 20px;">
            <i class="fas fa-history" style="color: #0ea5e9;"></i> Riwayat Purchase Order (PO) Terbit
        </h3>
    </div>

    <!-- KOTAK PENCARIAN & FILTER -->
    <form action="{{ route('purchasing.riwayat_po') }}" method="GET" style="background: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #cbd5e1; margin-bottom: 20px; display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
        
        <div>
            <label style="display: block; font-size: 12px; font-weight: bold; color: #64748b; margin-bottom: 5px;">Tanggal PO</label>
            <input type="date" name="tanggal_po" value="{{ request('tanggal_po') }}" style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box; width: 140px;">
        </div>
        
        <div>
            <label style="display: block; font-size: 12px; font-weight: bold; color: #64748b; margin-bottom: 5px;">Nama Vendor</label>
            <input type="text" name="vendor_name" value="{{ request('vendor_name') }}" placeholder="Cari vendor..." style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box; width: 200px;">
        </div>
        
        <div>
            <label style="display: block; font-size: 12px; font-weight: bold; color: #64748b; margin-bottom: 5px;">MM / Material</label>
            <input type="text" name="no_mm" value="{{ request('no_mm') }}" placeholder="Cari No MM..." style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box; width: 150px;">
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button type="submit" style="background: #0ea5e9; color: white; border: none; padding: 9px 15px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold;">
                <i class="fas fa-filter"></i> Terapkan
            </button>
            <a href="{{ route('purchasing.riwayat_po') }}" style="background: #e2e8f0; color: #334155; text-decoration: none; padding: 9px 15px; border-radius: 4px; font-size: 13px; font-weight: bold;">
                <i class="fas fa-sync"></i> Reset
            </a>
        </div>
    </form>

    <!-- TABEL DATA RIWAYAT -->
    <div style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 6px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #1e293b; color: white;">
                    <th style="padding: 12px 10px;">Tanggal / No PR</th>
                    <th style="padding: 12px 10px;">Vendor Supplier</th>
                    <th style="padding: 12px 10px;">Item Material (MM)</th>
                    <th style="padding: 12px 10px;">Qty & Harga Satuan</th>
                    <th style="padding: 12px 10px; text-align: center;">ETA (Tiba)</th>
                    <th style="padding: 12px 10px; text-align: center;">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pos as $po)
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 10px;">
                        <span style="font-weight: bold; color: #1e293b;">{{ date('d M Y', strtotime($po->tanggal_po)) }}</span><br>
                        <span style="font-size: 11px; font-weight: bold; color: #0284c7;">{{ $po->no_pr }}</span>
                    </td>
                    <td style="padding: 10px; font-weight: bold; color: #334155;">
                        {{ $po->vendor_name }}
                    </td>
                    <td style="padding: 10px;">
                        <b>{{ $po->no_mm }}</b><br>
                        <small>{{ $po->nama_material ?? '-' }}</small>
                    </td>
                    <td style="padding: 10px;">
                        <span style="color: #b91c1c; font-weight: bold;">{{ number_format($po->qty, 0, ',', '.') }} {{ $po->satuan }}</span><br>
                        <span style="font-size: 11px; color: #15803d; font-weight: bold;">Rp {{ number_format($po->harga_satuan, 0, ',', '.') }}</span>
                    </td>
                    <td style="padding: 10px; text-align: center; color: #64748b;">
                        {{ $po->eta ? date('d M Y', strtotime($po->eta)) : '-' }}
                    </td>
                    <td style="padding: 10px; text-align: center;">
                       <a href="{{ route('purchasing.print_po', $po->id) }}" target="_blank" style="background: #10b981; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: bold;">
                         <i class="fas fa-print"></i> Cetak
                       </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px; color: #64748b;">Belum ada Riwayat PO, atau data tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection