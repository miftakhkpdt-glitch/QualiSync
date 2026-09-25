@extends('layouts.staff-layout')

@section('konten')
<div style="padding: 20px; max-width: 1000px; margin: 0 auto;">
    
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 22px; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-sliders-h" style="color: #2563eb;"></i> Master AQL Standards
        </h1>
        <p style="font-size: 13px; color: #64748b; margin: 4px 0 0 0;">Kelola parameter standar AQL dan CRQS spesifik per Customer.</p>
    </div>

    @if(session('success'))
        <div style="padding: 12px 16px; background-color: #dcfce7; border: 1px solid #86efac; color: #166534; border-radius: 8px; font-size: 13px; margin-bottom: 20px;">
            <i class="fas fa-check-circle" style="margin-right: 6px;"></i> {{ session('success') }}
        </div>
    @endif

    <div style="display: flex; flex-wrap: wrap; gap: 24px; align-items: flex-start;">
        
        <!-- FORM INPUT PARAMETER CUSTOMER -->
        <div style="flex: 1 1 360px; max-width: 440px;">
            <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden;">
                
                <div style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9;">
                    <h2 style="font-size: 15px; font-weight: 600; color: #1e293b; margin: 0;">
                        <i class="fas fa-plus-circle" style="color: #2563eb;"></i> Tambah Standar Customer
                    </h2>
                </div>

                <form action="{{ url('/master-aql') }}" method="POST" style="padding: 20px;">
                    @csrf
                    
                    <!-- Pilih Customer (Value berupa customer_id, data-name menyimpan nama) -->
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px;">PILIH CUSTOMER *</label>
                        <select id="customer_select" name="customer_id" onchange="toggleAqlFields()" required style="width: 100%; padding: 10px 12px; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
                            <option value="">-- Pilih Customer --</option>
                            @foreach($customers as $cust)
                                @php 
                                    $custId = $cust->id;
                                    $custName = $cust->nama_customer ?? $cust->customer_name; 
                                @endphp
                                <option value="{{ $custId }}" data-name="{{ $custName }}">{{ $custName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Input Hidden untuk Customer Name & System Type -->
                    <input type="hidden" id="customer_name" name="customer_name">
                    <input type="hidden" id="system_type" name="system_type" value="standard">

                    <!-- SEKSI AQL STANDARD -->
                    <div id="standard_aql_section" style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 20px;">
                        <span style="display: block; font-size: 12px; font-weight: 700; color: #1e293b; margin-bottom: 12px;">Parameter Standard AQL</span>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <div>
                                <label style="font-size: 11px; font-weight: 600; color: #64748b; display: block; margin-bottom: 4px;">Zero Defect</label>
                                <input type="text" name="aql_zero_defect" placeholder="Contoh: 0" style="width: 100%; padding: 8px; font-size: 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 600; color: #64748b; display: block; margin-bottom: 4px;">Critical</label>
                                <input type="text" name="aql_critical" placeholder="Contoh: 0.1 / 0.65" style="width: 100%; padding: 8px; font-size: 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 600; color: #64748b; display: block; margin-bottom: 4px;">Major</label>
                                <input type="text" name="aql_major" placeholder="Contoh: 1.0 / 2.5" style="width: 100%; padding: 8px; font-size: 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 600; color: #64748b; display: block; margin-bottom: 4px;">Minor</label>
                                <input type="text" name="aql_minor" placeholder="Contoh: 6.5 / 4.0" style="width: 100%; padding: 8px; font-size: 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                            </div>
                        </div>
                    </div>

                    <!-- SEKSI CRQS (UNILEVER) -->
                    <div id="crqs_section" style="display: none; background: #fff7ed; padding: 16px; border-radius: 8px; border: 1px solid #ffedd5; margin-bottom: 20px;">
                        <span style="display: block; font-size: 12px; font-weight: 700; color: #c2410c; margin-bottom: 12px;">Parameter CRQS (Unilever)</span>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <div>
                                <label style="font-size: 11px; font-weight: 600; color: #9a3412; display: block; margin-bottom: 4px;">Amber (%)</label>
                                <input type="text" name="crqs_amber" placeholder="Contoh: 3%" style="width: 100%; padding: 8px; font-size: 12px; border: 1px solid #fdba74; border-radius: 6px; box-sizing: border-box;">
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 600; color: #9a3412; display: block; margin-bottom: 4px;">Red (%)</label>
                                <input type="text" name="crqs_red" placeholder="Contoh: 0%" style="width: 100%; padding: 8px; font-size: 12px; border: 1px solid #fdba74; border-radius: 6px; box-sizing: border-box;">
                            </div>
                        </div>
                    </div>

                    <button type="submit" style="width: 100%; padding: 11px; background: #2563eb; color: #ffffff; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                        <i class="fas fa-save"></i> Simpan Parameter
                    </button>
                </form>
            </div>
        </div>

        <!-- TABEL REKAP PARAMETER -->
        <div style="flex: 2 1 480px;">
            <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden;">
                <div style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9;">
                    <h2 style="font-size: 15px; font-weight: 600; color: #1e293b; margin: 0;">Daftar Standar Customer</h2>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 11px; text-transform: uppercase; text-align: left;">
                                <th style="padding: 12px 16px;">Nama Customer</th>
                                <th style="padding: 12px 16px;">Tipe Sistem</th>
                                <th style="padding: 12px 16px;">Detail Parameter</th>
                                <th style="padding: 12px 16px; text-align: center; width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($aqlStandards as $row)
                            <tr style="border-bottom: 1px solid #f1f5f9; color: #334155;">
                                <td style="padding: 12px 16px; font-weight: 700; color: #1e293b;">{{ $row->customer_name }}</td>
                                <td style="padding: 12px 16px;">
                                    @if(($row->tipe_standar ?? $row->system_type) == 'crqs')
                                        <span style="background: #ffedd5; color: #c2410c; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600;">CRQS</span>
                                    @else
                                        <span style="background: #e0f2fe; color: #0369a1; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600;">AQL Standard</span>
                                    @endif
                                </td>
                                <td style="padding: 12px 16px;">
                                    @if(($row->tipe_standar ?? $row->system_type) == 'crqs')
                                        <div style="font-size: 12px; color: #9a3412;">
                                            <b>Amber:</b> {{ $row->crqs_amber ?? '-' }} &nbsp;|&nbsp; <b>Red:</b> {{ $row->crqs_red ?? '-' }}
                                        </div>
                                    @else
                                        <div style="font-size: 12px; color: #334155;">
                                            @if(!is_null($row->aql_zero_defect) && $row->aql_zero_defect !== '') 
                                                <b>Zero Defect:</b> {{ $row->aql_zero_defect }} &nbsp;|&nbsp; 
                                            @endif
                                            <b>Critical:</b> {{ $row->aql_critical ?? '-' }} &nbsp;|&nbsp; 
                                            <b>Major:</b> {{ $row->aql_major ?? '-' }} &nbsp;|&nbsp; 
                                            <b>Minor:</b> {{ $row->aql_minor ?? '-' }}
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 12px 16px; text-align: center;">
                                    <div style="display: flex; justify-content: center; gap: 6px;">
                                        <!-- Tombol Edit -->
                                        <a href="{{ url('/master-aql/' . $row->id . '/edit') }}" 
                                           style="background: #e0f2fe; color: #0284c7; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 12px;" 
                                           title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Tombol Hapus -->
                                        <form action="{{ url('/master-aql/' . $row->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    style="background: #fee2e2; color: #dc2626; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;" 
                                                    title="Hapus Data">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" style="padding: 30px; text-align: center; color: #94a3b8;">Belum ada data standar customer.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

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
    const systemTypeInput = document.getElementById('system_type');

    if (customerName.includes('UNILEVER')) {
        stdSection.style.display = 'none';
        crqsSection.style.display = 'block';
        systemTypeInput.value = 'crqs';
    } else {
        stdSection.style.display = 'block';
        crqsSection.style.display = 'none';
        systemTypeInput.value = 'standard';
    }
}
</script>
@endsection