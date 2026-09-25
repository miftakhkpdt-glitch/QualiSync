@extends('layouts.staff-layout')

@section('title', 'Edit Inspeksi Finished Goods')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .modern-container { max-width: 1200px; margin: 30px auto; font-family: 'Inter', sans-serif; }
    .modern-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; overflow: hidden; margin-bottom: 30px; }
    .card-header { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 20px 25px; }
    .card-header h4 { margin: 0; color: #1e293b; font-size: 18px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
    .card-body { padding: 30px 25px; }
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px; text-transform: uppercase; }
    .modern-input { width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; background-color: #f8fafc; }
    .select2-container .select2-selection--single { height: 40px !important; border: 1px solid #cbd5e1 !important; border-radius: 6px !important; background-color: #f8fafc !important; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px !important; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 38px !important; }
    .header-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
    .table-wrapper { overflow-x: auto; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 20px; }
    .modern-table { width: 100%; border-collapse: collapse; min-width: 1000px; text-align: center; }
    .modern-table th { background-color: #f1f5f9; color: #334155; font-weight: 600; font-size: 12px; padding: 12px 15px; border: 1px solid #e2e8f0; }
    .modern-table td { padding: 8px; border: 1px solid #e2e8f0; }
    .btn { padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
    .btn-warning { background-color: #f59e0b; color: white; }
    .btn-danger { background-color: #ef4444; color: white; padding: 8px 12px; }
    .btn-outline { background-color: #f8fafc; color: #475569; border: 1px solid #cbd5e1; }
    .section-divider { display: flex; align-items: center; margin: 30px 0 20px; color: #94a3b8; }
    .section-divider::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; margin-left: 15px; }
</style>
@endpush

@section('konten')
<div class="modern-container">
    <div style="margin-bottom: 20px;">
        <a href="{{ url('/in-proses/fg/riwayat') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('error'))
        <div style="background: #fee2e2; color: #dc2626; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="modern-card">
        <div class="card-header">
            <h4><i class="fas fa-edit" style="color: #f59e0b;"></i> Edit Inspeksi Finished Goods</h4>
        </div>
        
        <div class="card-body">
            <form action="{{ url('/in-proses/fg/update/' . $header->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="header-grid">
                    <div class="form-group">
                        <label class="form-label">Tanggal Inspeksi</label>
                        <input type="date" name="tanggal" class="modern-input" value="{{ $header->tanggal }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. Batch</label>
                        <input type="text" name="no_batch" class="modern-input" value="{{ $header->no_batch }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. MM / Produk</label>
                        <select name="no_mm" class="modern-input select2" required style="width: 100%;">
                            @foreach($masterItems as $item)
                                <option value="{{ $item->no_mm }}" {{ $header->no_mm ==$item->no_mm ? 'selected' : '' }}>
                                    {{ $item->no_mm }} - {{ $item->item_name ?? $item->nama_item ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="header-grid">
                    <div class="form-group">
                        <label class="form-label">Line Produksi</label>
                        <select name="line_produksi" class="modern-input" required>
                            <option value="Line 1" {{ $header->line_produksi == 'Line 1' ? 'selected' : '' }}>Line 1</option>
                            <option value="Line 2" {{ $header->line_produksi == 'Line 2' ? 'selected' : '' }}>Line 2</option>
                            <option value="Line 3" {{ $header->line_produksi == 'Line 3' ? 'selected' : '' }}>Line 3</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Shift Kerja</label>
                        <select name="shift" class="modern-input" required>
                            <option value="Shift 1" {{ $header->shift == 'Shift 1' ? 'selected' : '' }}>Shift 1</option>
                            <option value="Shift 2" {{ $header->shift == 'Shift 2' ? 'selected' : '' }}>Shift 2</option>
                            <option value="Shift 3" {{ $header->shift == 'Shift 3' ? 'selected' : '' }}>Shift 3</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipe Inspeksi</label>
                        <select name="inspection_type" class="modern-input" required>
                            <option value="Normal Inspection" {{ $header->inspection_type == 'Normal Inspection' ? 'selected' : '' }}>Inspeksi Normal</option>
                            <option value="Tightened Inspection" {{ $header->inspection_type == 'Tightened Inspection' ? 'selected' : '' }}>Inspeksi Ketat (Tightened)</option>
                        </select>
                    </div>
                </div>

                <div class="section-divider">
                    <span style="font-weight: 600; color: #64748b; font-size: 14px;">DETAIL TEMUAN</span>
                </div>

                <div class="table-wrapper">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th rowspan="2">Nama PIC</th>
                                <th rowspan="2">NO. BOX</th>
                                <th rowspan="2" style="min-width: 220px;">CACAT (DEFECT)</th>
                                <th colspan="3">AQL</th>
                                <th rowspan="2">Keputusan</th>
                                <th colspan="3">Keterangan</th>
                                <th rowspan="2">Aksi</th>
                            </tr>
                            <tr>
                                <th>Critical</th><th>Major</th><th>Minor</th>
                                <th>Jumlah Total</th><th>Sortir OK</th><th>Sortir NG</th>
                            </tr>
                        </thead>
                        <tbody id="dynamic-row-container">
                            @foreach($details as $index =>$row)
                            <tr>
                                <td><input type="text" name="pic[]" class="modern-input" value="{{ $row->pic }}" required></td>
                                <td><input type="text" name="no_box[]" class="modern-input" value="{{ $row->no_box }}"></td>
                                <td>
                                    <select name="defect[]" class="modern-input">
                                        <option value="">-- Pilih Cacat --</option>
                                        @foreach($defects as $defect)
                                            <option value="{{ $defect->nama_defect }}" {{ $row->defect ==$defect->nama_defect ? 'selected' : '' }}>
                                                {{ $defect->nama_defect }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" name="critical[]" class="modern-input" value="{{ $row->critical }}" required></td>
                                <td><input type="number" name="major[]" class="modern-input" value="{{ $row->major }}" required></td>
                                <td><input type="number" name="minor[]" class="modern-input" value="{{ $row->minor }}" required></td>
                                <td>
                                    <select name="decision[]" class="modern-input">
                                        <option value="OK" {{ $row->decision == 'OK' ? 'selected' : '' }}>OK</option>
                                        <option value="Reject" {{ $row->decision == 'Reject' ? 'selected' : '' }}>Reject</option>
                                    </select>
                                </td>
                                <td><input type="text" name="jml_box[]" class="modern-input" value="{{ $row->jml_box }}"></td>
                                <td><input type="text" name="sortir_ok[]" class="modern-input" value="{{ $row->sortir_ok }}"></td>
                                <td><input type="text" name="sortir_ng[]" class="modern-input" value="{{ $row->sortir_ng }}"></td>
                                <td>
                                    @if($index == 0)
                                        <i class="fas fa-lock" style="color: #cbd5e1;"></i>
                                    @else
                                        <button type="button" onclick="hapusBaris(this)" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-bottom: 30px;">
                    <button type="button" onclick="tambahBaris()" class="btn btn-outline" style="color: #3b82f6;">
                        <i class="fas fa-plus"></i> Tambah Baris Defect
                    </button>
                </div>

                <div style="text-align: right; margin-top: 20px;">
                    <button type="submit" class="btn btn-warning" style="padding: 12px 25px;">
                        <i class="fas fa-save"></i> Perbarui Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {$('.select2').select2();
});

var defectOptions = `<option value="">-- Pilih Cacat --</option>`;
@foreach($defects as $defect)
    defectOptions += `<option value="{{ $defect->nama_defect }}">{{ $defect->nama_defect }}</option>`;
@endforeach

    function tambahBaris() {
        var tableBody = document.getElementById('dynamic-row-container');
        var newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td><input type="text" name="pic[]" class="modern-input" required></td>
            <td><input type="text" name="no_box[]" class="modern-input"></td>
            <td>
                <select name="defect[]" class="modern-input">
                    ${defectOptions}
                </select>
            </td>
            <td><input type="number" name="critical[]" value="0" class="modern-input" required></td>
            <td><input type="number" name="major[]" value="0" class="modern-input" required></td>
            <td><input type="number" name="minor[]" value="0" class="modern-input" required></td>
            <td>
                <select name="decision[]" class="modern-input">
                    <option value="OK">OK</option>
                    <option value="Reject">Reject</option>
                </select>
            </td>
            <td><input type="text" name="jml_box[]" class="modern-input" value="0"></td>
            <td><input type="text" name="sortir_ok[]" class="modern-input" value="0"></td>
            <td><input type="text" name="sortir_ng[]" class="modern-input" value="0"></td>
            <td><button type="button" onclick="hapusBaris(this)" class="btn btn-danger"><i class="fas fa-trash"></i></button></td>
        `;
        tableBody.appendChild(newRow);
    }

    function hapusBaris(button) { 
        button.closest('tr').remove(); 
    }
</script>
@endsection