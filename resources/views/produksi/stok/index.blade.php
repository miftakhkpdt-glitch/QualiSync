@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 1400px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 25px;">
        <h3 style="margin: 0; color: #1e293b; font-size: 20px;">
            <i class="fas fa-boxes"></i> Daftar Stok Fisik Saat Ini (Produksi Dept)
        </h3>
    </div>

    <!-- FORM FILTER -->
    <form action="{{ url()->current() }}" method="GET" style="background: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 20px; display: flex; gap: 15px; align-items: flex-end;">
        <div>
            <label style="font-size: 12px; font-weight: bold; color: #475569;">Kategori Produk</label>
            <select name="kategori" style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px; width: 160px; margin-top: 5px;">
                <option value="">-- Semua Kategori --</option>
                <option value="Raw Material" {{ request('kategori') == 'Raw Material' ? 'selected' : '' }}>Raw Material</option>
                <option value="Finish Good" {{ request('kategori') == 'Finish Good' ? 'selected' : '' }}>Finish Good</option>
            </select>
        </div>
        <div>
            <label style="font-size: 12px; font-weight: bold; color: #475569;">Pencarian</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari MM / Nama / Batch..." style="width: 250px; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px; margin-top: 5px;">
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="submit" style="background: #0ea5e9; color: white; border: none; padding: 9px 15px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold;">Filter</button>
            <a href="{{ url()->current() }}" style="background: #94a3b8; color: white; padding: 9px 15px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold;">Reset</a>
        </div>
    </form>

    <!-- TABEL DATA STOK -->
    <div style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 6px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #1e293b; color: white;">
                    <th style="padding: 12px 10px; text-align: center;">No</th>
                    <th style="padding: 12px 10px;">No. MM</th>
                    <th style="padding: 12px 10px;">Nama Material</th>
                    <th style="padding: 12px 10px; text-align: center;">Kategori</th>
                    <th style="padding: 12px 10px; text-align: center;">Batch</th>
                    <th style="padding: 12px 10px; text-align: right;">Total Kuantitas Fisik</th>
                    <th style="padding: 15px; text-align: center;">Tgl Update Terakhir</th>
                </tr>
            </thead>
            <tbody>
                @php $totalSemuaQty = 0; @endphp
                @forelse($stokList as $index => $stok)
                    @php $totalSemuaQty += $stok->qty; @endphp
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 10px; text-align: center; color: #64748b;">{{ $index + 1 }}</td>
                        <td style="padding: 10px; font-weight: bold; color: #1e293b;">{{ $stok->no_mm }}</td>
                        <td style="padding: 10px;">{{ $stok->nama_material ?? '-' }}</td>
                        <td style="padding: 10px; text-align: center;">
                            <span style="background: #fef3c7; color: #b45309; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">
                                {{ $stok->kategori ?? '-' }}
                            </span>
                        </td>
                        <td style="padding: 10px; text-align: center;">
                            <span style="color: #0369a1; font-weight: bold;">{{ $stok->batch ?? '-' }}</span>
                        </td>
                        <td style="padding: 10px; text-align: right; font-weight: bold; color: #16a34a;">
                            {{ number_format($stok->qty, 2) }}
                        </td>
                        <td style="padding: 15px; text-align: center; color: #64748b; font-size: 11px;">
    {{ \Carbon\Carbon::parse($stok->updated_at)->format('d M Y - H:i:s') }}
</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 25px; color: #64748b;">Belum ada data stok di departemen ini.</td>
                    </tr>
                @endforelse
            </tbody>
            @if(count($stokList) > 0)
            <tfoot>
                <tr style="background: #f1f5f9; font-weight: bold;">
                    <td colspan="5" style="padding: 12px 10px; text-align: right; color: #0f172a;">TOTAL AKUMULASI ITEM TERFILTER:</td>
                    <td style="padding: 12px 10px; text-align: right; color: #b91c1c; font-size: 14px;">{{ number_format($totalSemuaQty, 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection