@extends('layouts.staff-layout')

@section('konten')
<div style="padding: 30px; background-color: #f8fafc; min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    @if(session('error'))
    <div style="background-color: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #fecaca; font-weight: bold;">
        <i class="fas fa-exclamation-triangle" style="margin-right: 5px;"></i> {{ session('error') }}
    </div>
    @endif

    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-size: 22px; font-weight: bold; color: #1e293b; margin: 0 0 5px 0;">
                <i class="fas fa-edit" style="color: #6366f1; margin-right: 8px;"></i> Buat Jurnal Baru
            </h2>
        </div>
        <a href="{{ route('fat.journals.index') }}" style="background-color: #e2e8f0; color: #475569; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="{{ route('fat.journals.store') }}" method="POST" id="jurnalForm">
        @csrf
        
        <!-- Header Informasi -->
        <div style="background: white; border-radius: 10px; padding: 25px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); margin-bottom: 20px; display: flex; gap: 20px;">
            <div style="flex: 1;">
                <label style="font-size: 13px; font-weight: bold; color: #64748b; display: block; margin-bottom: 8px;">Tanggal Transaksi *</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
            </div>
            <div style="flex: 2;">
                <label style="font-size: 13px; font-weight: bold; color: #64748b; display: block; margin-bottom: 8px;">Keterangan Jurnal *</label>
                <input type="text" name="keterangan" value="{{ old('keterangan') }}" placeholder="Contoh: Pencatatan penyusutan mesin bulan Agustus" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
            </div>
        </div>

        <!-- Tabel Dinamis Multi-Baris -->
        <div style="background: white; border-radius: 10px; padding: 25px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;" id="tableJurnal">
                <thead>
                    <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 10px; font-size: 13px; color: #475569; width: 45%; text-align: left;">AKUN (COA)</th>
                        <th style="padding: 10px; font-size: 13px; color: #475569; width: 22%; text-align: right;">DEBIT</th>
                        <th style="padding: 10px; font-size: 13px; color: #475569; width: 22%; text-align: right;">KREDIT</th>
                        <th style="padding: 10px; width: 11%; text-align: center;">AKSI</th>
                    </tr>
                </thead>
                <tbody id="tbodyJurnal">
                    <!-- Baris Pertama -->
                    <tr>
                        <td style="padding: 10px;">
                            <select name="coa_id[]" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                                <option value="">-- Pilih Akun --</option>
                                @foreach($coas as $coa)
                                    <option value="{{ $coa->id }}">{{ $coa->kode_akun }} - {{ $coa->nama_akun }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="padding: 10px;"><input type="number" name="debit[]" class="input-debit" value="0" min="0" step="1" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; text-align: right; color: #10b981; font-weight: bold;"></td>
                        <td style="padding: 10px;"><input type="number" name="kredit[]" class="input-kredit" value="0" min="0" step="1" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; text-align: right; color: #ef4444; font-weight: bold;"></td>
                        <td style="padding: 10px; text-align: center;">
                            <button type="button" class="btn-hapus" style="background: #fee2e2; color: #ef4444; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer;" disabled><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <!-- Baris Kedua -->
                    <tr>
                        <td style="padding: 10px;">
                            <select name="coa_id[]" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                                <option value="">-- Pilih Akun --</option>
                                @foreach($coas as $coa)
                                    <option value="{{ $coa->id }}">{{ $coa->kode_akun }} - {{ $coa->nama_akun }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="padding: 10px;"><input type="number" name="debit[]" class="input-debit" value="0" min="0" step="1" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; text-align: right; color: #10b981; font-weight: bold;"></td>
                        <td style="padding: 10px;"><input type="number" name="kredit[]" class="input-kredit" value="0" min="0" step="1" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; text-align: right; color: #ef4444; font-weight: bold;"></td>
                        <td style="padding: 10px; text-align: center;">
                            <button type="button" class="btn-hapus" style="background: #fee2e2; color: #ef4444; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer;" disabled><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr style="background-color: #f1f5f9;">
                        <td style="padding: 15px 10px; text-align: right; font-weight: bold; font-size: 14px;">TOTAL:</td>
                        <td style="padding: 15px 10px; text-align: right; font-weight: bold; font-size: 16px; color: #10b981;" id="textTotalDebit">Rp 0</td>
                        <td style="padding: 15px 10px; text-align: right; font-weight: bold; font-size: 16px; color: #ef4444;" id="textTotalKredit">Rp 0</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                <button type="button" id="btnTambahBaris" style="background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 10px 15px; border-radius: 6px; font-size: 13px; font-weight: bold; cursor: pointer;">
                    <i class="fas fa-plus"></i> Tambah Baris Akun
                </button>

                <div style="display: flex; align-items: center; gap: 15px;">
                    <span id="statusBalance" style="padding: 8px 15px; border-radius: 20px; font-size: 12px; font-weight: bold; background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;">
                        <i class="fas fa-check-circle"></i> BALANCE
                    </span>
                    <button type="submit" id="btnSimpan" style="background-color: #6366f1; color: white; border: none; padding: 12px 25px; border-radius: 6px; font-size: 15px; font-weight: bold; cursor: pointer;">
                        <i class="fas fa-save"></i> Simpan Jurnal
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- SCRIPT OTOMATISASI FORM (Dynamic Rows & Auto Calculate) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tbody = document.getElementById('tbodyJurnal');
        const btnTambah = document.getElementById('btnTambahBaris');
        
        // Fungsi Update Total
        function calculateTotal() {
            let totalDebit = 0;
            let totalKredit = 0;
            
            document.querySelectorAll('.input-debit').forEach(function(input) {
                totalDebit += parseFloat(input.value) || 0;
            });
            document.querySelectorAll('.input-kredit').forEach(function(input) {
                totalKredit += parseFloat(input.value) || 0;
            });

            // Format ke Rupiah
            document.getElementById('textTotalDebit').innerText = 'Rp ' + totalDebit.toLocaleString('id-ID');
            document.getElementById('textTotalKredit').innerText = 'Rp ' + totalKredit.toLocaleString('id-ID');

            // Cek Balance
            const statusBadge = document.getElementById('statusBalance');
            const btnSimpan = document.getElementById('btnSimpan');

            if (totalDebit === totalKredit && totalDebit > 0) {
                statusBadge.innerHTML = '<i class="fas fa-check-circle"></i> BALANCE';
                statusBadge.style.background = '#dcfce7';
                statusBadge.style.color = '#166534';
                statusBadge.style.borderColor = '#bbf7d0';
                btnSimpan.disabled = false;
                btnSimpan.style.opacity = '1';
            } else {
                statusBadge.innerHTML = '<i class="fas fa-times-circle"></i> TIDAK BALANCE';
                statusBadge.style.background = '#fee2e2';
                statusBadge.style.color = '#991b1b';
                statusBadge.style.borderColor = '#fecaca';
                btnSimpan.disabled = true;
                btnSimpan.style.opacity = '0.5';
            }
        }

        // Jalankan event listener ke semua input angka
        function bindEvents() {
            document.querySelectorAll('.input-debit, .input-kredit').forEach(function(input) {
                input.addEventListener('input', calculateTotal);
            });
            
            // Logika hapus baris
            const rows = tbody.querySelectorAll('tr');
            document.querySelectorAll('.btn-hapus').forEach(function(btn, index) {
                // Minimal 2 baris tidak boleh dihapus
                if (rows.length <= 2) {
                    btn.disabled = true;
                    btn.style.opacity = '0.5';
                } else {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                    btn.onclick = function() {
                        this.closest('tr').remove();
                        bindEvents();
                        calculateTotal();
                    };
                }
            });
        }

        // Tambah Baris Baru
        btnTambah.addEventListener('click', function() {
            // Copy baris terakhir
            const firstRow = tbody.querySelector('tr');
            const newRow = firstRow.cloneNode(true);
            
            // Reset isi input
            newRow.querySelector('select').value = '';
            newRow.querySelector('.input-debit').value = '0';
            newRow.querySelector('.input-kredit').value = '0';
            
            tbody.appendChild(newRow);
            bindEvents();
            calculateTotal();
        });

        // Inisialisasi awal
        bindEvents();
        calculateTotal();
    });
</script>
@endsection