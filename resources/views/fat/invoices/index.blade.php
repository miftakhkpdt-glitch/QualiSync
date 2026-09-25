@extends('layouts.staff-layout')

@section('konten')
<!-- Pembungkus Utama Halaman -->
<div style="padding: 30px; background-color: #f8fafc; min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    <!-- Header Halaman -->
    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 22px; font-weight: bold; color: #1e293b; margin: 0 0 5px 0;">
            <i class="fas fa-file-invoice-dollar" style="color: #10b981; margin-right: 8px;"></i> Antrean Faktur Penjualan
        </h2>
        <p style="color: #64748b; font-size: 14px; margin: 0;">Daftar Surat Jalan yang sudah dikirim oleh Gudang dan siap ditagih ke Customer.</p>
    </div>

    <!-- Kotak Tabel (Card) -->
    <div style="background: white; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); overflow: hidden; border: 1px solid #e2e8f0;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            
            <!-- Kepala Tabel -->
            <thead>
                <tr style="background-color: #f1f5f9; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">No. Surat Jalan</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Tanggal Kirim</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Customer</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">No. PO Ref</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            
            <!-- Isi Tabel -->
            <tbody>
                @forelse($antreanSuratJalan as $data)
                <!-- Efek hover pada baris tabel -->
                <tr style="border-bottom: 1px solid #e2e8f0; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='white'">
                    
                    <td style="padding: 15px 20px;">
                        <!-- Label Badge untuk Nomor SJ -->
                        <span style="background-color: #e0f2fe; color: #0284c7; padding: 5px 10px; border-radius: 6px; font-size: 13px; font-weight: bold; border: 1px solid #bae6fd;">
                            SJ-{{ str_pad($data->sj_id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                    </td>
                    
                    <td style="padding: 15px 20px; font-size: 14px; color: #475569; font-weight: 500;">
                        {{ \Carbon\Carbon::parse($data->tanggal_pengiriman)->format('d M Y') }}
                    </td>
                    
                    <td style="padding: 15px 20px; font-size: 14px; color: #0f172a; font-weight: bold;">
                        {{ strtoupper($data->nama_customer) }}
                    </td>
                    
                    <td style="padding: 15px 20px; font-size: 14px; color: #64748b;">
                        {{ $data->no_po }}
                    </td>
                    
                    <td style="padding: 15px 20px; text-align: center;">
                        <!-- Tombol Buat Invoice Keren -->
                        <a href="{{ route('fat.invoices.create', $data->sj_id) }}" style="display: inline-block; background-color: #10b981; color: white; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);" onmouseover="this.style.backgroundColor='#059669'; this.style.transform='translateY(-1px)';" onmouseout="this.style.backgroundColor='#10b981'; this.style.transform='translateY(0)';">
                            <i class="fas fa-plus-circle" style="margin-right: 5px;"></i> Buat Invoice
                        </a>
                    </td>
                </tr>
                @empty
                <!-- Tampilan jika tidak ada antrean -->
                <tr>
                    <td colspan="5" style="padding: 40px; text-align: center; color: #94a3b8;">
                        <i class="fas fa-check-circle" style="font-size: 40px; color: #cbd5e1; margin-bottom: 15px; display: block;"></i>
                        <span style="font-size: 16px; font-weight: 500;">Hore! Pekerjaan selesai.</span><br>
                        <span style="font-size: 14px;">Tidak ada Surat Jalan yang perlu ditagih saat ini.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection