@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 1400px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px;">
        <h3 style="margin: 0; color: #334155;"><i class="fas fa-truck-loading"></i> Riwayat Mutasi Keluar (Quality Dept)</h3>
        <button onclick="bukaModalMutasi()" style="background: #10b981; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 13px;">
            <i class="fas fa-plus"></i> Buat Mutasi Baru
        </button>
    </div>

    <!-- TABEL DATA -->
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #1e293b; color: white;">
                    <th style="padding: 12px 10px;">Tanggal & Jam</th>
                    <th style="padding: 12px 10px;">Tujuan Dept</th>
                    <th style="padding: 12px 10px;">No. MM, Item & Batch</th>
                    <th style="padding: 12px 10px;">Qty Mutasi</th>
                    <th style="padding: 12px 10px;">Dibuat Oleh</th>
                    <th style="padding: 12px 10px; text-align: center;">Status Approval</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat_mutasi as $mutasi)
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    
                    <!-- FORMAT TANGGAL & JAM PRESISI -->
                    <td style="padding: 10px;">
                        <div style="font-weight: bold; color: #1e293b;">
                            {{ \Carbon\Carbon::parse($mutasi->created_at)->format('d M Y') }}
                        </div>
                        <div style="font-size: 11px; color: #0369a1; margin-bottom: 4px;">
                            <i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($mutasi->created_at)->format('H:i:s') }}
                        </div>
                        <span style="background: #e2e8f0; color: #475569; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: bold;">
                            {{ $mutasi->shift }}
                        </span>
                    </td>

                    <td style="padding: 10px;"><span style="background: #fef3c7; color: #b45309; padding: 3px 8px; border-radius: 4px; font-weight: bold;">{{ $mutasi->ke_dept }}</span></td>
                    <td style="padding: 10px;">
                        <b>{{ $mutasi->mm }}</b><br>
                        <small>{{ $mutasi->item_name }}</small><br>
                        <span style="color: #0ea5e9; font-size: 11px; font-weight: bold;">Batch: {{ $mutasi->batch ?? '-' }}</span>
                    </td>
                    <td style="padding: 10px; font-weight: bold; color: #b91c1c;">- {{ number_format($mutasi->qty, 2) }} {{ $mutasi->uom }}</td>
                    <td style="padding: 10px;">{{ $mutasi->pembuat->name ?? '-' }}</td>
                    <td style="padding: 10px; text-align: center;">
                        @if($mutasi->status_approval == 'Pending')
                            <span style="background: #fef08a; color: #854d0e; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;">Menunggu {{ $mutasi->ke_dept }}</span>
                        @elseif($mutasi->status_approval == 'Approved' || $mutasi->status_approval == 'Selesai')
                            <span style="background: #dcfce7; color: #15803d; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;">Approved</span>
                        @else
                            <span style="background: #fee2e2; color: #b91c1c; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;">{{ $mutasi->status_approval }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align: center; padding: 30px; color: #64748b;">Belum ada riwayat mutasi keluar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL FORM INPUT -->
<div id="modalMutasi" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center;">
    <div style="background-color: #fff; width: 500px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); overflow: hidden;">
        <div style="background-color: #1e293b; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px;"><i class="fas fa-truck-loading"></i> Form Mutasi Keluar (Quality)</h3>
            <span onclick="tutupModalMutasi()" style="cursor: pointer; font-size: 20px; font-weight: bold;">&times;</span>
        </div>
        
        <!-- ROUTE SUDAH DIARAHKAN KE QUALITY MUTASI STORE -->
        <form action="{{ route('quality.mutasi_keluar.store') }}" method="POST" style="padding: 20px;">
            @csrf
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">Tanggal</label>
                    <input type="date" name="tanggal" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">Shift</label>
                    <select name="shift" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                        <option value="Shift 1">Shift 1 (Pagi)</option><option value="Shift 2">Shift 2 (Sore)</option><option value="Shift 3">Shift 3 (Malam)</option><option value="Non Shift">Non Shift</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="font-size: 12px; font-weight: bold;">Tujuan Departemen</label>
                <select name="ke_dept" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                    <!-- DISESUAIKAN UNTUK QUALITY -->
                    <option value="Warehouse">Warehouse (Release / Retur)</option>
                    <option value="Produksi">Produksi (Rework)</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">No. MM</label>
                    <input type="text" name="mm" required placeholder="Contoh: 132015" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;" onkeyup="cariNamaItem(this.value)">
                </div>
                <div style="flex: 2;">
                    <label style="font-size: 12px; font-weight: bold;">Nama Item (Otomatis)</label>
                    <input type="text" id="input_item_name" name="item_name" placeholder="Otomatis..." readonly style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px; background-color: #f1f5f9;">
                </div>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">No. Batch</label>
                    <input type="text" name="batch" placeholder="Opsional" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">Qty</label>
                    <input type="number" step="0.01" name="qty" required placeholder="0.00" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">Satuan</label>
                    <input type="text" name="uom" value="Pcs" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                </div>
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <button type="button" onclick="tutupModalMutasi()" style="background: #94a3b8; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; margin-right: 5px;">Batal</button>
                <button type="submit" style="background: #10b981; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">Kirim</button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalMutasi() { document.getElementById('modalMutasi').style.display = 'flex'; }
    function tutupModalMutasi() { document.getElementById('modalMutasi').style.display = 'none'; }
    
    function cariNamaItem(mm) {
        let inputNama = document.getElementById('input_item_name');
        if (mm.trim() === '') { inputNama.value = ''; return; }
        
        // ROUTE AJAX SUDAH DIARAHKAN KE QUALITY GET ITEM
        fetch(`{{ route('quality.mutasi_keluar.get_item') }}?mm=${mm}`)
            .then(res => res.json())
            .then(data => { inputNama.value = data.success ? data.item_name : ''; })
            .catch(err => console.error(err));
    }
</script>
@endsection