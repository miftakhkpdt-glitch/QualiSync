@extends('layouts.staff-layout')

@section('title', 'Parameter Batas Stok (MRP) - PT KIMPAI DYNA TUBE')

@section('konten')
<div style="padding: 20px; width: 100%; box-sizing: border-box;">
    
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
    <div>
        <h2 style="font-size: 22px; color: #1e293b; margin: 0;"><i class="fas fa-sliders-h" style="color: #f59e0b;"></i> Parameter Batas Stok MRP</h2>
        <p style="color: #64748b; font-size: 13px; margin-top: 5px;">Atur batas kritis (ROP) dan target maksimal stok untuk memicu alarm produksi.</p>
    </div>

    <!-- KOTAK PENCARIAN (FILTER MANUAL) -->
    <form action="{{ route('ppic.mrp_parameter.index') }}" method="GET" style="display: flex; gap: 10px; align-items: center;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. MM atau Nama..." style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px; width: 250px;">
        <button type="submit" style="background: #3b82f6; color: white; border: none; padding: 8px 15px; border-radius: 4px; font-weight: bold; cursor: pointer;">
            <i class="fas fa-search"></i> Cari
        </button>
        @if(request('search'))
            <a href="{{ route('ppic.mrp_parameter.index') }}" style="background: #ef4444; color: white; padding: 8px 15px; border-radius: 4px; font-weight: bold; text-decoration: none; font-size: 13px;">Reset</a>
        @endif
    </form>
</div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 12px 15px; border-radius: 4px; margin-bottom: 20px; font-weight: bold; border-left: 4px solid #22c55e;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div style="background: #fff; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow-x: auto; border: 1px solid #e2e8f0; border-top: 4px solid #f59e0b;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #cbd5e1;">
                    <th style="padding: 15px; color: #475569; width: 15%;">No. MM</th>
                    <th style="padding: 15px; color: #475569; width: 35%;">Nama Item / Material</th>
                    <th style="padding: 15px; color: #475569; width: 20%;">Batas Kritis (ROP)</th>
                    <th style="padding: 15px; color: #475569; width: 20%;">Target (Max Stock)</th>
                    <th style="padding: 15px; color: #475569; text-align: center; width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;">
                    <td style="padding: 15px; font-weight: bold; color: #64748b;">{{ $item->no_mm }}</td>
                    
                    <!-- UBAH DISINI: Menggunakan nama_material -->
                    <td style="padding: 15px; font-weight: bold; color: #1e293b;">
                        {{ $item->nama_material ?? '-' }}
                    </td>
                    
                    <form action="{{ route('ppic.mrp_parameter.update', $item->id) }}" method="POST">
                        @csrf
                        <td style="padding: 10px;">
                            <input type="number" name="rop" value="{{ $item->rop ?? 0 }}" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px;">
                        </td>
                        <td style="padding: 10px;">
                            <input type="number" name="max_stock" value="{{ $item->max_stock ?? 0 }}" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px;">
                        </td>
                        <td style="padding: 10px; text-align: center;">
                            <button type="submit" style="background: #10b981; color: white; border: none; padding: 8px 12px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 12px;">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </td>
                    </form>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px; color: #94a3b8; font-style: italic;">Data Master Material masih kosong.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection