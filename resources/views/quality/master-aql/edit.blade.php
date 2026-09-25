@extends('layouts.staff-layout')

@section('konten')
<div style="padding: 24px; background-color: #f8fafc; min-height: 100vh; font-family: 'Inter', system-ui, sans-serif;">
    
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 22px; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-edit" style="color: #2563eb;"></i> Edit Standar AQL & CRQS
        </h1>
        <p style="font-size: 13px; color: #64748b; margin: 4px 0 0 0;">Perbarui parameter standar untuk customer ini.</p>
    </div>

    <div style="max-width: 600px; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden;">
        
        <form action="{{ url('/master-aql/' . $aqlStandard->id) }}" method="POST" style="padding: 20px;">
            @csrf
            @method('PUT')
            
            <!-- Pilih Customer -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px;">PILIH CUSTOMER *</label>
                <select id="customer_select" name="customer_id" onchange="toggleAqlFields()" required style="width: 100%; padding: 10px 12px; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
                    <option value="">-- Pilih Customer --</option>
                    @foreach($customers as $cust)
                        @php 
                            $custId = $cust->id;
                            $custName = $cust->nama_customer ?? $cust->customer_name; 
                        @endphp
                        <option value="{{ $custId }}" data-name="{{ $custName }}" {{ $aqlStandard->customer_id == $custId ? 'selected' : '' }}>
                            {{ $custName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Input Hidden untuk Customer Name & System Type -->
            <input type="hidden" id="customer_name" name="customer_name" value="{{ $aqlStandard->customer_name }}">
            <input type="hidden" id="system_type" name="system_type" value="{{ $aqlStandard->tipe_standar ?? $aqlStandard->system_type }}">

            @php
                $isCrqs = ($aqlStandard->tipe_standar ?? $aqlStandard->system_type) == 'crqs';
            @endphp

            <!-- SEKSI AQL STANDARD -->
            <div id="standard_aql_section" style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 20px; display: {{ $isCrqs ? 'none' : 'block' }};">
                <span style="display: block; font-size: 12px; font-weight: 700; color: #1e293b; margin-bottom: 12px;">Parameter Standard AQL</span>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label style="font-size: 11px; font-weight: 600; color: #64748b; display: block; margin-bottom: 4px;">Zero Defect</label>
                        <input type="text" name="aql_zero_defect" value="{{ $aqlStandard->aql_zero_defect }}" placeholder="Contoh: 0" style="width: 100%; padding: 8px; font-size: 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="font-size: 11px; font-weight: 600; color: #64748b; display: block; margin-bottom: 4px;">Critical</label>
                        <input type="text" name="aql_critical" value="{{ $aqlStandard->aql_critical }}" placeholder="Contoh: 0.1 / 0.65" style="width: 100%; padding: 8px; font-size: 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="font-size: 11px; font-weight: 600; color: #64748b; display: block; margin-bottom: 4px;">Major</label>
                        <input type="text" name="aql_major" value="{{ $aqlStandard->aql_major }}" placeholder="Contoh: 1.0 / 2.5" style="width: 100%; padding: 8px; font-size: 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="font-size: 11px; font-weight: 600; color: #64748b; display: block; margin-bottom: 4px;">Minor</label>
                        <input type="text" name="aql_minor" value="{{ $aqlStandard->aql_minor }}" placeholder="Contoh: 6.5 / 4.0" style="width: 100%; padding: 8px; font-size: 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                    </div>
                </div>
            </div>

            <!-- SEKSI CRQS (UNILEVER) -->
            <div id="crqs_section" style="background: #fff7ed; padding: 16px; border-radius: 8px; border: 1px solid #ffedd5; margin-bottom: 20px; display: {{ $isCrqs ? 'block' : 'none' }};">
                <span style="display: block; font-size: 12px; font-weight: 700; color: #c2410c; margin-bottom: 12px;">Parameter CRQS (Unilever)</span>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label style="font-size: 11px; font-weight: 600; color: #9a3412; display: block; margin-bottom: 4px;">Amber (%)</label>
                        <input type="text" name="crqs_amber" value="{{ $aqlStandard->crqs_amber }}" placeholder="Contoh: 3%" style="width: 100%; padding: 8px; font-size: 12px; border: 1px solid #fdba74; border-radius: 6px; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="font-size: 11px; font-weight: 600; color: #9a3412; display: block; margin-bottom: 4px;">Red (%)</label>
                        <input type="text" name="crqs_red" value="{{ $aqlStandard->crqs_red }}" placeholder="Contoh: 0%" style="width: 100%; padding: 8px; font-size: 12px; border: 1px solid #fdba74; border-radius: 6px; box-sizing: border-box;">
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" style="flex: 1; padding: 11px; background: #2563eb; color: #ffffff; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-save"></i> Perbarui Data
                </button>
                <a href="{{ url('/master-aql') }}" style="padding: 11px 20px; background: #e2e8f0; color: #334155; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; text-align: center;">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function toggleAqlFields() {
    const select = document.getElementById('customer_select');
    const selectedOption = select.options[select.selectedIndex];
    
    const rawName = selectedOption.getAttribute('data-name') || '';
    const customerName = rawName.toUpperCase();
    
    document.getElementById('customer_name').value = rawName;

    const stdSection = document.getElementById('standard_aql_section');
    const crqsSection = document.getElementById('crqs_section');
    const systemTypeInput = document.php ? '' : document.getElementById('system_type');

    if (customerName.includes('UNILEVER')) {
        stdSection.style.display = 'none';
        crqsSection.style.display = 'block';
        document.getElementById('system_type').value = 'crqs';
    } else {
        stdSection.style.display = 'block';
        crqsSection.style.display = 'none';
        document.getElementById('system_type').value = 'standard';
    }
}
</script>
@endsection