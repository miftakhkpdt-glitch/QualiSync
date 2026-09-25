@extends('layouts.staff-layout')

@section('title', 'Request Material WO - PT KIMPAI DYNA TUBE')

@section('konten')
<div style="padding: 20px; max-width: 1000px; margin: 0 auto;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 style="font-size: 24px; color: #334155; margin: 0;">
                <i class="fas fa-clipboard-list" style="color: #0ea5e9;"></i> Rincian Kebutuhan Material
            </h2>
            <p style="color: #64748b; font-size: 14px; margin-top: 5px;">Formulir permintaan pengeluaran stok dari Gudang ke area Produksi.</p>
        </div>
        <a href="{{ route('ppic.work_order.index') }}" style="background: #cbd5e1; color: #334155; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 13px;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Info Work Order -->
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 40px;">
        <div>
            <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: bold;">Nomor Work Order</div>
            <div style="font-size: 16px; font-weight: bold; color: #0f172a;">{{ $wo->no_wo }}</div>
        </div>
        <div>
            <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: bold;">Barang Jadi (Finish Goods)</div>
            <div style="font-size: 16px; font-weight: bold; color: #0284c7;">[{{ $wo->salesOrder->no_mm }}] {{ $wo->salesOrder->nama_produk }}</div>
        </div>
        <div>
            <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: bold;">Target Produksi</div>
            <div style="font-size: 16px; font-weight: bold; color: #10b981;">{{ number_format($targetQty, 0, ',', '.') }} {{ $wo->salesOrder->satuan ?? 'Pcs' }}</div>
        </div>
    </div>

    <!-- Tabel Hasil Kalkulasi BOM -->
    <div style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <h3 style="margin-top: 0; color: #334155; font-size: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
            Daftar Bahan Baku (Raw Material)
        </h3>
        
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th style="padding: 12px; color: #475569;">MM Komponen</th>
                    <th style="padding: 12px; color: #475569;">Nama Material</th>
                    <th style="padding: 12px; color: #475569; text-align: center;">Kebutuhan per 1 Pcs</th>
                    <th style="padding: 12px; color: #0f172a; text-align: center; background: #e0f2fe; border-radius: 4px 4px 0 0;">Total Minta ke Gudang</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kebutuhanMaterial ?? [] as $item)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 12px; font-weight: bold; color: #334155;">{{ $item->component_mm }}</td>
                    <td style="padding: 12px;">{{ $item->nama_komponen }}</td>
                    <td style="padding: 12px; text-align: center; color: #64748b;">
                        {{ floatval($item->qty_usage) }} {{ $item->satuan }}
                    </td>
                    <td style="padding: 12px; text-align: center; font-weight: bold; color: #0369a1; background: #f0f9ff;">
                        {{ number_format($item->total_kebutuhan, 2, ',', '.') }} {{ $item->satuan }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 25px; color: #ef4444; font-weight: bold;">
                        <i class="fas fa-exclamation-triangle"></i> Resep (BOM) untuk barang ini belum disetting di database!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- ========================================== -->
        <!-- TOMBOL SUBMIT KE GUDANG (BARU DITAMBAHKAN) -->
        <!-- ========================================== -->
        <div style="margin-top: 25px; border-top: 2px solid #e2e8f0; padding-top: 20px; text-align: right;">
            <form action="{{ url('/ppic/work-order/'.$wo->id.'/send-request') }}" method="POST">
                @csrf
                <!-- Tombol disembunyikan jika BOM kosong agar tidak error -->
                @if(!empty($kebutuhanMaterial) && count($kebutuhanMaterial) > 0)
                    <button type="submit" onclick="return confirm('Kirim daftar permintaan material ini ke Gudang sekarang?')" style="background: #10b981; color: white; border: none; padding: 12px 25px; border-radius: 5px; font-weight: bold; cursor: pointer; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3); font-size: 14px;">
                        <i class="fas fa-paper-plane" style="margin-right: 8px;"></i> Kirim Permintaan ke Gudang
                    </button>
                @endif
            </form>
        </div>

    </div>
</div>
@endsection