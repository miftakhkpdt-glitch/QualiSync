@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 1400px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 25px;">
        <h3 style="margin: 0; color: #1e293b; font-size: 20px;">
            <i class="fas fa-clipboard-list"></i> Rekapan Stok All Material
        </h3>
        <button onclick="window.print()" style="background: #3b82f6; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 13px;">
            <i class="fas fa-print"></i> Cetak Laporan
        </button>
    </div>

    <!-- ========================================== -->
    <!-- BAGIAN 1: STOK AKTUAL (RINCIAN PER BATCH)  -->
    <!-- ========================================== -->
    <div style="margin-bottom: 50px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 15px; flex-wrap: wrap; gap: 15px;">
            <h4 style="color: #0f172a; margin: 0; font-size: 16px;"><i class="fas fa-boxes"></i> 1. Ringkasan Stok Fisik Rinci Per Batch</h4>
            
            <!-- FORM FILTER DENGAN KOLOM PENCARIAN TERPISAH -->
            <form action="{{ url()->current() }}" method="GET" style="display: flex; gap: 10px; align-items: center; background: #f8fafc; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                
                <div>
                    <label style="font-size: 11px; font-weight: bold; color: #475569; display: block; margin-bottom: 3px;">Kategori Produk</label>
                    <select name="stok_kategori" style="padding: 7px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 12px;">
                        <option value="">-- Semua Kategori --</option>
                        <option value="Raw Material" {{ request('stok_kategori') == 'Raw Material' ? 'selected' : '' }}>Raw Material</option>
                        <option value="Finish Good" {{ request('stok_kategori') == 'Finish Good' ? 'selected' : '' }}>Finish Good</option>
                    </select>
                </div>
                
                <div>
                    <label style="font-size: 11px; font-weight: bold; color: #475569; display: block; margin-bottom: 3px;">Cari MM / Nama</label>
                    <input type="text" name="stok_search" value="{{ request('stok_search') }}" placeholder="Ketik MM / Nama..." style="padding: 7px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 12px; width: 150px;">
                </div>

                <div>
                    <label style="font-size: 11px; font-weight: bold; color: #475569; display: block; margin-bottom: 3px;">Cari No. Batch</label>
                    <input type="text" name="batch_search" value="{{ request('batch_search') }}" placeholder="Ketik No. Batch..." style="padding: 7px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 12px; width: 150px;">
                </div>
                
                <div style="display: flex; gap: 5px; margin-top: 17px;">
                    <button type="submit" style="background: #0ea5e9; color: white; border: none; padding: 7px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: bold;">Filter Stok</button>
                    <a href="{{ url()->current() }}" style="background: #94a3b8; color: white; padding: 7px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: bold; display: flex; align-items: center; justify-content: center;">Reset</a>
                </div>
            </form>
        </div>

        <div style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 6px;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead>
                    <tr style="background: #1e293b; color: white;">
                        <th style="padding: 12px 10px; text-align: center;">No</th>
                        <th style="padding: 12px 10px; text-align: center;">Status Stok</th>
                        <th style="padding: 12px 10px;">No. MM</th>
                        <th style="padding: 12px 10px;">No. Batch</th>
                        <th style="padding: 12px 10px;">Nama Material</th>
                        <th style="padding: 12px 10px; text-align: center;">Kategori Produk</th>
                        <th style="padding: 12px 10px; text-align: right;">Kuantitas Fisik</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalSemuaQty = 0; @endphp
                    @forelse($stokList as $index => $stok)
                        @php $totalSemuaQty += $stok->qty; @endphp
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 10px; text-align: center;">{{ $index + 1 }}</td>
                            <td style="padding: 10px; text-align: center;">
                                <span style="color: #0369a1; font-weight: bold; font-size: 12px;">Aktif</span>
                            </td>
                            <td style="padding: 10px; font-weight: bold;">{{ $stok->no_mm }}</td>
                            <td style="padding: 10px;">
                                <span style="background: #f1f5f9; border: 1px solid #cbd5e1; padding: 3px 6px; border-radius: 4px; font-weight: bold; font-size: 12px; color: #334155;">
                                    {{ $stok->batch }}
                                </span>
                            </td>
                            <td style="padding: 10px;">{{ $stok->nama_material ?? '-' }}</td>
                            <td style="padding: 10px; text-align: center;">
                                <span style="background: #fef3c7; color: #b45309; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">
                                    {{ $stok->kategori ?? '-' }}
                                 </span>
                            </td>
                            <td style="padding: 10px; text-align: right; font-weight: bold; color: #16a34a;">
                                {{ number_format($stok->qty, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 20px; color: #64748b;">Belum ada data stok yang tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($stokList) > 0)
                <tfoot>
                    <tr style="background: #f1f5f9; font-weight: bold;">
                        <td colspan="6" style="padding: 12px 10px; text-align: right; color: #0f172a;">TOTAL AKUMULASI SELURUH ITEM TERFILTER:</td>
                        <td style="padding: 12px 10px; text-align: right; color: #b91c1c; font-size: 14px;">{{ number_format($totalSemuaQty, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

</div>
@endsection