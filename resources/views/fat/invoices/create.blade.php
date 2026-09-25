@extends('layouts.staff-layout')

@section('konten')
<div style="padding: 30px; background-color: #f8fafc; min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    <!-- Header Halaman -->
    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-size: 22px; font-weight: bold; color: #1e293b; margin: 0 0 5px 0;">
                <i class="fas fa-file-invoice" style="color: #3b82f6; margin-right: 8px;"></i> Buat Faktur Penjualan (Invoice)
            </h2>
            <p style="color: #64748b; font-size: 14px; margin: 0;">Terbitkan tagihan berdasarkan Surat Jalan yang sudah dikirim.</p>
        </div>
        <a href="{{ route('fat.invoices.index') }}" style="background-color: #e2e8f0; color: #475569; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Form Pembungkus -->
    <!-- Catatan: Action form sementara dikosongkan sampai kita membuat route POST-nya -->
    <form action="{{ route('fat.invoices.store') }}" method="POST" id="invoiceForm">
        @csrf
        <!-- ID Surat Jalan disembunyikan untuk disimpan ke database nanti -->
        <input type="hidden" name="warehouse_outgoing_id" value="{{ $data->sj_id }}">

        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            
            <!-- KOTAK KIRI: Informasi Referensi (Read-Only) -->
            <div style="flex: 1; min-width: 300px; background: white; border-radius: 10px; padding: 20px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <h3 style="font-size: 15px; font-weight: bold; color: #0f172a; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 15px;">Informasi Dokumen Referensi</h3>
                
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 12px; font-weight: bold; color: #64748b; display: block; margin-bottom: 5px;">Nama Customer</label>
                    <div style="background-color: #f8fafc; padding: 10px; border-radius: 6px; border: 1px solid #e2e8f0; color: #0f172a; font-weight: bold;">
                        {{ strtoupper($data->nama_customer) }}
                    </div>
                </div>

                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label style="font-size: 12px; font-weight: bold; color: #64748b; display: block; margin-bottom: 5px;">No. Surat Jalan</label>
                        <div style="background-color: #f8fafc; padding: 10px; border-radius: 6px; border: 1px solid #e2e8f0; color: #0f172a;">
                            SJ-{{ str_pad($data->sj_id, 4, '0', STR_PAD_LEFT) }}
                        </div>
                    </div>
                    <div style="flex: 1;">
                        <label style="font-size: 12px; font-weight: bold; color: #64748b; display: block; margin-bottom: 5px;">No. PO Referensi</label>
                        <div style="background-color: #f8fafc; padding: 10px; border-radius: 6px; border: 1px solid #e2e8f0; color: #0f172a;">
                            {{ $data->no_po }}
                        </div>
                    </div>
                </div>

                <div style="background-color: #f0fdf4; border: 1px dashed #86efac; padding: 15px; border-radius: 6px; margin-top: 20px;">
                    <p style="font-size: 12px; color: #166534; font-weight: bold; margin: 0 0 5px 0;">Rincian Barang Terkirim:</p>
                    <p style="font-size: 14px; color: #14532d; margin: 0 0 5px 0;"><strong>{{ $data->nama_produk }}</strong></p>
                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #166534;">
                        <span>Qty Kirim: <strong>{{ number_format($data->qty_kirim, 0, ',', '.') }}</strong></span>
                        <span>Harga Satuan: <strong>Rp {{ number_format($data->price, 0, ',', '.') }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- KOTAK KANAN: Input Invoice FAT -->
            <div style="flex: 1; min-width: 300px; background: white; border-radius: 10px; padding: 20px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <h3 style="font-size: 15px; font-weight: bold; color: #0f172a; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 15px;">Pengaturan Tagihan</h3>

                <div style="margin-bottom: 15px;">
                    <label style="font-size: 12px; font-weight: bold; color: #64748b; display: block; margin-bottom: 5px;">Nomor Invoice *</label>
                    <input type="text" name="no_invoice" value="INV-{{ date('Ymd') }}-{{ $data->sj_id }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; font-weight: bold; color: #0f172a; outline: none;">
                </div>

                <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                    <div style="flex: 1;">
                        <label style="font-size: 12px; font-weight: bold; color: #64748b; display: block; margin-bottom: 5px;">Tanggal Terbit *</label>
                        <input type="date" name="tanggal_invoice" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; outline: none;">
                    </div>
                    <div style="flex: 1;">
                        <label style="font-size: 12px; font-weight: bold; color: #64748b; display: block; margin-bottom: 5px;">Jatuh Tempo *</label>
                        <!-- Default 30 hari dari sekarang -->
                        <input type="date" name="jatuh_tempo" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; outline: none;">
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">

                <!-- Perhitungan Nominal -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-size: 14px; color: #64748b; font-weight: bold;">Subtotal</span>
                    <input type="number" id="inputSubtotal" name="subtotal" value="{{ $subtotal }}" readonly style="text-align: right; border: none; background: transparent; font-size: 16px; font-weight: bold; color: #0f172a; outline: none;">
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <span style="font-size: 14px; color: #64748b; font-weight: bold;">PPN (11%)</span>
                    <input type="number" id="inputPpn" name="ppn" style="text-align: right; width: 150px; padding: 5px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px; outline: none;" oninput="hitungTotal()">
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; background-color: #f1f5f9; padding: 15px; border-radius: 6px;">
                    <span style="font-size: 16px; color: #0f172a; font-weight: bold;">TOTAL TAGIHAN</span>
                    <input type="number" id="inputTotal" name="total_tagihan" readonly style="text-align: right; border: none; background: transparent; font-size: 20px; font-weight: 900; color: #2563eb; outline: none;">
                </div>

                <button type="submit" style="width: 100%; background-color: #3b82f6; color: white; border: none; padding: 12px; border-radius: 6px; font-size: 15px; font-weight: bold; margin-top: 20px; cursor: pointer; transition: 0.3s;" onmouseover="this.style.backgroundColor='#2563eb'" onmouseout="this.style.backgroundColor='#3b82f6'">
                    <i class="fas fa-save" style="margin-right: 5px;"></i> Simpan & Terbitkan Invoice
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Script untuk Menghitung PPN Otomatis -->
<script>
    function hitungTotal() {
        // Ambil nilai dari input, jika kosong anggap 0
        let subtotal = parseFloat(document.getElementById('inputSubtotal').value) || 0;
        let ppn = parseFloat(document.getElementById('inputPpn').value) || 0;
        
        // Hitung Grand Total
        let grandTotal = subtotal + ppn;
        
        // Tampilkan ke input total
        document.getElementById('inputTotal').value = grandTotal;
    }

    // Jalankan otomatis saat halaman pertama kali dibuka
    window.onload = function() {
        let subtotal = parseFloat(document.getElementById('inputSubtotal').value) || 0;
        // Set default PPN 11%
        let defaultPpn = subtotal * 0.11;
        document.getElementById('inputPpn').value = defaultPpn;
        
        hitungTotal(); // Panggil fungsi untuk menjumlahkan
    };
</script>
@endsection