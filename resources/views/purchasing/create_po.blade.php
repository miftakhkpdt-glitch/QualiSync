@extends('layouts.staff-layout')

@section('title', 'Buat Purchase Order (PO)')

@section('konten')
<div style="max-width: 800px; margin: 20px auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    <h3 style="margin-bottom: 20px; color: #334155; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
        <i class="fas fa-shopping-cart" style="color: #f59e0b;"></i> Form Penerbitan Purchase Order (PO)
    </h3>

    <!-- Kotak Informasi PR dari PPIC -->
    <div style="background: #f8fafc; border: 1px solid #cbd5e1; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
        <div style="font-size: 13px; color: #475569; margin-bottom: 5px;">Berdasarkan Dokumen: <b style="color: #0284c7;">{{ $pr->no_pr ?? 'Manual' }}</b></div>
        <div style="font-size: 14px; color: #1e293b;">
            Nama Material: <b>{{ $pr->nama_material ?? '-' }}</b>
        </div>
    </div>

    <!-- Form dikirim ke URL yang membawa ID PR -->
    <form action="{{ isset($pr) ? url('/purchasing/store-po/' . $pr->id) : route('purchasing.po.store') }}" method="POST">
        @csrf
        
        <div style="display: flex; gap: 15px; margin-bottom: 15px;">
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #334155;">Nomor Material (MM)</label>
                <input type="text" name="no_mm" value="{{ $pr->no_mm ?? request('no_mm') }}" readonly 
                       style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; background: #f1f5f9; color: #64748b; font-weight: bold; box-sizing: border-box;">
            </div>
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #334155;">Jumlah (Qty) - Bisa Diedit</label>
                <input type="number" name="qty_pesan" id="qty_pesan" value="{{ $pr->qty ?? request('qty') }}" required min="1" step="0.01"
                       style="width: 100%; padding: 10px; border: 2px solid #fcd34d; border-radius: 4px; font-size: 16px; font-weight: bold; color: #1e293b; box-sizing: border-box;">
            </div>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #334155;">Pilih Supplier / Vendor</label>
            <select name="supplier_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; background: white; box-sizing: border-box;">
                <option value="">-- Pilih Supplier --</option>
                @foreach($vendors ?? [] as $vendor)
                    <option value="{{ $vendor->id }}">{{ $vendor->vendor_name }}</option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; gap: 15px; margin-bottom: 15px;">
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #334155;">Tanggal PO</label>
                <input type="date" name="tanggal_po" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
            </div>
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #334155;">Estimasi Kedatangan (ETA)</label>
                <input type="date" name="eta" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
            </div>
        </div>
        
        <!-- ========================================== -->
        <!-- [BARU] BARIS: HARGA VENDOR, KONVERSI SATUAN & DISKON -->
        <!-- ========================================== -->
        <div style="background: #f8fafc; padding: 15px; border: 1px solid #cbd5e1; border-radius: 6px; margin-bottom: 15px;">
            <div style="display: flex; gap: 15px; align-items: flex-end;">
                <div style="flex: 2;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #334155;">Harga dari Vendor (Rp)</label>
                    <input type="number" name="harga_vendor" id="harga_vendor" value="0" required min="0" step="0.01" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                </div>
                
                <div style="flex: 2;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #334155;">Satuan Harga</label>
                    <select name="satuan_vendor" id="satuan_vendor" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; background: white; box-sizing: border-box;">
                        <option value="1">Satuan Standar (Sama dengan Qty)</option>
                        <option value="kemasan">Per Kemasan (Zak / Box / Drum)</option>
                    </select>
                </div>

                <!-- Kolom ini awalnya disembunyikan, akan muncul jika pilih 'Per Kemasan' -->
                <div style="flex: 1; display: none;" id="box_konversi">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #334155;">Isi / Kemasan</label>
                    <input type="number" name="isi_kemasan" id="isi_kemasan" value="1" min="0.01" step="0.01" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;" placeholder="Misal: 25">
                </div>

                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #334155;">Diskon (%)</label>
                    <input type="number" name="diskon" id="diskon" value="0" min="0" max="100" step="0.01" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                </div>
            </div>
            <small style="color: #64748b; margin-top: 8px; display: block;">* Jika harga vendor adalah per Zak/Drum, pilih "Per Kemasan" dan masukkan isinya (misal: isi 25 Kg).</small>
        </div>

        <div style="display: flex; gap: 15px; margin-bottom: 15px;">
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #334155;">PPN (%)</label>
                <select name="ppn" id="ppn" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; background: white; box-sizing: border-box;">
                    @for($i = 0; $i <= 20; $i++)
                        <option value="{{ $i }}" {{ $i == 11 ? 'selected' : '' }}>{{ $i }}%</option>
                    @endfor
                </select>
            </div>
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #334155;">PPh (%)</label>
                <select name="pph" id="pph" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; background: white; box-sizing: border-box;">
                    <option value="0">0%</option>
                    <option value="2">2%</option>
                    <option value="2.5">2.5%</option>
                    <option value="3">3%</option>
                    <option value="3.5">3.5%</option>
                    <option value="4">4%</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #334155;">Additional Charge / Biaya Tambahan (Rp)</label>
            <input type="number" name="additional_charge" id="additional_charge" value="0" min="0" step="0.01" placeholder="Contoh: Biaya admin, asuransi, atau ongkir..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #334155;">Keterangan / Catatan Tambahan</label>
            <textarea name="keterangan" rows="3" placeholder="Tulis keterangan untuk PO ini (opsional)..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; resize: vertical; font-family: inherit; box-sizing: border-box;"></textarea>
        </div>

        <!-- ========================================== -->
        <!-- [BARU] KOTAK PREVIEW GRAND TOTAL -->
        <!-- ========================================== -->
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
            <h5 style="margin: 0 0 10px 0; color: #166534; font-size: 14px;">Preview Perhitungan:</h5>
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px; color: #475569; font-size: 14px;">
                <span>Subtotal:</span><span id="prev_subtotal">Rp 0</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px; color: #ef4444; font-size: 14px;">
                <span>Diskon:</span><span id="prev_diskon">- Rp 0</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px; color: #475569; font-size: 14px;">
                <span>PPN:</span><span id="prev_ppn">+ Rp 0</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px; color: #ef4444; font-size: 14px;">
                <span>PPh:</span><span id="prev_pph">- Rp 0</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px; color: #475569; font-size: 14px;">
                <span>Biaya Tambahan:</span><span id="prev_tambahan">+ Rp 0</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 10px; padding-top: 10px; border-top: 1px dashed #bbf7d0; font-weight: bold; color: #166534; font-size: 18px;">
                <span>GRAND TOTAL:</span><span id="prev_grandtotal">Rp 0</span>
            </div>
            
            <!-- INPUT HIDDEN: Mengirim Harga Satuan Asli ke Controller Laravel Anda -->
            <input type="hidden" name="harga_satuan" id="harga_satuan_real" value="0">
        </div>

        <button type="submit" 
                style="background: #f59e0b; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; font-size: 14px; transition: background 0.2s;">
            <i class="fas fa-paper-plane"></i> Terbitkan PO
        </button>
    </form>
</div>

<!-- ========================================== -->
<!-- [BARU] SCRIPT PERHITUNGAN REAL-TIME -->
<!-- ========================================== -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil elemen
        const elQty = document.getElementById('qty_pesan');
        const elHargaVendor = document.getElementById('harga_vendor');
        const elSatuanVendor = document.getElementById('satuan_vendor');
        const elIsiKemasan = document.getElementById('isi_kemasan');
        const boxKonversi = document.getElementById('box_konversi');
        
        const elDiskon = document.getElementById('diskon');
        const elPpn = document.getElementById('ppn');
        const elPph = document.getElementById('pph');
        const elTambahan = document.getElementById('additional_charge');

        const outSubtotal = document.getElementById('prev_subtotal');
        const outDiskon = document.getElementById('prev_diskon');
        const outPpn = document.getElementById('prev_ppn');
        const outPph = document.getElementById('prev_pph');
        const outTambahan = document.getElementById('prev_tambahan');
        const outGrandTotal = document.getElementById('prev_grandtotal');
        
        // Elemen Input Hidden untuk Backend Laravel
        const hiddenHargaSatuan = document.getElementById('harga_satuan_real');

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
        }

        // Tampilkan/Sembunyikan kolom "Isi per kemasan"
        elSatuanVendor.addEventListener('change', function() {
            if(this.value === 'kemasan') {
                boxKonversi.style.display = 'block';
            } else {
                boxKonversi.style.display = 'none';
                elIsiKemasan.value = 1; // Kembalikan ke 1 jika standar
            }
            hitungLive();
        });

        function hitungLive() {
            let qty = parseFloat(elQty.value) || 0;
            let hargaVendor = parseFloat(elHargaVendor.value) || 0;
            let isiKemasan = parseFloat(elIsiKemasan.value) || 1;
            
            // LOGIKA KONVERSI HARGA
            let hargaSatuanSebenarnya = hargaVendor / isiKemasan;
            
            // Simpan harga sebenarnya ke input hidden agar bisa di-save ke database
            hiddenHargaSatuan.value = hargaSatuanSebenarnya;

            let diskon = parseFloat(elDiskon.value) || 0;
            let ppn = parseFloat(elPpn.value) || 0;
            let pph = parseFloat(elPph.value) || 0;
            let tambahan = parseFloat(elTambahan.value) || 0;

            // 1. Subtotal = Qty * Harga Satuan Sebenarnya
            let subtotal = qty * hargaSatuanSebenarnya;
            
            // 2. Diskon & DPP
            let nilaiDiskon = subtotal * (diskon / 100);
            let dpp = subtotal - nilaiDiskon;
            
            // 3. Pajak
            let nilaiPpn = dpp * (ppn / 100);
            let nilaiPph = dpp * (pph / 100);
            
            // 4. Grand Total
            let grandTotal = dpp + nilaiPpn - nilaiPph + tambahan;

            // Tampilkan ke layar
            outSubtotal.innerText = formatRupiah(subtotal);
            outDiskon.innerText = '- ' + formatRupiah(nilaiDiskon);
            outPpn.innerText = '+ ' + formatRupiah(nilaiPpn);
            outPph.innerText = '- ' + formatRupiah(nilaiPph);
            outTambahan.innerText = '+ ' + formatRupiah(tambahan);
            outGrandTotal.innerText = formatRupiah(grandTotal);
        }

        // Jalankan saat mengetik
        const daftarInput = [elQty, elHargaVendor, elIsiKemasan, elDiskon, elPpn, elPph, elTambahan];
        daftarInput.forEach(input => {
            if(input) {
                input.addEventListener('input', hitungLive);
                input.addEventListener('change', hitungLive);
            }
        });

        hitungLive();
    });
</script>
@endsection