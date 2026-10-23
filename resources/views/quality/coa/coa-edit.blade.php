@extends('layouts.staff-layout')

@section('konten')
@php
    $selectedTemplate = old('template_type', $coa->template_type);
@endphp
<style>
    .coa-edit-card { background: #fff; border: 1px solid #eaecf4; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,.05); margin: 25px 15px; overflow: hidden; }
    .coa-edit-header { background: #4e73df; color: #fff; padding: 18px 25px; }
    .coa-edit-body { padding: 25px; }
    .coa-edit-section { color: #4e73df; border-bottom: 2px solid #eaecf4; padding-bottom: 10px; margin: 15px 0 20px; }
    .coa-edit-grid { display: flex; flex-wrap: wrap; margin: 0 -12px; }
    .coa-edit-field { flex: 0 0 33.333%; max-width: 33.333%; box-sizing: border-box; padding: 0 12px; margin-bottom: 20px; }
    .coa-edit-field.wide { flex-basis: 100%; max-width: 100%; }
    .coa-edit-field label { display: block; font-size: .85rem; font-weight: 600; color: #3a3b45; margin-bottom: 8px; }
    .coa-edit-field input, .coa-edit-field select, .coa-edit-field textarea { width: 100%; box-sizing: border-box; padding: 10px 15px; border: 1px solid #d1d3e2; border-radius: 6px; color: #6e707e; font-size: .9rem; }
    .coa-edit-field input[readonly] { background: #eaecf4; cursor: not-allowed; }
    .coa-edit-field select:focus, .coa-edit-field input:focus, .coa-edit-field textarea:focus { border-color: #bac8f3; outline: none; box-shadow: 0 0 0 3px rgba(78,115,223,.15); }
    .yasulor-box { background: #fffdf5; border: 1px solid #fcefdc; border-radius: 8px; padding: 20px 25px 5px; margin-bottom: 25px; }
    .yasulor-section { color: #e3a600; border-bottom-color: #fcefdc; }
    .coa-edit-table-wrap { overflow-x: auto; border: 1px solid #eaecf4; border-radius: 8px; margin-bottom: 25px; }
    .coa-edit-table { width: 100%; border-collapse: collapse; font-size: .85rem; }
    .coa-edit-table th { background: #f8f9fc; padding: 12px; color: #4e73df; font-weight: 600; text-align: left; border-bottom: 2px solid #eaecf4; white-space: nowrap; }
    .coa-edit-table td { padding: 10px 12px; border-bottom: 1px solid #eaecf4; vertical-align: middle; color: #5a5c69; }
    .coa-edit-table input, .coa-edit-table select { width: 100%; min-width: 70px; box-sizing: border-box; padding: 6px 10px; border: 1px solid #d1d3e2; border-radius: 4px; font-size: .85rem; }
    .coa-edit-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 20px 25px; background: #f8f9fc; border-top: 1px solid #eaecf4; }
    .coa-edit-button { padding: 10px 20px; border: 0; border-radius: 6px; text-decoration: none; font-weight: 600; cursor: pointer; }
    @media(max-width: 768px) { .coa-edit-field { flex-basis: 100%; max-width: 100%; } }
</style>

<div class="coa-edit-card">
    <div class="coa-edit-header">
        <h4 style="margin: 0;"><i class="fas fa-edit"></i> Edit Dokumen COA</h4>
        <div style="margin-top: 5px;">{{ $coa->no_coa }} &mdash; MM: {{ $coa->no_mm }} &mdash; Batch: {{ $coa->no_batch }}</div>
    </div>

    <form action="{{ route('coa.update', $coa->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="coa-edit-body">
            @if(session('error'))
                <div role="alert" style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 18px;">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div role="alert" style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 18px;">
                    <strong>Periksa kembali input berikut:</strong>
                    <ul style="margin: 6px 0 0;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <h5 class="coa-edit-section" style="margin-top: 0;">Informasi Pengiriman & Customer</h5>
            <div class="coa-edit-grid">
                <div class="coa-edit-field">
                    <label>No. Dokumen</label>
                    <input type="text" value="{{ $coa->no_coa }}" readonly>
                </div>
                <div class="coa-edit-field">
                    <label>No. MM</label>
                    <input type="text" value="{{ $coa->no_mm }}" readonly>
                </div>
                <div class="coa-edit-field">
                    <label>Nomor Batch</label>
                    <input type="text" value="{{ $coa->no_batch }}" readonly>
                </div>
                <div class="coa-edit-field">
                    <label>Tipe Template COA <span style="color: #e74a3b;">*</span></label>
                    <select name="template_type" id="template_type" required>
                        <option value="GENERAL" {{ $selectedTemplate === 'GENERAL' ? 'selected' : '' }}>GENERAL (Standar Umum)</option>
                        <option value="YASULOR" {{ $selectedTemplate === 'YASULOR' ? 'selected' : '' }}>YASULOR (Format Khusus L'Oreal)</option>
                    </select>
                </div>
                <div class="coa-edit-field">
                    <label>Nama Customer</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name', $coa->customer_name) }}" list="customerList" autocomplete="off">
                    <datalist id="customerList">
                        @foreach($customers as $customer)
                            <option value="{{ $customer->nama_customer ?? $customer->name ?? $customer->customer_name }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div class="coa-edit-field">
                    <label>Item Code Customer</label>
                    <input type="text" value="{{ $coa->item_code_customer }}" readonly>
                </div>
                <div class="coa-edit-field">
                    <label>Delivery Quantity (Pcs)</label>
                    <input type="number" name="delivery_quantity" min="0" value="{{ old('delivery_quantity', $coa->delivery_quantity) }}">
                </div>
                <div class="coa-edit-field">
                    <label>Delivery Date</label>
                    <input type="date" name="delivery_date" value="{{ old('delivery_date', $coa->delivery_date ? \Illuminate\Support\Carbon::parse($coa->delivery_date)->format('Y-m-d') : '') }}">
                </div>
            </div>

            <div id="yasulor_extra_info" class="yasulor-box">
                <h5 class="coa-edit-section yasulor-section" style="margin-top: 0;"><i class="fas fa-star"></i> Ekstra Parameter Yasulor</h5>
                <div class="coa-edit-grid">
                    <div class="coa-edit-field">
                        <label>Machine No</label>
                        <input type="text" name="machine_no" value="{{ old('machine_no', $coa->machine_no) }}">
                    </div>
                    <div class="coa-edit-field">
                        <label>Sample Quantity DS (Pcs)</label>
                        <input type="number" min="0" name="sample_quantity" value="{{ old('sample_quantity', $coa->sample_quantity) }}">
                    </div>
                    <div class="coa-edit-field">
                        <label>Box Quantity Note</label>
                        <input type="text" name="box_qty_note" value="{{ old('box_qty_note', $coa->box_qty_note) }}">
                    </div>
                </div>
            </div>

            <h5 class="coa-edit-section"><i class="fas fa-list-check"></i> Detail Parameter Inspeksi</h5>
            <div class="coa-edit-table-wrap">
                <table class="coa-edit-table">
                    <thead>
                        <tr>
                            <th style="min-width: 180px;">Nama Parameter</th>
                            <th class="general-col">Standard Spesifikasi</th>
                            <th class="yasulor-col" style="display: none; min-width: 200px;">Teks Standard (Yasulor)</th>
                            <th class="yasulor-col" style="display: none; width: 120px;">Control Method</th>
                            <th class="yasulor-col" style="display: none; width: 100px;">Insp. Level</th>
                            <th class="yasulor-col" style="display: none; width: 80px;">AQL</th>
                            <th class="yasulor-col" style="display: none; width: 80px;">Freq.</th>
                            <th class="yasulor-col" style="display: none; width: 80px;">n sampling</th>
                            <th style="min-width: 170px; text-align: center; background: #eaecf4;">Result (MIN / AVG / MAX QIR)</th>
                            <th style="width: 90px;">Decision</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coa->details as $index => $detail)
                            @php
                                $standardText = $detail->standar_text;
                                if (empty($standardText)) {
                                    if ($detail->min_val !== null && $detail->max_val !== null) {
                                        $standardText = $detail->min_val . ' - ' . $detail->max_val . ($detail->uom ? ' ' . $detail->uom : '');
                                    } elseif ($detail->min_val !== null) {
                                        $standardText = 'Min. ' . $detail->min_val . ($detail->uom ? ' ' . $detail->uom : '');
                                    } elseif ($detail->max_val !== null) {
                                        $standardText = 'Max. ' . $detail->max_val . ($detail->uom ? ' ' . $detail->uom : '');
                                    }
                                }
                                $numericResult = is_numeric($detail->result_avg);
                            @endphp
                            <tr>
                                <td>
                                    <input type="hidden" name="parameters[{{ $index }}][id]" value="{{ $detail->id }}">
                                    <strong style="color: #2c3e50;">{{ $detail->nama_parameter }}</strong>
                                    @if($detail->uom)
                                        <span style="display: block; font-size: .75rem; color: #858796;">Satuan: {{ $detail->uom }}</span>
                                    @endif
                                </td>
                                <td class="general-col">
                                    <div style="background: #eaecf4; padding: 6px 10px; border-radius: 4px; text-align: center;">
                                        @if($detail->min_val !== null && $detail->max_val !== null)
                                            {{ $detail->min_val }} - {{ $detail->max_val }}
                                        @elseif($detail->min_val !== null)
                                            Min. {{ $detail->min_val }}
                                        @elseif($detail->max_val !== null)
                                            Max. {{ $detail->max_val }}
                                        @else
                                            {{ $standardText ?? '-' }}
                                        @endif
                                    </div>
                                </td>
                                <td class="yasulor-col" style="display: none;">
                                    <input type="text" name="parameters[{{ $index }}][standar_text]" value="{{ old("parameters.$index.standar_text", $standardText) }}" placeholder="Isi teks standar">
                                </td>
                                <td class="yasulor-col" style="display: none;"><input type="text" name="parameters[{{ $index }}][control_method]" value="{{ old("parameters.$index.control_method", $detail->control_method) }}"></td>
                                <td class="yasulor-col" style="display: none;"><input type="text" name="parameters[{{ $index }}][insp_level]" value="{{ old("parameters.$index.insp_level", $detail->insp_level) }}"></td>
                                <td class="yasulor-col" style="display: none;"><input type="text" name="parameters[{{ $index }}][aql]" value="{{ old("parameters.$index.aql", $detail->aql) }}"></td>
                                <td class="yasulor-col" style="display: none;"><input type="text" name="parameters[{{ $index }}][frequency]" value="{{ old("parameters.$index.frequency", $detail->frequency) }}"></td>
                                <td class="yasulor-col" style="display: none;"><input type="number" min="0" name="parameters[{{ $index }}][n_sampling]" value="{{ old("parameters.$index.n_sampling", $detail->n_sampling) }}"></td>
                                <td style="text-align: center;">
                                    @if($numericResult)
                                        <div style="font-size: .72rem; line-height: 1.5; text-align: left; margin-bottom: 4px;">
                                            <div>MIN: <strong>{{ $detail->result_min ?? '-' }}</strong></div>
                                            <div>MAX: <strong>{{ $detail->result_max ?? '-' }}</strong></div>
                                        </div>
                                        <div style="font-size: .72rem; text-align: left; margin-bottom: 3px;">AVG:</div>
                                        <input type="text" name="parameters[{{ $index }}][result_avg]" value="{{ old("parameters.$index.result_avg", $detail->result_avg) }}" style="text-align: center; font-weight: bold; color: #4e73df; background: #f8f9fc;" placeholder="AVG QIR">
                                    @else
                                        <input type="hidden" name="parameters[{{ $index }}][result_avg]" value="{{ old("parameters.$index.result_avg", $detail->result_avg) }}">
                                        <strong>{{ $detail->result_avg ?: '-' }}</strong>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <select name="parameters[{{ $index }}][decision]" style="font-weight: bold;">
                                        <option value="OK" {{ old("parameters.$index.decision", $detail->decision) === 'OK' ? 'selected' : '' }}>OK</option>
                                        <option value="NG" {{ old("parameters.$index.decision", $detail->decision) === 'NG' ? 'selected' : '' }}>NG</option>
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <h5 class="coa-edit-section">Keputusan & Tanda Tangan</h5>
            <div class="coa-edit-grid">
                <div class="coa-edit-field">
                    <label>Status Keputusan Akhir <span style="color: #e74a3b;">*</span></label>
                    <select name="status_decision" required>
                        @foreach(['PASSED', 'PASSED WITH NOTE'] as $status)
                            <option value="{{ $status }}" {{ old('status_decision', $coa->status_decision) === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="coa-edit-field">
                    <label>Prepared By <span style="color: #e74a3b;">*</span></label>
                    <input type="text" name="prepared_by" value="{{ old('prepared_by', $coa->prepared_by) }}" required>
                </div>
                <div class="coa-edit-field">
                    <label>Approved By <span style="color: #e74a3b;">*</span></label>
                    <input type="text" name="approved_by" value="{{ old('approved_by', $coa->approved_by) }}" required>
                </div>
                <div class="coa-edit-field wide">
                    <label>Catatan Umum (Remark) Dokumen COA</label>
                    <input type="text" name="remark" value="{{ old('remark', $coa->remark) }}">
                </div>
            </div>
        </div>

        <div class="coa-edit-footer">
            <a href="{{ route('coa.index') }}" class="coa-edit-button" style="background: #858796; color: #fff;">Kembali</a>
            <button type="submit" class="coa-edit-button" style="background: #1cc88a; color: #fff;"><i class="fas fa-save"></i> Simpan Perubahan</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const templateSelect = document.getElementById('template_type');
    const yasulorExtraInfo = document.getElementById('yasulor_extra_info');

    function toggleTemplateFields() {
        const isYasulor = templateSelect.value === 'YASULOR';
        document.querySelectorAll('.general-col').forEach(column => {
            column.style.display = isYasulor ? 'none' : 'table-cell';
        });
        document.querySelectorAll('.yasulor-col').forEach(column => {
            column.style.display = isYasulor ? 'table-cell' : 'none';
        });
        yasulorExtraInfo.style.display = isYasulor ? 'block' : 'none';
    }

    toggleTemplateFields();
    templateSelect.addEventListener('change', toggleTemplateFields);
});
</script>
@endsection
