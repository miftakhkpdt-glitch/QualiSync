@extends('layouts.staff-layout')

@section('title', 'Kelola Standar Parameter')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .modern-container { max-width: 1400px; margin: 30px auto; font-family: 'Inter', sans-serif; }
    .modern-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; padding: 30px; }
    
    .item-header { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; }
    .item-title { font-size: 20px; font-weight: bold; color: #1e293b; margin: 0; }
    .item-subtitle { color: #64748b; font-size: 14px; margin-top: 5px; }
    
    .modern-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
    .modern-table th { background-color: #f1f5f9; color: #334155; font-weight: 600; padding: 10px; border: 1px solid #e2e8f0; text-align: center; }
    .modern-table td { padding: 8px; border: 1px solid #e2e8f0; vertical-align: middle; }
    
    .input-box { width: 100%; padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; }
    .btn-action { padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 8px; color: white; }
    .btn-add { background-color: #10b981; margin-top: 15px; }
    .btn-save { background-color: #3b82f6; }
    .btn-remove { background-color: #ef4444; padding: 8px 12px; }
    
    .bg-yasulor-header { background-color: #e0f2fe !important; color: #0369a1 !important; }
</style>
@endpush

@section('konten')
<div class="modern-container">
    @if(session('error'))
        <div style="background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="modern-card">
        <div class="item-header">
            <div>
                <h2 class="item-title">Kelola Standar: {{ $item->no_mm }}</h2>
                <div class="item-subtitle">{{ $item->nama_material ?? 'Nama Item Tidak Diketahui' }}</div>
            </div>
            <div>
                <a href="{{ url('/master-standar') }}" style="color: #64748b; text-decoration: none; font-weight: bold;"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </div>

        <form action="{{ url('/master-standar/store/' . $item->no_mm) }}" method="POST">
            @csrf
            
            <div style="overflow-x: auto;">
                <table class="modern-table" id="parameter-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th style="min-width: 200px;">Nama Parameter</th>
                            <th style="width: 100px;">Nilai Min</th>
                            <th style="width: 100px;">Nilai Max</th>
                            <th style="min-width: 150px;">Standar Teks/Visual</th>
                            
                            <!-- KOLOM TAMBAHAN UNTUK YASULOR -->
                            <th class="bg-yasulor-header" style="width: 140px;">Control Method</th>
                            <th class="bg-yasulor-header" style="width: 90px;">Insp. Level</th>
                            <th class="bg-yasulor-header" style="width: 80px;">AQL</th>
                            <th class="bg-yasulor-header" style="width: 100px;">Freq</th>
                            <th class="bg-yasulor-header" style="width: 60px;">n</th>
                            
                            <th style="width: 60px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-parameter">
                        
                        <!-- JIKA SUDAH ADA DATA TERSIMPAN -->
                        @if(count($standarTersimpan) > 0)
                            @foreach($standarTersimpan as $index => $std)
                            <tr>
                                <td class="row-number" style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                                <td>
                                    <select name="parameter_id[]" class="input-box select-param" required>
                                        <option value="">-- Pilih Parameter --</option>
                                        @foreach($semuaParameter as $param)
                                            <option value="{{ $param->id }}" data-tipe="{{ $param->tipe_input }}" {{ $std->parameter_id == $param->id ? 'selected' : '' }}>
                                                {{ $param->nama_parameter }} {{ $param->satuan ? '('.$param->satuan.')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" step="0.01" name="min_value[]" class="input-box" value="{{ $std->min_value }}"></td>
                                <td><input type="number" step="0.01" name="max_value[]" class="input-box" value="{{ $std->max_value }}"></td>
                                <td><input type="text" name="standar_teks[]" class="input-box" value="{{ $std->standar_teks }}" placeholder="Cth: OK"></td>
                                
                                <!-- INPUT YASULOR (OPSIONAL) -->
                                <td><input type="text" name="control_method[]" class="input-box" value="{{ $std->control_method }}" placeholder="Method"></td>
                                <td><input type="text" name="insp_level[]" class="input-box" value="{{ $std->insp_level }}" placeholder="Level"></td>
                                <td><input type="text" name="aql[]" class="input-box" value="{{ $std->aql }}" placeholder="AQL"></td>
                                <td><input type="text" name="freq[]" class="input-box" value="{{ $std->freq }}" placeholder="Freq"></td>
                                <td><input type="text" name="n[]" class="input-box" value="{{ $std->n }}" placeholder="n"></td>

                                <td style="text-align: center;">
                                    <button type="button" class="btn-action btn-remove"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <!-- JIKA MASIH KOSONG, TAMPILKAN 1 BARIS DEFAULT -->
                            <tr>
                                <td class="row-number" style="text-align: center; font-weight: bold;">1</td>
                                <td>
                                    <select name="parameter_id[]" class="input-box select-param" required>
                                        <option value="">-- Pilih Parameter --</option>
                                        @foreach($semuaParameter as $param)
                                            <option value="{{ $param->id }}" data-tipe="{{ $param->tipe_input }}">
                                                {{ $param->nama_parameter }} {{ $param->satuan ? '('.$param->satuan.')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" step="0.01" name="min_value[]" class="input-box"></td>
                                <td><input type="number" step="0.01" name="max_value[]" class="input-box"></td>
                                <td><input type="text" name="standar_teks[]" class="input-box" placeholder="Cth: OK"></td>
                                
                                <!-- INPUT YASULOR (OPSIONAL) -->
                                <td><input type="text" name="control_method[]" class="input-box" placeholder="Method"></td>
                                <td><input type="text" name="insp_level[]" class="input-box" placeholder="Level"></td>
                                <td><input type="text" name="aql[]" class="input-box" placeholder="AQL"></td>
                                <td><input type="text" name="freq[]" class="input-box" placeholder="Freq"></td>
                                <td><input type="text" name="n[]" class="input-box" placeholder="n"></td>

                                <td style="text-align: center;">
                                    <button type="button" class="btn-action btn-remove"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <button type="button" id="btn-tambah" class="btn-action btn-add"><i class="fas fa-plus"></i> Tambah Parameter Baru</button>

            <div style="margin-top: 40px; text-align: right; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                <button type="submit" class="btn-action btn-save"><i class="fas fa-save"></i> Simpan Standar Produk</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        
        let parameterOptions = `
            <option value="">-- Pilih Parameter --</option>
            @foreach($semuaParameter as $param)
                <option value="{{ $param->id }}" data-tipe="{{ $param->tipe_input }}">
                    {{ $param->nama_parameter }} {{ $param->satuan ? '('.$param->satuan.')' : '' }}
                </option>
            @endforeach
        `;

        // Fitur Tambah Baris Parameter
        $('#btn-tambah').click(function() {
            let newRow = `
                <tr>
                    <td class="row-number" style="text-align: center; font-weight: bold;"></td>
                    <td>
                        <select name="parameter_id[]" class="input-box select-param" required>
                            ${parameterOptions}
                        </select>
                    </td>
                    <td><input type="number" step="0.01" name="min_value[]" class="input-box"></td>
                    <td><input type="number" step="0.01" name="max_value[]" class="input-box"></td>
                    <td><input type="text" name="standar_teks[]" class="input-box" placeholder="Cth: OK"></td>
                    
                    <!-- INPUT YASULOR (OPSIONAL) -->
                    <td><input type="text" name="control_method[]" class="input-box" placeholder="Method"></td>
                    <td><input type="text" name="insp_level[]" class="input-box" placeholder="Level"></td>
                    <td><input type="text" name="aql[]" class="input-box" placeholder="AQL"></td>
                    <td><input type="text" name="freq[]" class="input-box" placeholder="Freq"></td>
                    <td><input type="text" name="n[]" class="input-box" placeholder="n"></td>

                    <td style="text-align: center;">
                        <button type="button" class="btn-action btn-remove"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
            $('#tbody-parameter').append(newRow);
            reindexRows();
        });

        // Fitur Hapus Baris
        $(document).on('click', '.btn-remove', function() {
            $(this).closest('tr').remove();
            reindexRows();
        });

        // Rapikan Nomor Urut
        function reindexRows() {
            $('#tbody-parameter tr').each(function(index) {
                $(this).find('.row-number').text(index + 1);
            });
        }
        reindexRows();
    });
</script>
@endsection