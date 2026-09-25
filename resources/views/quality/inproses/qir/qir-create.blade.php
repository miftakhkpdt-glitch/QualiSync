@extends('layouts.staff-layout')

@section('title', 'Buat Dokumen QIR Dinamis')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .modern-container { max-width: 1200px; margin: 30px auto; font-family: 'Inter', sans-serif; }
    .modern-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; padding: 25px; }
    .header-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; }
    .form-group { margin-bottom: 15px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; }
    .modern-input { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; }
    
    .table-wrapper { overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 15px; }
    .modern-table { width: 100%; border-collapse: collapse; min-width: 800px; text-align: center; font-size: 13px; }
    .modern-table th { background-color: #f1f5f9; color: #334155; padding: 10px; border: 1px solid #e2e8f0; }
    .modern-table td { padding: 8px; border: 1px solid #e2e8f0; }
    .std-row { background-color: #fffbeb; font-size: 12px; font-weight: bold; color: #d97706; }
    .input-sm { width: 100%; padding: 6px; border: 1px solid #cbd5e1; border-radius: 4px; text-align: center; }
</style>
@endpush

@section('konten')
<div class="modern-container">
    <form action="{{ url('/qir/store') }}" method="POST" id="form-qir">
        @csrf
        <div class="modern-card">
            
            <!-- Tombol Kembali Ditambahkan di Sini -->
            <div style="margin-bottom: 20px;">
                <a href="{{ url('/qir') }}" style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none; background-color: #64748b; color: white; padding: 8px 14px; border-radius: 6px; font-size: 13px; font-weight: bold; transition: background 0.2s;">
                    <i class="fas fa-arrow-left"></i> Kembali ke Menu
                </a>
            </div>

            <h4 style="margin-top:0; color:#1e293b; border-bottom:2px solid #f1f5f9; padding-bottom:15px; margin-bottom:20px;">
                <i class="fas fa-file-signature" style="color:#3b82f6;"></i> Input QIR Baru (Sistem Dinamis)
            </h4>

            <!-- HEADER FORM -->
            <div class="header-grid">
                <div>
                    <div class="form-group">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="modern-input" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. Batch</label>
                        <input type="text" name="no_batch" class="modern-input" placeholder="Masukkan No Batch..." required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. MM / Item Produk</label>
                        <select name="no_mm" id="no_mm" class="modern-input select2" required>
                            <option value="">-- Pilih Item Produk --</option>
                            @foreach($masterItems as $item)
                                <option value="{{ $item->no_mm }}">{{ $item->no_mm }} - {{ $item->nama_material }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label">Shift Kerja</label>
                        <select name="shift" class="modern-input" required>
                            <option value="Shift 1">Shift 1</option>
                            <option value="Shift 2">Shift 2</option>
                            <option value="Shift 3">Shift 3</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Line Produksi</label>
                        <select name="line_produksi" class="modern-input" required>
                            <option value="Line 1">Line 1</option>
                            <option value="Line 2">Line 2</option>
                            <option value="Line 3">Line 3</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- TABEL DINAMIS (KOSONG DI AWAL, DIGAMBAR OLEH JAVASCRIPT NANTINYA) -->
            <div class="table-wrapper">
                <table class="modern-table">
                    <thead id="table-head">
                        <tr>
                            <th style="padding: 30px; color: #94a3b8; font-style: italic;">
                                Pilih Item Produk (No MM) di atas untuk memunculkan parameter pengecekan...
                            </th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        <!-- Baris Sampel Akan Muncul Di Sini -->
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 10px;">
                <button type="button" id="btn-tambah-sampel" style="background-color: #10b981; color: white; padding: 8px 15px; border: none; border-radius: 6px; cursor: pointer; display: none;">
                    <i class="fas fa-plus"></i> Tambah Sampel
                </button>
            </div>

            <div style="text-align: right; margin-top: 30px;">
                <button type="submit" style="background-color: #3b82f6; color: white; padding: 12px 30px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
                    <i class="fas fa-save"></i> Simpan Dokumen QIR
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        if ($.fn.select2) { $('#no_mm').select2(); }

        let currentParameters = []; 
        let sampleCount = 0;

        // KETIKA QC MEMILIH PRODUK DARI DROPDOWN
        $('#no_mm').on('change', function() {
            let no_mm = $(this).val();
            
            if(!no_mm) {
                $('#table-head').html('<tr><th style="padding: 30px;">Pilih Item Produk...</th></tr>');
                $('#table-body').empty();
                $('#btn-tambah-sampel').hide();
                return;
            }

            // Meminta daftar parameter dari database
            $('#table-head').html('<tr><th>Loading parameter...</th></tr>');
            
            $.ajax({
                url: "{{ url('/qir/get-parameters') }}/" + no_mm,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    currentParameters = data;
                    renderDynamicTable();
                }
            });
        });

        // FUNGSI MENGGAMBAR TABEL (KOLOM)
        function renderDynamicTable() {
            if(currentParameters.length === 0) {
                $('#table-head').html('<tr><th style="color:red;">Produk ini belum memiliki standar parameter! Silakan atur di Master Standar.</th></tr>');
                $('#table-body').empty();
                $('#btn-tambah-sampel').hide();
                return;
            }

            $('#btn-tambah-sampel').show();

            // 1. Gambar Baris Judul & Baris Standar (Kuning)
            let trJudul = '<tr><th style="width: 70px;">Sample</th>';
            let trStandar = '<tr class="std-row"><td>Standard</td>';

            currentParameters.forEach(param => {
                let namaKolom = param.nama_parameter + (param.satuan ? ` <br><small>(${param.satuan})</small>` : '');
                trJudul += `<th>${namaKolom}</th>`;

                if(param.tipe_input === 'Angka') {
                    let min = param.min_value !== null ? param.min_value : '-';
                    let max = param.max_value !== null ? param.max_value : '-';
                    trStandar += `<td>Min: ${min} | Max: ${max}</td>`;
                } else {
                    trStandar += `<td>${param.standar_teks ?? '-'}</td>`;
                }
            });

            trJudul += '<th style="width: 60px;">Aksi</th></tr>';
            trStandar += '<td>-</td></tr>';

            $('#table-head').html(trJudul + trStandar);

            // 2. Kosongkan baris bawah dan tambahkan 1 baris sampel
            $('#table-body').empty();
            sampleCount = 0;
            addSampleRow();
        }

        // FUNGSI MENGGAMBAR BARIS SAMPEL (INPUT)
        function addSampleRow() {
            sampleCount++;
            let tr = `<tr>
                <td class="sample-number">
                    <strong>${sampleCount}</strong>
                    <input type="hidden" name="sample_no[]" value="${sampleCount}">
                </td>`;

            // Trik jenius: Array dalam Array -> name="hasil_aktual[1][4]" (Sampel 1, Parameter ID 4)
            currentParameters.forEach(param => {
                let inputName = `hasil_aktual[${sampleCount}][${param.id}]`;
                
                if(param.tipe_input === 'Angka') {
                    tr += `<td><input type="number" step="0.01" name="${inputName}" class="input-sm" required></td>`;
                } else {
                    tr += `<td>
                        <select name="${inputName}" class="input-sm" required>
                            <option value="OK">OK</option>
                            <option value="NG">NG</option>
                        </select>
                    </td>`;
                }
            });

            tr += `<td>
                    <button type="button" class="btn-hapus-baris" style="background-color: #ef4444; color: white; border: none; padding: 4px 10px; cursor: pointer; border-radius:4px;"><i class="fas fa-times"></i></button>
                   </td>
                </tr>`;

            $('#table-body').append(tr);
        }

        $('#btn-tambah-sampel').click(function() {
            addSampleRow();
        });

        $(document).on('click', '.btn-hapus-baris', function() {
            $(this).closest('tr').remove();
            reindexSamples();
        });

        function reindexSamples() {
            let currentCount = 0;
            $('#table-body tr').each(function() {
                currentCount++;
                $(this).find('.sample-number strong').text(currentCount);
                $(this).find('.sample-number input[type="hidden"]').val(currentCount);
                
                // Update atribut name agar berurutan kembali (misal dari baris 3 jadi baris 2)
                let inputs = $(this).find('input[type="number"], select');
                inputs.each(function() {
                    let oldName = $(this).attr('name'); 
                    // oldName format: hasil_aktual[old_index][param_id]
                    let newName = oldName.replace(/hasil_aktual\[\d+\]/, `hasil_aktual[${currentCount}]`);
                    $(this).attr('name', newName);
                });
            });
            sampleCount = currentCount;
        }
    });
</script>
@endsection