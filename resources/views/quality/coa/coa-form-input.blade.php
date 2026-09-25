@extends('layouts.staff-layout')

@section('konten')
<!-- Custom CSS Mandiri untuk Form Modern -->
<style>
    .coa-form-card { background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eaecf4; overflow: hidden; margin-bottom: 25px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .coa-form-header { background: #4e73df; color: white; padding: 18px 25px; display: flex; justify-content: space-between; align-items: center; }
    .coa-form-header h5 { margin: 0; font-weight: 600; font-size: 1.15rem; }
    .coa-form-header .header-badge { background: rgba(255,255,255,0.2); padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; letter-spacing: 0.5px; }
    .coa-form-body { padding: 30px; }
    
    /* Grid System Mandiri */
    .c-row { display: flex; flex-wrap: wrap; margin-left: -12px; margin-right: -12px; }
    .c-col-12 { flex: 0 0 100%; max-width: 100%; padding: 0 12px; margin-bottom: 20px; }
    .c-col-6 { flex: 0 0 50%; max-width: 50%; padding: 0 12px; margin-bottom: 20px; }
    .c-col-4 { flex: 0 0 33.333%; max-width: 33.333%; padding: 0 12px; margin-bottom: 20px; }
    .c-col-3 { flex: 0 0 25%; max-width: 25%; padding: 0 12px; margin-bottom: 20px; }
    @media(max-width: 768px) { .c-col-6, .c-col-4, .c-col-3 { flex: 0 0 100%; max-width: 100%; } }

    /* Form Elements */
    .c-label { display: block; font-weight: 600; color: #3a3b45; font-size: 0.85rem; margin-bottom: 8px; letter-spacing: 0.3px; }
    .c-input, .c-select { width: 100%; padding: 10px 15px; border: 1px solid #d1d3e2; border-radius: 6px; font-size: 0.9rem; color: #6e707e; background-color: #fff; transition: 0.2s; box-sizing: border-box; }
    .c-input:focus, .c-select:focus { border-color: #bac8f3; outline: none; box-shadow: 0 0 0 3px rgba(78,115,223,0.15); }
    .c-input[readonly] { background-color: #eaecf4; cursor: not-allowed; }
    
    /* Section Titles */
    .section-title { font-size: 0.9rem; font-weight: 700; color: #4e73df; text-transform: uppercase; border-bottom: 2px solid #eaecf4; padding-bottom: 10px; margin-top: 15px; margin-bottom: 20px; }
    .section-title.yasulor { color: #e3a600; border-bottom-color: #fcefdc; }

    /* Yasulor Extra Box */
    .yasulor-box { background-color: #fffdf5; border: 1px solid #fcefdc; border-radius: 8px; padding: 25px 25px 5px 25px; margin-bottom: 30px; box-shadow: inset 0 0 10px rgba(246,194,62,0.05); }
    
    /* Table Styling */
    .table-responsive { overflow-x: auto; border: 1px solid #eaecf4; border-radius: 8px; margin-bottom: 30px; }
    .c-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    .c-table th { background-color: #f8f9fc; padding: 12px; color: #4e73df; font-weight: 600; text-align: left; border-bottom: 2px solid #eaecf4; white-space: nowrap; }
    .c-table td { padding: 10px 12px; border-bottom: 1px solid #eaecf4; vertical-align: middle; color: #5a5c69; }
    .c-table tr:hover { background-color: #fdfdfe; }
    .t-input { width: 100%; padding: 6px 10px; border: 1px solid #d1d3e2; border-radius: 4px; font-size: 0.85rem; box-sizing: border-box; }
    .t-input:focus { border-color: #4e73df; outline: none; }
    
    /* Buttons */
    .coa-footer { background-color: #f8f9fc; padding: 20px 30px; display: flex; justify-content: flex-end; border-top: 1px solid #eaecf4; gap: 10px; }
    .btn-save { background: #1cc88a; color: white !important; padding: 10px 24px; border-radius: 6px; border: none; font-weight: 600; font-size: 0.9rem; cursor: pointer; transition: 0.2s; text-decoration: none; display: flex; align-items: center; box-shadow: 0 2px 6px rgba(28,200,138,0.3); }
    .btn-save:hover { background: #17a673; transform: translateY(-2px); }
    .btn-back { background: #858796; color: white !important; padding: 10px 24px; border-radius: 6px; border: none; font-weight: 600; font-size: 0.9rem; cursor: pointer; transition: 0.2s; text-decoration: none; display: flex; align-items: center; }
    .btn-back:hover { background: #717384; }
    
    .text-danger { color: #e74a3b; }
    .help-text { font-size: 0.75rem; color: #858796; margin-top: 5px; display: block; }
</style>

<div class="container-fluid" style="padding: 25px 15px;">
    
    <div class="coa-form-card">
        <!-- Header -->
        <div class="coa-form-header">
            <h5><i class="fas fa-file-signature" style="margin-right: 8px;"></i> Penerbitan Dokumen COA</h5>
            <div class="header-badge">
                <i class="fas fa-box" style="margin-right: 5px;"></i> MM: {{ $mm }} | Batch: {{ $batch }}
            </div>
        </div>

        <form action="{{ url('/coa/store') }}" method="POST">
            @csrf
            <input type="hidden" name="no_mm" value="{{ $mm }}">
            <input type="hidden" name="no_batch" value="{{ $batch }}">

            <div class="coa-form-body">
                
                <!-- 1. TEMPLATE SELECTION -->
                <div class="c-row" style="border-bottom: 1px dashed #eaecf4; padding-bottom: 10px; margin-bottom: 25px;">
                    <div class="c-col-4">
                        <label class="c-label">Tipe Template COA <span class="text-danger">*</span></label>
                        <select name="template_type" id="template_type" class="c-select" style="border-color: #4e73df; font-weight: 600; color: #4e73df;" required>
                            <option value="GENERAL">1. GENERAL (Standar Umum)</option>
                            <option value="YASULOR">2. YASULOR (Format Khusus L'Oreal)</option>
                        </select>
                        <span class="help-text">Pilih template untuk mengubah mode kolom tabel.</span>
                    </div>
                </div>

                <!-- 2. INFO UMUM -->
                <div class="section-title"><i class="fas fa-truck" style="margin-right: 6px;"></i> Informasi Pengiriman & Customer</div>
                <div class="c-row">
                    <div class="c-col-4">
                        <label class="c-label">Nama Customer</label>
                        <input type="text" name="customer_name" class="c-input" list="customerList" placeholder="Pilih atau ketik nama customer..." autocomplete="off">
                        <datalist id="customerList">
                            @if(isset($customers) && count($customers) > 0)
                                @foreach($customers as $cust)
                                    <option value="{{ $cust->nama_customer ?? $cust->name ?? $cust->customer_name }}"></option>
                                @endforeach
                            @endif
                        </datalist>
                    </div>

                    <div class="c-col-4">
                        <label class="c-label">Item Code Customer</label>
                        <input type="text" name="item_code_customer" class="c-input" placeholder="Kode item di pihak customer">
                    </div>
                    <div class="c-col-4">
                        <label class="c-label">PO Number</label>
                        <input type="text" name="po_number" class="c-input" placeholder="Nomor Purchase Order">
                    </div>
                    <div class="c-col-6">
                        <label class="c-label">Delivery Quantity (Pcs)</label>
                        <input type="number" name="delivery_quantity" class="c-input" placeholder="Total qty yang dikirim">
                    </div>
                    <div class="c-col-6">
                        <label class="c-label">Delivery Date</label>
                        <input type="date" name="delivery_date" class="c-input">
                    </div>
                </div>

                <!-- 3. INFO KHUSUS YASULOR -->
                <div id="yasulor_extra_info" class="yasulor-box" style="display: none;">
                    <div class="section-title yasulor" style="margin-top: 0;"><i class="fas fa-star" style="margin-right: 6px;"></i> Ekstra Parameter Yasulor</div>
                    <div class="c-row">
                        <div class="c-col-3">
                            <label class="c-label">Machine No</label>
                            <input type="text" name="machine_no" class="c-input">
                        </div>                                                
                        <div class="c-col-4">
                            <label class="c-label">Sample Quantity DS (Pcs)</label>
                            <input type="number" name="sample_quantity" class="c-input">
                        </div>
                        <div class="c-col-4">
                            <label class="c-label">Box Quantity Note</label>
                            <input type="text" name="box_qty_note" class="c-input" placeholder="Cth: Qty per box 100 pcs">
                        </div>
                    </div>
                </div>

                <!-- 4. TABEL PARAMETER -->
                <div class="section-title"><i class="fas fa-list-check" style="margin-right: 6px;"></i> Detail Parameter Inspeksi (Dari QIR)</div>
                <div class="table-responsive">
                    <table class="c-table">
                        <thead>
                            <tr>
                                <th style="min-width: 180px;">Nama Parameter</th>
                                
                                <!-- Kolom General -->
                                <th class="general-col">Standard Spesifikasi</th>
                                
                                <!-- Kolom Yasulor -->
                                <th class="yasulor-col" style="display: none; min-width: 200px;">Teks Standard (Yasulor)</th>
                                <th class="yasulor-col" style="display: none; width: 120px;">Control Method</th>
                                <th class="yasulor-col" style="display: none; width: 100px;">Insp. Level</th>
                                <th class="yasulor-col" style="display: none; width: 80px;">AQL</th>
                                <th class="yasulor-col" style="display: none; width: 80px;">Freq.</th>
                                <th class="yasulor-col" style="display: none; width: 70px;">n sampling</th>
                                
                                <!-- Kolom Hasil QIR (MUNCUL DI KEDUA MODE GENERAL & YASULOR) -->
                                <th style="min-width: 140px; text-align: center; background-color: #eaecf4; color: #4e73df;">Result (AVG QIR)</th>
                                
                                <!-- Kolom Keputusan Yasulor -->
                                <th class="yasulor-col" style="display: none; width: 90px;">Decision</th>
                                <th class="yasulor-col" style="display: none; min-width: 130px;">Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($parameters as $index => $param)
                            @php
                                // Mengambil ID parameter (menyesuaikan nama field di db)
                                $pId = $param->parameter_id ?? $param->id;
                                
                                // Nilai Rata-rata dari QIR (jika ada)
                                $valHasil = $rekomendasiHasil[$pId] ?? '';
                            @endphp
                            <tr>
                                <td>
                                    <!-- Hidden Inputs untuk Data Origin -->
                                    <input type="hidden" name="parameters[{{ $index }}][parameter_id]" value="{{ $pId }}">
                                    <input type="hidden" name="parameters[{{ $index }}][nama_parameter]" value="{{ $param->nama_parameter }}">
                                    <input type="hidden" name="parameters[{{ $index }}][uom]" value="{{ $param->satuan }}">
                                    <input type="hidden" name="parameters[{{ $index }}][min_val]" value="{{ $param->min_value }}">
                                    <input type="hidden" name="parameters[{{ $index }}][max_val]" value="{{ $param->max_value }}">
                                    
                                    <strong style="color: #2c3e50;">{{ $param->nama_parameter }}</strong>
                                    @if($param->satuan)
                                        <span style="display:block; font-size: 0.75rem; color: #858796;">Satuan: {{ $param->satuan }}</span>
                                    @endif
                                </td>

                                <!-- Kolom General Standard -->
<td class="general-col">
    <div style="background: #eaecf4; padding: 6px 10px; border-radius: 4px; font-weight: 500; font-size: 0.8rem; text-align: center;">
        @if($param->min_value !== null && $param->max_value !== null)
            {{ $param->min_value }} - {{ $param->max_value }}
        @elseif($param->min_value !== null)
            Min. {{ $param->min_value }}
        @elseif($param->max_value !== null)
            Max. {{ $param->max_value }}
        @else
            {{ $param->standar_teks ?? '-' }}
        @endif
    </div>
</td>

<!-- Kolom Yasulor Standard Teks -->
<td class="yasulor-col" style="display: none;">
    @php
        $stdText = $param->standar_teks;
        if(empty($stdText)) {
            if($param->min_value !== null && $param->max_value !== null) {
                $stdText = $param->min_value . ' - ' . $param->max_value . ($param->satuan ? ' '.$param->satuan : '');
            } elseif($param->min_value !== null) {
                $stdText = 'Min. ' . $param->min_value . ($param->satuan ? ' '.$param->satuan : '');
            } elseif($param->max_value !== null) {
                $stdText = 'Max. ' . $param->max_value . ($param->satuan ? ' '.$param->satuan : '');
            }
        }
    @endphp
    <input type="text" name="parameters[{{ $index }}][standar_text]" class="t-input" value="{{ $stdText }}" placeholder="Isi teks standar">
</td>

                                <!-- Kolom Yasulor Ekstra (OTOMATIS TERISI DARI MASTER STANDARD) -->
                                <td class="yasulor-col" style="display: none;">
                                    <input type="text" name="parameters[{{ $index }}][control_method]" class="t-input" value="{{ $param->control_method ?? '' }}">
                                </td>
                                <td class="yasulor-col" style="display: none;">
                                    <input type="text" name="parameters[{{ $index }}][insp_level]" class="t-input" value="{{ $param->insp_level ?? '' }}">
                                </td>
                                <td class="yasulor-col" style="display: none;">
                                    <input type="text" name="parameters[{{ $index }}][aql]" class="t-input" value="{{ $param->aql ?? '' }}">
                                </td>
                                <td class="yasulor-col" style="display: none;">
                                    <input type="text" name="parameters[{{ $index }}][frequency]" class="t-input" value="{{ $param->frequency ?? $param->freq ?? '' }}">
                                </td>
                                <td class="yasulor-col" style="display: none;">
                                    <input type="number" name="parameters[{{ $index }}][n_sampling]" class="t-input" value="{{ $param->n_sampling ?? $param->n ?? '' }}">
                                </td>

                                <!-- Result / Hasil AVG QIR (MUNCUL DI KEDUA MODE GENERAL MAUPUN YASULOR) -->
                                <td style="text-align: center;">
                                    @php
                                        // 1. Sesuaikan variabel $nama_parameter dengan variabel yang Anda gunakan
                                        // untuk memunculkan nama di kolom paling kiri. 
                                        // (Bisa $param->nama_parameter, $item->nama_parameter, atau $detail->nama_parameter)
                                        $namaParam = $param->nama_parameter ?? ''; 
                                        
                                        // 2. Daftar parameter yang dipaksa menjadi OK
                                        $parameterVisual = ['Air Tight Test', 'Tape Test', 'Unzip', 'Pinch'];
                                        
                                        // 3. Jika nama parameter cocok, timpa variabel $valHasil menjadi OK
                                        if(in_array($namaParam, $parameterVisual)) {
                                            $valHasil = 'OK';
                                        }
                                    @endphp
                                    
                                    <input type="text" 
                                           name="parameters[{{ $index }}][result_avg]" 
                                           class="t-input" 
                                           style="text-align: center; font-weight: bold; color: #4e73df; background-color: #f8f9fc;" 
                                           value="{{ $valHasil }}" 
                                           placeholder="Otomatis QIR">
                                </td>

                                <!-- Kolom Yasulor Decision & Remark -->
                                <td class="yasulor-col" style="display: none;">
                                    <select name="parameters[{{ $index }}][decision]" class="t-input" style="font-weight: bold;">
                                        <option value="OK" style="color: green;">OK</option>
                                        <option value="NG" style="color: red;">NG</option>
                                    </select>
                                </td>
                                <td class="yasulor-col" style="display: none;">
                                    <input type="text" name="parameters[{{ $index }}][remark]" class="t-input">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- 5. KEPUTUSAN & TANDA TANGAN -->
                <div style="background: #f8f9fc; padding: 25px; border-radius: 8px; border: 1px solid #eaecf4;">
                    <div class="c-row">
                        <div class="c-col-3">
                            <label class="c-label">Status Keputusan Akhir <span class="text-danger">*</span></label>
                            <select name="status_decision" class="c-select" style="font-weight: bold; border-color: #1cc88a;" required>
                                <option value="PASSED">PASSED</option>
                                <option value="PASSED WITH NOTE">PASSED WITH NOTE</option>
                                <option value="RELEASE">RELEASE</option>
                                <option value="BLOCKED">BLOCKED</option>
                            </select>
                        </div>
                        <div class="c-col-9">
                            <label class="c-label">Catatan Umum (Remark) Dokumen COA</label>
                            <input type="text" name="remark" class="c-input" placeholder="Tulis catatan opsional jika ada...">
                        </div>
                        
                        <div class="c-col-12"><hr style="border-top: 1px solid #d1d3e2; margin: 10px 0 20px 0;"></div>

                        <div class="c-col-6">
                            <label class="c-label">Prepared By (Pembuat Dokumen) <span class="text-danger">*</span></label>
                            <input type="text" name="prepared_by" class="c-input" value="Miftakh" required>
                        </div>
                        <div class="c-col-6">
                            <label class="c-label">Approved By (Penyetuju) <span class="text-danger">*</span></label>
                            <input type="text" name="approved_by" class="c-input" value="Rajib" required>
                        </div>
                    </div>
                </div>

            </div>
            
            <!-- Footer Form -->
            <div class="coa-footer">
                <a href="{{ url('/coa/create') }}" class="btn-back">
                    <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Kembali
                </a>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save" style="margin-right: 8px;"></i> Simpan & Terbitkan COA
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript Logic Toggling -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const templateSelect = document.getElementById('template_type');
    const yasulorExtraInfo = document.getElementById('yasulor_extra_info');
    
    function toggleTemplateFields() {
        const selected = templateSelect.value;
        const generalCols = document.querySelectorAll('.general-col');
        const yasulorCols = document.querySelectorAll('.yasulor-col');

        if(selected === 'YASULOR') {
            yasulorExtraInfo.style.display = 'block';
            generalCols.forEach(col => { col.style.display = 'none'; });
            yasulorCols.forEach(col => { col.style.display = 'table-cell'; });
        } else {
            yasulorExtraInfo.style.display = 'none';
            generalCols.forEach(col => { col.style.display = 'table-cell'; });
            yasulorCols.forEach(col => { col.style.display = 'none'; });
        }
    }

    // Trigger saat render pertama
    toggleTemplateFields();
    // Trigger saat user mengganti dropdown
    templateSelect.addEventListener('change', toggleTemplateFields);
});
</script>
@endsection