@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 1400px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px;">
        <h3 style="margin: 0; color: #334155;">
            <i class="fas fa-clipboard-list"></i> Rekapan Stok All Material
        </h3>
        <button onclick="window.print()" style="background: #3b82f6; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 13px;">
            <i class="fas fa-print"></i> Cetak Laporan
        </button>
    </div>

    <!-- FORM FILTER -->
    <form action="" method="GET" style="background: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 20px; display: flex; gap: 15px; align-items: flex-end;">
        <div>
            <label style="font-size: 12px; font-weight: bold; color: #475569;">Pencarian (No. MM / Nama)</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik MM / Nama..." style="width: 250px; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="submit" style="background: #0ea5e9; color: white; border: none; padding: 9px 15px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold;">Tampilkan</button>
            <a href="{{ url()->current() }}" style="background: #94a3b8; color: white; padding: 9px 15px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold;">Reset</a>
        </div>
    </form>

    <!-- TABEL REKAPITULASI -->
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #1e293b; color: white;">
                    <th style="padding: 12px 10px; width: 50px; text-align: center;">No</th>
                    <th style="padding: 12px 10px;">Status Kategori</th>
                    <th style="padding: 12px 10px;">No. MM</th>
                    <th style="padding: 12px 10px;">Nama Material</th>
                    <th style="padding: 12px 10px; text-align: right;">Total Kuantitas Fisik</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stok_list as $index => $item)
                <tr style="border-bottom: 1px solid #e2e8f0; background: {{ $loop->iteration % 2 == 0 ? '#f8fafc' : '#ffffff' }};">
                    <td style="padding: 10px; text-align: center;">{{ $index + 1 }}</td>
                    <td style="padding: 10px;">
                        <span style="background: #e0f2fe; color: #0369a1; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;">Stok Aktif</span>
                    </td>
                    <td style="padding: 10px; font-weight: bold;">{{ $item->no_mm }}</td>
                    <td style="padding: 10px;">{{ $item->nama_material ?? '-' }}</td>
                    <td style="padding: 10px; text-align: right; font-weight: bold; font-size: 14px; color: #0f172a;">
                        {{ number_format($item->qty, 2) }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align: center; padding: 30px;">Tidak ada data stok yang ditemukan.</td></tr>
                @endforelse
            </tbody>
            @if(count($stok_list) > 0)
            <tfoot>
                <tr style="background: #f1f5f9; font-weight: bold;">
                    <td colspan="4" style="padding: 12px 10px; text-align: right; text-transform: uppercase;">Total Akumulasi Seluruh Item Terfilter:</td>
                    <td style="padding: 12px 10px; text-align: right; font-size: 15px; color: #b91c1c;">{{ number_format($total_kuantitas, 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection