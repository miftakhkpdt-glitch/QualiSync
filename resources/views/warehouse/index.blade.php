@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 1400px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h3 style="margin-bottom: 5px; color: #334155; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
        <i class="fas fa-boxes"></i> Stok Material Gudang (Pass QC)
    </h3>
    <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">Daftar material di gudang yang sudah lolos verifikasi Quality (Pass).</p>

    <!-- Filter Search -->
    <form action="{{ route('warehouse.index') }}" method="GET" style="display: flex; gap: 8px; align-items: center; margin-bottom: 20px;">
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari No. MM / Nama Material / STPB..." style="padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px; width: 250px;">
        <button type="submit" style="background: #0ea5e9; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px;">
            <i class="fas fa-search"></i> Filter
        </button>
        @if(isset($search) && $search != '')
            <a href="{{ route('warehouse.index') }}" style="background: #64748b; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 13px; display: flex; align-items: center;">
                <i class="fas fa-redo"></i> Reset
            </a>
        @endif
    </form>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #0ea5e9; color: white;">
                    <th style="padding: 10px;">Date</th>
                    <th style="padding: 10px;">MM</th>
                    <th style="padding: 10px;">Item Name</th>
                    <th style="padding: 10px;">Stok (Quantity)</th>
                    <th style="padding: 10px;">Uom</th>
                    <th style="padding: 10px;">STPB No</th>
                    <th style="padding: 10px;">Vendor Name</th>
                    <th style="padding: 10px;">Status QC</th>
                    <th style="padding: 10px;">Lokasi Stok</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materials as $item)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 9px;">{{ $item->date ?? '-' }}</td>
                        <td style="padding: 9px; font-weight: bold;">{{ $item->mm ?? '-' }}</td>
                        <td style="padding: 9px;">{{ $item->item_name ?? '-' }}</td>
                        <td style="padding: 9px; font-weight: bold; color: #16a34a;">{{ number_format($item->quantity ?? 0, 2) }}</td>
                        <td style="padding: 9px;">{{ $item->uom ?? '-' }}</td>
                        <td style="padding: 9px;">{{ $item->stpb_number ?? '-' }}</td>
                        <td style="padding: 9px;">{{ $item->vendor_name ?? '-' }}</td>
                        <td style="padding: 9px;">
                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; background: #dcfce7; color: #15803d;">
                                {{ $item->status_qc }}
                            </span>
                        </td>
                        <td style="padding: 9px;">
                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; background: #e0f2fe; color: #0369a1;">
                                {{ $item->lokasi_stok }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 30px; color: #64748b;">Belum ada stok material yang berstatus Pass QC.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection