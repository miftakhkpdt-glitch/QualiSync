@extends('layouts.staff-layout')

@section('title', 'Tambah Master Customer - PT KIMPAI DYNA TUBE')

@section('konten')
<div style="padding: 20px; max-width: 800px; margin: 0 auto;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 style="font-size: 24px; color: #334155; margin: 0;">
                <i class="fas fa-building" style="color: #0ea5e9;"></i> Tambah Master Customer
            </h2>
            <p style="color: #64748b; font-size: 14px; margin-top: 5px;">Daftarkan pelanggan dan atur rumus MRP yang akan digunakan.</p>
        </div>
        <!-- PERBAIKAN: Diubah ke route index agar kembali ke daftar customer -->
        <a href="{{ route('sales.master-customer.index') }}" style="background: #cbd5e1; color: #334155; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 13px;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-top: 4px solid #0ea5e9;">
        <form action="{{ route('sales.master-customer.store') }}" method="POST">
            @csrf
            
            <h3 style="font-size: 16px; color: #1e293b; margin-top: 0; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">Informasi Dasar</h3>
            
            <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 13px; font-weight: bold; color: #475569; margin-bottom: 5px;">Kode Customer (Opsional)</label>
                    <input type="text" name="kode_customer" placeholder="Contoh: CUST-001" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px;">
                </div>
                <div style="flex: 2;">
                    <label style="display: block; font-size: 13px; font-weight: bold; color: #475569; margin-bottom: 5px;">Nama Customer <span style="color: red;">*</span></label>
                    <input type="text" name="nama_customer" required placeholder="Contoh: PT Unilever Indonesia" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px;">
                </div>
            </div>

            <h3 style="font-size: 16px; color: #1e293b; margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">Pengaturan Sistem MRP</h3>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: bold; color: #475569; margin-bottom: 5px;">Pilih Tipe Kalkulasi MRP <span style="color: red;">*</span></label>
                <select name="tipe_kalkulasi_mrp" id="tipe_mrp" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px; background-color: #f8fafc;">
                    <option value="Min-Max">Sistem Min-Max (Menggunakan ROP & Max Stock)</option>
                    <option value="Coverage">Sistem Coverage (Berdasarkan Target Bulan Depan)</option>
                </select>
            </div>

            <!-- Blok Parameter Coverage (Awalnya Disembunyikan) -->
            <div id="blok_coverage" style="display: none; background: #f0fdf4; padding: 15px; border: 1px dashed #22c55e; border-radius: 4px; margin-bottom: 20px;">
                <p style="margin-top: 0; font-size: 12px; color: #166534; font-weight: bold;"><i class="fas fa-info-circle"></i> Parameter Khusus Tipe Coverage</p>
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 13px; font-weight: bold; color: #475569; margin-bottom: 5px;">Target Coverage (Bulan)</label>
                        <input type="number" name="batas_coverage_bulan" id="input_bulan" placeholder="Contoh: 2" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px;">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 13px; font-weight: bold; color: #475569; margin-bottom: 5px;">Yield Printed Web (%)</label>
                        <input type="number" step="0.01" name="yield_pweb" id="input_yield" value="85" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px;">
                        <small style="color: #64748b; font-size: 11px;">Isi 85 untuk 85%.</small>
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div style="text-align: right; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                <button type="submit" style="background: #10b981; color: white; border: none; padding: 12px 25px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 14px;">
                    <i class="fas fa-save"></i> Simpan Data Customer
                </button>
            </div>
            
        </form>
    </div>
</div>

<!-- Script untuk Tampil/Sembunyikan Parameter Coverage -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectTipe = document.getElementById('tipe_mrp');
        const blokCoverage = document.getElementById('blok_coverage');
        const inputBulan = document.getElementById('input_bulan');

        function cekTipe() {
            if (selectTipe.value === 'Coverage') {
                blokCoverage.style.display = 'block';
                inputBulan.setAttribute('required', 'required'); 
            } else {
                blokCoverage.style.display = 'none';
                inputBulan.removeAttribute('required'); 
            }
        }

        cekTipe();
        selectTipe.addEventListener('change', cekTipe);
    });
</script>
@endsection