@extends('layouts.staff-layout')

@section('konten')
<div style="padding: 30px; background-color: #f8fafc; min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    <!-- Header Halaman -->
    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-size: 22px; font-weight: bold; color: #1e293b; margin: 0 0 5px 0;">
                <i class="fas fa-file-invoice" style="color: #8b5cf6; margin-right: 8px;"></i> Catat Faktur Pembelian (Tagihan Supplier)
            </h2>
            <p style="color: #64748b; font-size: 14px; margin: 0;">Verifikasi tagihan masuk berdasarkan barang yang telah diterima di Gudang.</p>
        </div>
        <a href="{{ route('fat.purchase_invoices.index') }}" style="background-color: #e2e8f0; color: #475569; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Form Pembungkus -->
    <!-- Action disiapkan untuk rute store yang akan kita buat nanti -->
    <form action="{{ route('fat.purchase_invoices.store') }}" method="POST" id="purchaseInvoiceForm">
        @csrf
        <input type="hidden" name="incoming_material_id" value="{{ $data->penerimaan_id }}">

        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            
            <!-- KOTAK KIRI: Rincian Dokumen & Barang (Read-Only) -->
            <div style="flex: 1; min-width: 300px; background: white; border-radius: 10px; padding: 20px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <h3 style="font-size: 15px; font-weight: bold; color: #0f172a; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 15px;">Informasi Referensi</h3>
                
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 12px; font-weight: bold; color: #64748b; display: block; margin-bottom: 5px;">Nama Vendor / Supplier</label>
                    <div style="background-color: #f8fafc; padding: 10px; border-radius: 6px; border: 1px solid #e2e8f0; color: #6d28d9; font-weight: bold;">
                        {{ strtoupper($data->vendor_name) }}
                    </div>
                </div>

                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label style="font-size: 12px; font-weight: bold; color: #64748b; display: block; margin-bottom: 5px;">No. Surat Jalan Supplier</label>
                        <div style="background-color: #f8fafc; padding: 10px; border-radius: 6px; border: 1px solid #e2e8f0; color: #0f172a;">
                            {{ $data->sj_supplier_number ?? 'Tidak Ada' }}
                        </div>
                    </div>
                    <div style="flex: 1;">
                        <label style="font-size: 12px; font-weight: bold; color: #64748b; display: block; margin-bottom: 5px;">No. PO KPDT</label>
                        <div style="background-color: #f8fafc; padding: 10px; border-radius: 6px; border: 1px solid #e2e8f0; color: #0f172a;">
                            {{ $data->po_kpdt_number }}
                        </div>
                    </div>
                </div>

                <div style="background-color: #f5f3ff; border: 1px dashed #c4b5fd; padding: 15px; border-radius: 6px; margin-top: 20px;">
                    <p style="font-size: 12px; color: #5b21b6; font-weight: bold; margin: 0 0 5px 0;">Rincian Barang Diterima (Lulus QC):</p>
                    <p style="font-size: 14px; color: #4c1d95; margin: 0 0 5px 0;"><strong>{{ $data->item_name }}</strong></p>
                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #5b21b6;">
                        <span>Qty Terima: <strong>{{ number_format($data->quantity, 0, ',', '.') }}</strong></span>
                        <span>Harga PO: <strong>Rp {{ number_format($data->harga_satuan ?? 0, 0, ',', '.') }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- KOTAK KANAN: Input Tagihan FAT -->
            <div style="flex: 1; min-width: 300px; background: white; border-radius: 10px; padding: 20px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <h3 style="font-size: 15px; font-weight: bold; color: #0f172a; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 15px;">Pengaturan Pembayaran</h3>

                <div style="margin-bottom: 15px;">
                    <label style="font-size: 12px; font-weight: bold; color: #64748b; display: block; margin-bottom: 5px;">Nomor Invoice dari Supplier *</label>
                    <input type="text" name="no_invoice_supplier" placeholder="Contoh: INV/VND/2026/08" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; font-weight: bold; color: #0f172a; outline: none; border-left: 4px solid #8b5cf6;">
                </div>

                <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                    <div style="flex: 1;">
                        <label style="font-size: 12px; font-weight: bold; color: #64748b; display: block; margin-bottom: 5px;">Tanggal Invoice *</label>
                        <input type="date" name="tanggal_invoice" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; outline: none;">
                    </div>
                    <div style="flex: 1;">
                        <label style="font-size: 12px; font-weight: bold; color: #64748b; display: block; margin-bottom: 5px;">Jatuh Tempo *</label>
                        <!-- Default 30 hari -->
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
                    <span style="font-size: 14px; color: #64748b; font-weight: bold;">PPN Masukan (11%)</span>
                    <input type="number" id="inputPpn" name="ppn" style="text-align: right; width: 150px; padding: 5px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px; outline: none;" oninput="hitungTotalPurchase()">
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; background-color: #f8fafc; padding: 15px; border-radius: 6px;">
                    <span style="font-size: 16px; color: #0f172a; font-weight: bold;">TOTAL HUTANG</span>
                    <input type="number" id="inputTotal" name="total_tagihan" readonly style="text-align: right; border: none; background: transparent; font-size: 20px; font-weight: 900; color: #8b5cf6; outline: none;">
                </div>

                <button type="submit" style="width: 100%; background-color: #8b5cf6; color: white; border: none; padding: 12px; border-radius: 6px; font-size: 15px; font-weight: bold; margin-top: 20px; cursor: pointer; transition: 0.3s;" onmouseover="this.style.backgroundColor='#7c3aed'" onmouseout="this.style.backgroundColor='#8b5cf6'">
                    <i class="fas fa-save" style="margin-right: 5px;"></i> Simpan & Akui Hutang
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Script Hitung Otomatis -->
<script>
    function hitungTotalPurchase() {
        let subtotal = parseFloat(document.getElementById('inputSubtotal').value) || 0;
        let ppn = parseFloat(document.getElementById('inputPpn').value) || 0;
        let grandTotal = subtotal + ppn;
        document.getElementById('inputTotal').value = grandTotal;
    }

    window.onload = function() {
        let subtotal = parseFloat(document.getElementById('inputSubtotal').value) || 0;
        let defaultPpn = subtotal * 0.11;
        document.getElementById('inputPpn').value = defaultPpn;
        hitungTotalPurchase();
    };
</script>
@endsection