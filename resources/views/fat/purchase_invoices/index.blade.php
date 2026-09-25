@extends('layouts.staff-layout')

@section('konten')
<div style="padding: 30px; background-color: #f8fafc; min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    <!-- Header Halaman -->
    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 22px; font-weight: bold; color: #1e293b; margin: 0 0 5px 0;">
            <i class="fas fa-file-invoice" style="color: #8b5cf6; margin-right: 8px;"></i> Antrean Faktur Pembelian (AP)
        </h2>
        <p style="color: #64748b; font-size: 14px; margin: 0;">Daftar penerimaan barang dari Supplier yang sudah Lulus QC dan siap dibayar.</p>
    </div>

    <!-- Kotak Tabel -->
    <div style="background: white; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden; border: 1px solid #e2e8f0;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase;">No. SJ Supplier</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase;">Tanggal Terima</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase;">Nama Vendor</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase;">No. PO Referensi</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($antreanPenerimaan as $data)
                <tr style="border-bottom: 1px solid #e2e8f0; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f1f5f9'" onmouseout="this.style.backgroundColor='white'">
                    
                    <td style="padding: 15px 20px; font-weight: bold; color: #0f172a;">
                        {{ $data->sj_supplier_number ?? '-' }}
                    </td>
                    
                    <td style="padding: 15px 20px; font-size: 14px; color: #475569; font-weight: 500;">
                        {{ \Carbon\Carbon::parse($data->tanggal_terima)->format('d M Y') }}
                    </td>
                    
                    <td style="padding: 15px 20px; font-size: 14px; color: #6d28d9; font-weight: bold;">
                        {{ strtoupper($data->vendor_name) }}
                    </td>
                    
                    <td style="padding: 15px 20px; font-size: 14px; color: #64748b;">
                        {{ $data->po_kpdt_number }}
                    </td>
                    
                    <td style="padding: 15px 20px; text-align: center;">
                        <!-- Tombol Buat Faktur -->
                        <a href="{{ route('fat.purchase_invoices.create', $data->penerimaan_id) }}" style="display: inline-block; background-color: #8b5cf6; color: white; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold; transition: 0.3s; box-shadow: 0 2px 4px rgba(139, 92, 246, 0.3);" onmouseover="this.style.backgroundColor='#7c3aed';" onmouseout="this.style.backgroundColor='#8b5cf6';">
                            <i class="fas fa-plus-circle" style="margin-right: 5px;"></i> Proses Tagihan
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 40px; text-align: center; color: #94a3b8;">
                        <i class="fas fa-check-circle" style="font-size: 40px; color: #cbd5e1; margin-bottom: 15px; display: block;"></i>
                        <span style="font-size: 16px; font-weight: 500;">Tidak ada tagihan supplier yang perlu diproses saat ini.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection