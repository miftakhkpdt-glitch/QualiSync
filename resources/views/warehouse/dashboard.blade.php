@extends('layouts.staff-layout')

@section('title', 'Dashboard Warehouse - PT KIMPAI DYNA TUBE')

@section('konten')
<div style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    
    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 24px; color: #334155; margin: 0;">
            <i class="fas fa-warehouse" style="color: #0ea5e9;"></i> Dashboard Utama Warehouse
        </h2>
        <p style="color: #64748b; font-size: 14px; margin-top: 5px;">Pantauan real-time stok fisik, kedatangan barang, dan mutasi antar departemen.</p>
    </div>

    <!-- ================= WIDGET ANGKA RANGKUMAN ================= -->
    <div style="display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap;">
        <!-- Card 1: Total Item -->
        <div style="flex: 1; min-width: 250px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 5px solid #3b82f6;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="color: #64748b; font-size: 13px; font-weight: bold; text-transform: uppercase;">Total Item Tersimpan</div>
                <i class="fas fa-boxes" style="color: #93c5fd; font-size: 24px;"></i>
            </div>
            <div style="font-size: 32px; font-weight: bold; color: #1e293b; margin-top: 10px;">
                {{ $totalItem ?? 0 }} <span style="font-size: 14px; font-weight: normal; color: #94a3b8;">Batch/Item</span>
            </div>
        </div>

        <!-- Card 2: Pending Approval -->
        <div style="flex: 1; min-width: 250px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 5px solid #ef4444;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="color: #64748b; font-size: 13px; font-weight: bold; text-transform: uppercase;">Menunggu Approval</div>
                <i class="fas fa-clipboard-check" style="color: #fca5a5; font-size: 24px;"></i>
            </div>
            <div style="font-size: 32px; font-weight: bold; color: #1e293b; margin-top: 10px;">
                {{ $pendingApproval ?? 0 }} <span style="font-size: 14px; font-weight: normal; color: #94a3b8;">Tugas</span>
            </div>
        </div>

        <!-- Card 3: Kedatangan Hari Ini -->
        <div style="flex: 1; min-width: 250px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 5px solid #10b981;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="color: #64748b; font-size: 13px; font-weight: bold; text-transform: uppercase;">Kedatangan Hari Ini</div>
                <i class="fas fa-truck-loading" style="color: #6ee7b7; font-size: 24px;"></i>
            </div>
            <div style="font-size: 32px; font-weight: bold; color: #1e293b; margin-top: 10px;">
                {{ $incomingHariIni ?? 0 }} <span style="font-size: 14px; font-weight: normal; color: #94a3b8;">Transaksi</span>
            </div>
        </div>
    </div>

    <!-- ================= AREA DUA KOLOM: MUTASI & STOK KRITIS ================= -->
    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
        
        <!-- KOLOM KIRI: Tabel 5 Mutasi Terbaru -->
        <div style="flex: 2; min-width: 500px; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; color: #334155; font-size: 16px;">
                    <i class="fas fa-exchange-alt" style="color: #64748b; margin-right: 5px;"></i> Riwayat Mutasi Terbaru
                </h3>
                <a href="{{ route('warehouse.mutasi.approval.index') }}" style="font-size: 12px; color: #0ea5e9; text-decoration: none; font-weight: bold;">Lihat Semua <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 12px;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid #cbd5e1;">
                            <th style="padding: 12px 10px; color: #475569;">MM / Item Name</th>
                            <th style="padding: 12px 10px; color: #475569;">Rute Mutasi</th>
                            <th style="padding: 12px 10px; color: #475569; text-align: right;">Qty</th>
                            <th style="padding: 12px 10px; color: #475569; text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mutasiTerbaru ?? [] as $mutasi)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 10px;">
                                <div style="font-weight: bold; color: #1e293b;">{{ $mutasi->mm }}</div>
                                <div style="font-size: 10px; color: #64748b;">{{ \Illuminate\Support\Str::limit($mutasi->item_name, 25) }}</div>
                            </td>
                            <td style="padding: 10px;">
                                <span style="color: #ef4444; font-weight: bold;">{{ $mutasi->dari_dept }}</span> 
                                <i class="fas fa-arrow-right" style="color: #cbd5e1; font-size: 10px; margin: 0 4px;"></i> 
                                <span style="color: #10b981; font-weight: bold;">{{ $mutasi->ke_dept }}</span>
                            </td>
                            <td style="padding: 10px; font-weight: bold; text-align: right; color: #0f172a;">{{ number_format($mutasi->qty, 2) }}</td>
                            <td style="padding: 10px; text-align: center;">
                                @if($mutasi->status_approval == 'Approved')
                                    <span style="background: #dcfce7; color: #166534; padding: 3px 8px; border-radius: 20px; font-size: 10px; font-weight: bold;">Approved</span>
                                @elseif($mutasi->status_approval == 'Rejected')
                                    <span style="background: #fee2e2; color: #991b1b; padding: 3px 8px; border-radius: 20px; font-size: 10px; font-weight: bold;">Rejected</span>
                                @else
                                    <span style="background: #fef3c7; color: #92400e; padding: 3px 8px; border-radius: 20px; font-size: 10px; font-weight: bold;">Pending</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 20px; color: #94a3b8; font-style: italic;">Belum ada aktivitas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- KOLOM KANAN: Tabel Peringatan Stok Kritis -->
        <div style="flex: 1; min-width: 300px; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-top: 4px solid #ef4444;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; color: #b91c1c; font-size: 16px;">
                    <i class="fas fa-exclamation-triangle" style="margin-right: 5px;"></i> Peringatan Stok Kritis
                </h3>
            </div>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 12px;">
                    <thead>
                        <tr style="background: #fef2f2; border-bottom: 2px solid #fecaca;">
                            <th style="padding: 12px 10px; color: #991b1b;">No. MM</th>
                            <th style="padding: 12px 10px; color: #991b1b; text-align: right;">Sisa Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stokKritis ?? [] as $stok)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 12px 10px; font-weight: bold; color: #1e293b;">{{ $stok->no_mm }}</td>
                            <td style="padding: 12px 10px; text-align: right;">
                                <span style="background: #fee2e2; color: #b91c1c; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;">
                                    {{ number_format($stok->total_qty, 2) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" style="text-align: center; padding: 20px; color: #10b981; font-weight: bold;">
                                <i class="fas fa-check-circle" style="font-size: 20px; display: block; margin-bottom: 5px;"></i>
                                Stok aman.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 15px; text-align: center;">
                <a href="{{ route('warehouse.stok.rekap') }}" style="font-size: 11px; color: #64748b; text-decoration: underline;">Cek Seluruh Stok Material</a>
            </div>
        </div>

    </div>

</div>
@endsection