@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 1400px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h3 style="margin-bottom: 5px; color: #334155; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
        <i class="fas fa-file-alt"></i> PPIC - Daftar PO Masuk (Demand dari Sales)
    </h3>
    <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">Daftar pesanan customer yang masuk dari Sales untuk dianalisis kebutuhan materialnya (MRP).</p>

    @if(session('success'))
        <div style="background: #dcfce7; color: #16a34a; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- TABEL DAFTAR PO MASUK DARI SALES -->
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #0ea5e9; color: white;">
                    <th style="padding: 10px;">No. PO</th>
                    <!-- KOLOM BARU: Tanggal Terima PO -->
                    <th style="padding: 10px;">Tanggal Terima (Input)</th>
                    <th style="padding: 10px;">Nama Customer</th>
                    <th style="padding: 10px;">Finished Goods (FG)</th>
                    <th style="padding: 10px;">Qty Order</th>
                    <th style="padding: 10px;">Tanggal Kirim (Delivery)</th>
                    <th style="padding: 10px; text-align: center;">Aksi MRP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pos as $po)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 10px; font-weight: bold; color: #1e293b;">{{ $po->no_po }}</td>
                        
                        <!-- DATA BARU: Menampilkan tanggal PO dibuat/diinput -->
                        <td style="padding: 10px; color: #475569; font-weight: 500;">
                            {{ date('d M Y', strtotime($po->tanggal_po ?? $po->created_at)) }}
                        </td>
                        
                        <td style="padding: 10px;">{{ $po->nama_customer ?? '-' }}</td>
                        <td style="padding: 10px;">
                            <b>{{ $po->no_mm }}</b><br>
                            <span style="color: #64748b; font-size: 12px;">{{ $po->nama_material ?? '-' }}</span>
                        </td>
                        <td style="padding: 10px; font-weight: bold; color: #0284c7;">{{ number_format($po->qty) }} Pcs</td>
                        <td style="padding: 10px; font-weight: bold; color: #b91c1c;">
                            {{ date('d M Y', strtotime($po->delivery_date)) }}
                        </td>
                        <td style="padding: 10px; text-align: center;">
                            <a href="{{ route('ppic.mrp.calculate', $po->id) }}" style="background: #0284c7; color: white; padding: 7px 14px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 12px; display: inline-flex; align-items: center; gap: 5px;">
                                <i class="fas fa-calculator"></i> Jalankan MRP
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <!-- REVISI: colspan diubah dari 6 menjadi 7 -->
                        <td colspan="7" style="text-align: center; padding: 25px; color: #64748b;">Belum ada PO Masuk dari Sales.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection