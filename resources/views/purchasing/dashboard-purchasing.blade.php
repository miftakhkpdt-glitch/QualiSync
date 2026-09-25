@extends('layouts.staff-layout')

@section('konten')
<div style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    
    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 24px; color: #334155; margin: 0;">
            <i class="fas fa-chart-line" style="color: #0ea5e9;"></i> Dashboard Utama Purchasing
        </h2>
        <p style="color: #64748b; font-size: 14px; margin-top: 5px;">Rangkuman performa dan status dokumen pengadaan material.</p>
    </div>

    <!-- ================= WIDGET ANGKA RANGKUMAN ================= -->
    <div style="display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap;">
        <!-- Card 1: PR Pending -->
        <div style="flex: 1; min-width: 250px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 5px solid #f59e0b;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="color: #64748b; font-size: 13px; font-weight: bold; text-transform: uppercase;">PR Menunggu Diproses</div>
                <i class="fas fa-file-signature" style="color: #fcd34d; font-size: 24px;"></i>
            </div>
            <div style="font-size: 32px; font-weight: bold; color: #1e293b; margin-top: 10px;">
                {{ $prPending ?? 0 }} <span style="font-size: 14px; font-weight: normal; color: #94a3b8;">Dokumen</span>
            </div>
        </div>

        <!-- Card 2: PO Bulan Ini -->
        <div style="flex: 1; min-width: 250px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 5px solid #10b981;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="color: #64748b; font-size: 13px; font-weight: bold; text-transform: uppercase;">PO Terbit Bulan Ini</div>
                <i class="fas fa-file-invoice-dollar" style="color: #6ee7b7; font-size: 24px;"></i>
            </div>
            <div style="font-size: 32px; font-weight: bold; color: #1e293b; margin-top: 10px;">
                {{ $poBulanIni ?? 0 }} <span style="font-size: 14px; font-weight: normal; color: #94a3b8;">Dokumen</span>
            </div>
        </div>

        <!-- Card 3: Outstanding PO -->
        <div style="flex: 1; min-width: 250px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 5px solid #ef4444;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="color: #64748b; font-size: 13px; font-weight: bold; text-transform: uppercase;">Outstanding PO</div>
                <i class="fas fa-truck-loading" style="color: #fca5a5; font-size: 24px;"></i>
            </div>
            <div style="font-size: 32px; font-weight: bold; color: #1e293b; margin-top: 10px;">
                {{ $poOutstanding ?? 0 }} <span style="font-size: 14px; font-weight: normal; color: #94a3b8;">Pesanan</span>
            </div>
        </div>
    </div>

    <!-- ================= TABEL 5 PO TERBARU ================= -->
    <div style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #334155; font-size: 18px;">
                <i class="fas fa-history" style="color: #64748b; margin-right: 5px;"></i> 5 Purchase Order (PO) Terbaru
            </h3>
            <a href="{{ route('purchasing.riwayat_po') }}" style="font-size: 13px; color: #0ea5e9; text-decoration: none; font-weight: bold;">Lihat Semua <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #cbd5e1;">
                        <th style="padding: 15px 12px; color: #475569;">No. PO</th>
                        <th style="padding: 15px 12px; color: #475569;">Tanggal Dibuat</th>
                        <th style="padding: 15px 12px; color: #475569;">Material (MM)</th>
                        <th style="padding: 15px 12px; color: #475569;">Vendor / Supplier</th>
                        <th style="padding: 15px 12px; color: #475569;">Status Kedatangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($poTerbaru ?? [] as $po)
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;">
                        
                        <!-- 1. No. PO -->
                        <td style="padding: 12px; font-weight: bold; color: #0284c7;">PO-{{ date('Ymd', strtotime($po->tanggal_po)) }}-{{ $po->id }}</td>
                        
                        <!-- 2. Tanggal -->
                        <td style="padding: 12px; color: #334155;">{{ date('d M Y', strtotime($po->tanggal_po)) }}</td>
                        
                        <!-- 3. [DIPERBAIKI] Material dan Nama Item -->
                        <td style="padding: 12px;">
                            <div style="font-weight: bold; color: #1e293b;">{{ $po->no_mm ?? '-' }}</div>
                            <div style="font-size: 12px; color: #64748b;">{{ $po->nama_material ?? 'Material Tidak Diketahui' }}</div>
                        </td>

                        <!-- 4. Nama Vendor -->
                        <td style="padding: 12px; font-weight: bold; color: #1e293b;">{{ $po->vendor_name }}</td>
                        
                        <!-- 5. Status -->
                        <td style="padding: 12px;">
                            @if($po->status_barang == 'Sudah Diterima')
                                <span style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold;">Lunas / Selesai</span>
                            @elseif($po->status_barang == 'Parsial')
                                <span style="background: #fef3c7; color: #92400e; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold;">Parsial</span>
                            @else
                                <span style="background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold;">Belum Datang</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <!-- [DIPERBAIKI] Ubah colspan dari 4 menjadi 5 agar tidak bolong jika data kosong -->
                        <td colspan="5" style="text-align: center; padding: 30px; color: #94a3b8; font-style: italic;">
                            Belum ada dokumen PO yang diterbitkan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection