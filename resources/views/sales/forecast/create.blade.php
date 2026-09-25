@extends('layouts.staff-layout')

@section('title', 'Input Forecast Customer - PT KIMPAI DYNA TUBE')

@section('konten')
<div style="padding: 20px; max-width: 900px; margin: 0 auto;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 style="font-size: 24px; color: #334155; margin: 0;">
                <i class="fas fa-chart-line" style="color: #0ea5e9;"></i> Input Forecast Customer
            </h2>
            <p style="color: #64748b; font-size: 14px; margin-top: 5px;">Masukkan perkiraan pesanan untuk beberapa bulan sekaligus.</p>
        </div>
        <a href="{{ route('sales.forecast.index') }}" style="background: #cbd5e1; color: #334155; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 13px;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- FORM INPUT -->
    <div style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-top: 4px solid #0ea5e9;">
        <form action="{{ route('sales.forecast.store') }}" method="POST">
            @csrf
            
            <!-- Baris 1: Info Customer -->
            <h3 style="font-size: 16px; color: #1e293b; margin-top: 0; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">Informasi Pelanggan & Produk</h3>
            
            <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 13px; font-weight: bold; color: #475569; margin-bottom: 5px;">Nama Customer <span style="color: red;">*</span></label>
                    <select name="nama_customer" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box; font-size: 14px; background: #fff;">
    <option value="">-- Pilih Customer dari Master Data --</option>
    @foreach($customers as $cust)
        <option value="{{ $cust->nama_customer }}">{{ $cust->nama_customer }}</option>
    @endforeach
</select>
                </div>
            </div>

            <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 13px; font-weight: bold; color: #475569; margin-bottom: 5px;">No. MM Barang Jadi <span style="color: red;">*</span></label>
                    <input type="text" name="no_mm" id="input_mm" required placeholder="Ketik MM lalu klik di luar kotak..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box; font-size: 14px; background: #fffbeb;">
                    <small id="mm_status" style="color: #64748b; font-size: 11px;">Otomatis mencari nama produk...</small>
                </div>
                <div style="flex: 2;">
                    <label style="display: block; font-size: 13px; font-weight: bold; color: #475569; margin-bottom: 5px;">Nama Produk <span style="color: red;">*</span></label>
                    <input type="text" name="nama_produk" id="input_nama_produk" required placeholder="Akan terisi otomatis..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box; font-size: 14px; background: #f1f5f9;" readonly>
                </div>
            </div>

            <!-- Baris 2: Tabel Multi Bulan -->
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; margin-bottom: 15px; padding-bottom: 10px;">
                <h3 style="font-size: 16px; color: #1e293b; margin: 0;">Target Produksi / Forecast (Bisa Lebih Dari 1 Bulan)</h3>
                <button type="button" id="btn_tambah_baris" style="background: #3b82f6; color: white; border: none; padding: 6px 12px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 12px;">
                    <i class="fas fa-plus"></i> Tambah Bulan
                </button>
            </div>
            
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;" id="tabel_forecast">
                <thead>
                    <tr style="background: #f8fafc;">
                        <th style="padding: 10px; text-align: left; font-size: 13px; color: #475569;">Bulan <span style="color: red;">*</span></th>
                        <th style="padding: 10px; text-align: left; font-size: 13px; color: #475569;">Tahun <span style="color: red;">*</span></th>
                        <th style="padding: 10px; text-align: left; font-size: 13px; color: #475569;">Qty (Jumlah) <span style="color: red;">*</span></th>
                        <th style="padding: 10px; text-align: center; font-size: 13px; color: #475569;">Hapus</th>
                    </tr>
                </thead>
                <tbody id="body_forecast">
                    <!-- Baris Default (Bulan 1) -->
                    <tr>
                        <td style="padding: 8px;">
                            <select name="bulan[]" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px;">
                                <option value="">-- Pilih Bulan --</option>
                                <option value="1">1 - Januari</option>
                                <option value="2">2 - Februari</option>
                                <option value="3">3 - Maret</option>
                                <option value="4">4 - April</option>
                                <option value="5">5 - Mei</option>
                                <option value="6">6 - Juni</option>
                                <option value="7">7 - Juli</option>
                                <option value="8">8 - Agustus</option>
                                <option value="9">9 - September</option>
                                <option value="10">10 - Oktober</option>
                                <option value="11">11 - November</option>
                                <option value="12">12 - Desember</option>
                            </select>
                        </td>
                        <td style="padding: 8px;">
                            <input type="number" name="tahun[]" value="{{ date('Y') }}" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px;">
                        </td>
                        <td style="padding: 8px;">
                            <input type="number" name="qty_forecast[]" required placeholder="Contoh: 150000" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px;">
                        </td>
                        <td style="padding: 8px; text-align: center;">
                            <button type="button" class="btn_hapus" style="background: #ef4444; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer;"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Tombol Simpan -->
            <div style="text-align: right; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                <button type="submit" style="background: #10b981; color: white; border: none; padding: 12px 25px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 14px;">
                    <i class="fas fa-save"></i> Simpan Semua Forecast
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPT UNTUK AUTO-FILL & TAMBAH BARIS -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Fitur Auto-Fill Nama Produk berdasarkan MM
        const inputMm = document.getElementById('input_mm');
        const inputNama = document.getElementById('input_nama_produk');
        const mmStatus = document.getElementById('mm_status');

        inputMm.addEventListener('blur', function() {
            let mm = this.value.trim();
            if(mm !== '') {
                mmStatus.innerText = "Mencari ke database...";
                mmStatus.style.color = "#f59e0b";
                
                // Gunakan base url Laravel agar rute selalu akurat
                fetch(`{{ url('/sales/get-produk') }}/${mm}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Sistem Laravel mengembalikan error 500");
                    }
                    return response.json();
                })
                .then(data => {
                    if(data.success) {
                        inputNama.value = data.nama_produk;
                        mmStatus.innerText = "✓ Produk ditemukan";
                        mmStatus.style.color = "#10b981";
                    } else {
                        inputNama.value = "";
                        // Menampilkan pesan error dari Controller
                        mmStatus.innerText = "❌ " + (data.message || "Gagal mengambil data");
                        mmStatus.style.color = "#ef4444";
                    }
                })
                .catch(error => {
                    inputNama.value = "";
                    mmStatus.innerText = "❌ Error Jaringan/Server. Cek terminal VS Code Anda.";
                    mmStatus.style.color = "#ef4444";
                    console.error(error);
                });
            } else {
                inputNama.value = "";
                mmStatus.innerText = "Otomatis mencari nama produk...";
                mmStatus.style.color = "#64748b";
            }
        });

        // 2. Fitur Tambah Baris Bulan (Multi Bulan)
        const btnTambah = document.getElementById('btn_tambah_baris');
        const tbody = document.getElementById('body_forecast');

        btnTambah.addEventListener('click', function() {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="padding: 8px;">
                    <select name="bulan[]" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px;">
                        <option value="">-- Pilih Bulan --</option>
                        <option value="1">1 - Januari</option>
                        <option value="2">2 - Februari</option>
                        <option value="3">3 - Maret</option>
                        <option value="4">4 - April</option>
                        <option value="5">5 - Mei</option>
                        <option value="6">6 - Juni</option>
                        <option value="7">7 - Juli</option>
                        <option value="8">8 - Agustus</option>
                        <option value="9">9 - September</option>
                        <option value="10">10 - Oktober</option>
                        <option value="11">11 - November</option>
                        <option value="12">12 - Desember</option>
                    </select>
                </td>
                <td style="padding: 8px;">
                    <input type="number" name="tahun[]" value="{{ date('Y') }}" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px;">
                </td>
                <td style="padding: 8px;">
                    <input type="number" name="qty_forecast[]" required placeholder="Contoh: 150000" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px;">
                </td>
                <td style="padding: 8px; text-align: center;">
                    <button type="button" class="btn_hapus" style="background: #ef4444; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer;"><i class="fas fa-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // 3. Fitur Hapus Baris
        tbody.addEventListener('click', function(e) {
            if(e.target.closest('.btn_hapus')) {
                if(tbody.children.length > 1) {
                    e.target.closest('tr').remove();
                } else {
                    alert('Minimal harus ada 1 bulan yang diinput.');
                }
            }
        });
    });
</script>
@endsection