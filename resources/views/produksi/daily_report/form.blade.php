@extends('layouts.staff-layout')

@section('title', 'Form Daily Report')

@section('konten')
<div style="padding: 30px; background-color: #f8fafc; min-height: 100vh;">
    
    <div style="margin-bottom: 25px;">
        <a href="{{ route('produksi.daily_report.index') }}" style="color: #64748b; text-decoration: none; font-size: 14px; font-weight: bold; margin-bottom: 10px; display: inline-block;">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar WO
        </a>
        <h2 style="font-size: 24px; font-weight: bold; color: #1e293b; margin: 0;">
            <i class="fas fa-clipboard-check" style="color: #f59e0b; margin-right: 8px;"></i> Check Sheet Daily Report
        </h2>
        <div style="font-size: 14px; color: #64748b; margin-top: 5px;">
            WO: <strong>{{ $wo->nomor_wo ?? '-' }}</strong> | Produk: <strong style="color: #0ea5e9;">{{ $wo->nama_material ?? 'Nama Produk' }}</strong>
        </div>
    </div>

    <!-- Nanti form action-nya kita arahkan ke route simpan -->
    <form action="#" method="POST" id="formDailyReport">
        @csrf
        
        <!-- BAGIAN 1: INFO PRODUKSI & TARGET -->
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; margin-bottom: 20px;">
            <h3 style="font-size: 16px; margin: 0 0 15px 0; color: #334155; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;"><i class="fas fa-info-circle text-blue-500"></i> 1. Informasi Shift & Mesin</h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div><label style="font-size: 12px; font-weight: bold; color: #64748b;">Tanggal</label>
                    <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px;"></div>
                
                <div><label style="font-size: 12px; font-weight: bold; color: #64748b;">Shift</label>
                    <select name="shift" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px;">
                        <option value="1">Shift 1</option><option value="2">Shift 2</option><option value="3">Shift 3</option>
                    </select></div>
                
                <div><label style="font-size: 12px; font-weight: bold; color: #64748b;">Nama Operator</label>
                    <input type="text" name="operator" required placeholder="Nama Operator" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px;"></div>
                
                <div><label style="font-size: 12px; font-weight: bold; color: #64748b;">Mesin & Line</label>
                    <input type="text" name="mesin" placeholder="Contoh: AISA Line 1" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px;"></div>
                
                <div><label style="font-size: 12px; font-weight: bold; color: #16a34a;">Output Actual (FG Bagus)</label>
                    <input type="number" name="output_actual" required min="0" placeholder="0" style="width: 100%; padding: 10px; border: 1px solid #22c55e; background: #f0fdf4; border-radius: 5px; font-weight:bold;"></div>
                
                <div><label style="font-size: 12px; font-weight: bold; color: #0ea5e9;">Target Output (Standard)</label>
                    <input type="number" name="output_standard" required min="0" placeholder="0" style="width: 100%; padding: 10px; border: 1px solid #3b82f6; background: #eff6ff; border-radius: 5px;"></div>
            </div>
        </div>

        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            
            <!-- BAGIAN 2: REJECT / CACAT -->
            <div style="flex: 1; min-width: 350px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; border-top: 4px solid #ef4444;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 15px;">
                    <h3 style="font-size: 16px; margin: 0; color: #991b1b;"><i class="fas fa-times-circle"></i> 2. Rincian Reject</h3>
                    <button type="button" onclick="tambahBarisReject()" style="background: #ef4444; color: white; border: none; padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer;">+ Tambah Reject</button>
                </div>
                
                <div id="wadahReject">
                    <!-- Baris Reject 1 -->
                    <div class="baris-reject" style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <select name="reject_id[]" style="flex: 2; padding: 8px; border: 1px solid #fca5a5; border-radius: 4px; font-size: 13px;">
                            <option value="">-- Pilih Jenis Cacat --</option>
                            @foreach($master_rejects as $mr)
                                <option value="{{ $mr->id }}">[{{ $mr->mesin }}] {{ $mr->nama_reject }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="reject_qty[]" placeholder="Qty" min="1" style="flex: 1; padding: 8px; border: 1px solid #fca5a5; border-radius: 4px; font-size: 13px;">
                        <button type="button" onclick="hapusBaris(this)" style="background: #f1f5f9; border: 1px solid #cbd5e1; padding: 5px 10px; border-radius: 4px; cursor: pointer; color: #ef4444;"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 3: DOWNTIME / MESIN MATI -->
            <div style="flex: 1; min-width: 350px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; border-top: 4px solid #f59e0b;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 15px;">
                    <h3 style="font-size: 16px; margin: 0; color: #b45309;"><i class="fas fa-power-off"></i> 3. Rincian Downtime</h3>
                    <button type="button" onclick="tambahBarisDowntime()" style="background: #f59e0b; color: white; border: none; padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer;">+ Tambah Downtime</button>
                </div>
                
                <div id="wadahDowntime">
                    <!-- Baris Downtime 1 -->
                    <div class="baris-downtime" style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <select name="downtime_id[]" style="flex: 2; padding: 8px; border: 1px solid #fcd34d; border-radius: 4px; font-size: 13px;">
                            <option value="">-- Pilih Alasan Mati --</option>
                            @foreach($master_downtimes as $md)
                                <option value="{{ $md->id }}">[{{ $md->mesin }}] {{ $md->nama_masalah }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="downtime_menit[]" placeholder="Menit" min="1" style="flex: 1; padding: 8px; border: 1px solid #fcd34d; border-radius: 4px; font-size: 13px;">
                        <button type="button" onclick="hapusBaris(this)" style="background: #f1f5f9; border: 1px solid #cbd5e1; padding: 5px 10px; border-radius: 4px; cursor: pointer; color: #ef4444;"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>

        </div>

        <div style="margin-top: 25px; text-align: right;">
            <button type="submit" style="background: #10b981; color: white; border: none; padding: 12px 25px; border-radius: 5px; font-size: 16px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);">
                <i class="fas fa-save" style="margin-right: 5px;"></i> Simpan Daily Report
            </button>
        </div>
    </form>
</div>

<!-- SCRIPT UNTUK MENAMBAH BARIS DINAMIS -->
<script>
    function hapusBaris(btn) {
        btn.parentElement.remove();
    }

    function tambahBarisReject() {
        const wadah = document.getElementById('wadahReject');
        const barisPertama = wadah.querySelector('.baris-reject');
        if(barisPertama) {
            const barisBaru = barisPertama.cloneNode(true);
            barisBaru.querySelector('select').value = "";
            barisBaru.querySelector('input').value = "";
            wadah.appendChild(barisBaru);
        }
    }

    function tambahBarisDowntime() {
        const wadah = document.getElementById('wadahDowntime');
        const barisPertama = wadah.querySelector('.baris-downtime');
        if(barisPertama) {
            const barisBaru = barisPertama.cloneNode(true);
            barisBaru.querySelector('select').value = "";
            barisBaru.querySelector('input').value = "";
            wadah.appendChild(barisBaru);
        }
    }
</script>
@endsection