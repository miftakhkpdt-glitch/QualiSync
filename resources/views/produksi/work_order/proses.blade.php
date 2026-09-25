@extends('layouts.staff-layout')

@section('title', 'Lapor Produksi (FG)')

@section('konten')
<div style="padding: 20px;">
    
    <!-- NOTIFIKASI -->
    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #bbf7d0;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #fecaca;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- HEADER WO -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="font-size: 22px; color: #1e293b; margin: 0;"><i class="fas fa-industry" style="color: #0ea5e9;"></i> Eksekusi Work Order</h2>
            <p style="color: #64748b; margin: 5px 0 0 0; font-size: 14px;">MM: {{ $wo->no_mm }} | <strong style="color: #0284c7;">{{ $wo->nama_material ?? 'Nama Produk' }}</strong></p>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 12px; color: #64748b;">Target Produksi:</div>
            <div style="font-size: 20px; font-weight: bold; color: #b45309;">{{ number_format($wo->qty_target) }} Pcs</div>
        </div>
    </div>

    <!-- FORM LAPOR HASIL FG -->
    @if($wo->status != 'Selesai')
        <div style="background: #fff; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); padding: 20px; border-top: 4px solid #10b981;">
            <h3 style="margin: 0 0 10px 0; color: #047857; font-size: 16px;"><i class="fas fa-box-open"></i> Lapor Hasil Produksi (Finish Good)</h3>
            
            <div style="background: #e0f2fe; border-left: 4px solid #0284c7; padding: 12px; border-radius: 4px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                <div style="font-size: 13px; color: #0369a1;">
                    <strong>Akumulasi FG Disetor Sejauh Ini:</strong> 
                    <span style="font-size: 16px; font-weight: bold; margin-left: 5px;">{{ number_format($wo->qty_good ?? 0) }}</span> Pcs
                </div>
                <div style="font-size: 13px; color: #0369a1;">
                    <strong>Target WO:</strong> <span style="font-weight: bold;">{{ number_format($wo->qty_target) }}</span> Pcs
                </div>
            </div>

            <form action="{{ route('produksi.wo.finish', $wo->id) }}" method="POST">
                @csrf
                
                <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px dashed #e2e8f0;">
                    <div style="flex: 1; min-width: 150px;">
                        <label style="display: block; font-size: 11px; font-weight: bold; color: #64748b; margin-bottom: 5px;">Shift FG *</label>
                        <select name="shift_fg" id="input_shift_fg" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 5px; box-sizing: border-box; background: #f8fafc; color: #334155;">
                            <option value="1">Shift 1</option><option value="2">Shift 2</option><option value="3">Shift 3</option>
                        </select>
                    </div>
                    <div style="flex: 1; min-width: 150px;">
                        <label style="display: block; font-size: 11px; font-weight: bold; color: #64748b; margin-bottom: 5px;">Operator FG *</label>
                        <input type="text" name="operator_fg" id="input_operator_fg" required placeholder="Nama Anda" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 5px; box-sizing: border-box; background: #f8fafc; color: #334155;">
                    </div>
                </div>

                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 150px;">
                        <label style="display: block; font-size: 12px; font-weight: bold; color: #166534; margin-bottom: 5px;">Qty FG (Shift Ini) *</label>
                        <input type="number" name="qty_good" required min="1" placeholder="0" style="width: 100%; padding: 10px; border: 1px solid #22c55e; border-radius: 5px; box-sizing: border-box; background: #f0fdf4;">
                    </div>
                    <div style="flex: 1; min-width: 150px;">
                        <label style="display: block; font-size: 12px; font-weight: bold; color: #991b1b; margin-bottom: 5px;">Qty Reject (Shift Ini) *</label>
                        <input type="number" name="qty_reject" id="inputReject" required min="0" value="0" style="width: 100%; padding: 10px; border: 1px solid #ef4444; border-radius: 5px; box-sizing: border-box; background: #fef2f2;" oninput="cekReject()">
                    </div>
                    <div id="boxJenisReject" style="flex: 1; min-width: 200px; display: none;">
                        <label style="display: block; font-size: 12px; font-weight: bold; color: #991b1b; margin-bottom: 5px;">Kategori Reject *</label>
                        <select name="jenis_reject" id="selectReject" style="width: 100%; padding: 10px; border: 1px solid #ef4444; border-radius: 5px; box-sizing: border-box;">
                            <option value="">-- Pilih Kendala --</option>
                            <option value="Bocor">Bocor</option>
                            <option value="Sablon Luntur/Meleset">Sablon Luntur/Meleset</option>
                            <option value="Baret">Baret / Gores</option>
                            <option value="Tutup Pecah">Tutup Pecah</option>
                            <option value="Sealing Miring">Sealing Miring</option>
                            <option value="Lainnya">Lainnya...</option>
                        </select>
                    </div>
                </div>

                <div style="margin-top: 25px; text-align: right; border-top: 1px solid #f1f5f9; padding-top: 15px; display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="submit" name="action_type" value="parsial" onclick="return confirm('Simpan hasil shift ini dan Lanjutkan WO untuk shift berikutnya?')" 
                            style="background: #3b82f6; color: white; border: none; padding: 10px 15px; border-radius: 5px; font-weight: bold; cursor: pointer;">
                        <i class="fas fa-save"></i> Simpan Hasil Shift (Parsial)
                    </button>
                    <button type="submit" name="action_type" value="closing" onclick="return confirm('PERINGATAN: Ini akan MENUTUP Work Order. Pastikan target sudah terpenuhi. Lanjutkan?')" 
                            style="background: #10b981; color: white; border: none; padding: 10px 20px; border-radius: 5px; font-weight: bold; cursor: pointer;">
                        <i class="fas fa-flag-checkered"></i> Simpan & Closing WO
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>

<script>
    function cekReject() {
        var qtyReject = document.getElementById('inputReject').value;
        var boxJenis = document.getElementById('boxJenisReject');
        var selectReject = document.getElementById('selectReject');
        if (qtyReject > 0) { boxJenis.style.display = 'block'; selectReject.setAttribute('required', 'required'); }
        else { boxJenis.style.display = 'none'; selectReject.removeAttribute('required'); }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Tarik nama dari memory traceability
        const fgOperatorInput = document.getElementById('input_operator_fg');
        if(fgOperatorInput) {
            let savedOp = localStorage.getItem('trace_operator');
            if(savedOp) fgOperatorInput.value = savedOp;
        }
    });
</script>
@endsection