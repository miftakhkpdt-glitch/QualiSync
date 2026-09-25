@extends('layouts.staff-layout')

@section('title', 'Edit Dokumen QIR Dinamis')

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
    <div style="margin-bottom: 20px;">
        <a href="{{ url('/qir') }}" style="background: #64748b; color: white; padding: 8px 15px; text-decoration: none; border-radius: 6px; font-size: 13px; font-weight: bold;"><i class="fas fa-arrow-left"></i> Batal & Kembali</a>
    </div>

    <form action="{{ url('/qir/update/' . $qir->id) }}" method="POST" id="form-qir">
        @csrf
        <div class="modern-card">
            <h4 style="margin-top:0; color:#1e293b; border-bottom:2px solid #f1f5f9; padding-bottom:15px; margin-bottom:20px;">
                <i class="fas fa-edit" style="color:#eab308;"></i> Edit QIR Dinamis: {{ $qir->no_batch }}
            </h4>

            <!-- HEADER FORM -->
            <div class="header-grid">
                <div>
                    <div class="form-group">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="modern-input" value="{{ $qir->tanggal }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. Batch</label>
                        <input type="text" name="no_batch" class="modern-input" value="{{ $qir->no_batch }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. MM / Item Produk</label>
                        <!-- Saat edit, dropdown dikunci saja agar tidak merusak data jika beda parameter -->
                        <input type="text" class="modern-input" value="{{ $qir->no_mm }}" disabled style="background:#f1f5f9;">
                        <input type="hidden" name="no_mm" value="{{ $qir->no_mm }}">
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label">Shift Kerja</label>
                        <select name="shift" class="modern-input" required>
                            <option value="Shift 1" {{ $qir->shift == 'Shift 1' ? 'selected' : '' }}>Shift 1</option>
                            <option value="Shift 2" {{ $qir->shift == 'Shift 2' ? 'selected' : '' }}>Shift 2</option>
                            <option value="Shift 3" {{ $qir->shift == 'Shift 3' ? 'selected' : '' }}>Shift 3</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Line Produksi</label>
                        <select name="line_produksi" class="modern-input" required>
                            <option value="Line 1" {{ $qir->line_produksi == 'Line 1' ? 'selected' : '' }}>Line 1</option>
                            <option value="Line 2" {{ $qir->line_produksi == 'Line 2' ? 'selected' : '' }}>Line 2</option>
                            <option value="Line 3" {{ $qir->line_produksi == 'Line 3' ? 'selected' : '' }}>Line 3</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- TABEL DINAMIS (DIRENDER OLEH JAVASCRIPT BERDASARKAN DATA LAMA) -->
            <div class="table-wrapper">
                <table class="modern-table">
                    <thead id="table-head">
                        <tr><th>Loading Data...</th></tr>
                    </thead>
                    <tbody id="table-body">
                        <!-- Baris Data Lama Muncul Di Sini -->
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 10px;">
                <button type="button" id="btn-tambah-sampel" style="background-color: #10b981; color: white; padding: 8px 15px; border: none; border-radius: 6px; cursor: pointer;">
                    <i class="fas fa-plus"></i> Tambah Sampel
                </button>
            </div>

            <div style="text-align: right; margin-top: 30px;">
                <button type="submit" style="background-color: #eab308; color: white; padding: 12px 30px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
                    <i class="fas fa-save"></i> Perbarui Dokumen QIR
                </button>
            </div>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        
        // Data dari Controller dilempar langsung ke dalam Javascript
        let currentParameters = @json($parameters);
        let existingData = @json($existingData); 
        let sampleCount = 0;

        function renderEditTable() {
            if(currentParameters.length === 0) {
                $('#table-head').html('<tr><th style="color:red;">Error: Parameter Master tidak ditemukan untuk item ini.</th></tr>');
                return;
            }

            // 1. Gambar Judul Kolom (Sama seperti halaman Create)
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
            $('#table-body').empty();

            // 2. Looping Data Lama yang tersimpan di Database untuk digambar di baris input
            for (const [sampleNo, hasil_parameter] of Object.entries(existingData)) {
                sampleCount++;
                let tr = `<tr>
                    <td class="sample-number">
                        <strong>${sampleCount}</strong>
                        <input type="hidden" name="sample_no[]" value="${sampleCount}">
                    </td>`;

                // Looping kotak input per baris
                currentParameters.forEach(param => {
                    let inputName = `hasil_aktual[${sampleCount}][${param.id}]`;
                    
                    // Ambil nilai lama jika ada, jika kosong set jadi text kosong
                    let val = hasil_parameter[param.id] !== undefined ? hasil_parameter[param.id] : '';
                    
                    if(param.tipe_input === 'Angka') {
                        tr += `<td><input type="number" step="0.01" name="${inputName}" class="input-sm" value="${val}" required></td>`;
                    } else {
                        let selOK = val === 'OK' ? 'selected' : '';
                        let selNG = val === 'NG' ? 'selected' : '';
                        tr += `<td>
                            <select name="${inputName}" class="input-sm" required>
                                <option value="OK" ${selOK}>OK</option>
                                <option value="NG" ${selNG}>NG</option>
                            </select>
                        </td>`;
                    }
                });

                tr += `<td><button type="button" class="btn-hapus-baris" style="background-color: #ef4444; color: white; border: none; padding: 4px 10px; cursor: pointer; border-radius:4px;"><i class="fas fa-times"></i></button></td></tr>`;
                
                $('#table-body').append(tr);
            }
        }

        // Jalankan fungsi gambar tabel saat halaman pertama kali diload
        renderEditTable();

        // Fitur Tambah Baris (Persis dengan Create)
        $('#btn-tambah-sampel').click(function() {
            sampleCount++;
            let tr = `<tr><td class="sample-number"><strong>${sampleCount}</strong><input type="hidden" name="sample_no[]" value="${sampleCount}"></td>`;
            currentParameters.forEach(param => {
                let inputName = `hasil_aktual[${sampleCount}][${param.id}]`;
                if(param.tipe_input === 'Angka') {
                    tr += `<td><input type="number" step="0.01" name="${inputName}" class="input-sm" required></td>`;
                } else {
                    tr += `<td><select name="${inputName}" class="input-sm" required><option value="OK">OK</option><option value="NG">NG</option></select></td>`;
                }
            });
            tr += `<td><button type="button" class="btn-hapus-baris" style="background-color: #ef4444; color: white; border: none; padding: 4px 10px; cursor: pointer; border-radius:4px;"><i class="fas fa-times"></i></button></td></tr>`;
            $('#table-body').append(tr);
        });

        // Fitur Hapus Baris dan Reindex
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
                
                let inputs = $(this).find('input[type="number"], select');
                inputs.each(function() {
                    let oldName = $(this).attr('name'); 
                    let newName = oldName.replace(/hasil_aktual\[\d+\]/, `hasil_aktual[${currentCount}]`);
                    $(this).attr('name', newName);
                });
            });
            sampleCount = currentCount;
        }
    });
</script>
@endsection