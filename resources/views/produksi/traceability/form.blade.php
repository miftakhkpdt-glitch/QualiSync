@extends('layouts.staff-layout')

@section('title', 'Traceability Control')

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

    <!-- Tombol Kembali & Header -->
    <div style="margin-bottom: 20px;">
        <a href="{{ route('produksi.traceability.index') }}" style="color: #3b82f6; text-decoration: none; font-size: 14px; margin-bottom: 15px; display: inline-block; font-weight: bold;">
            <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Kembali ke Daftar WO
        </a>
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="font-size: 22px; color: #1e293b; margin: 0;"><i class="fas fa-qrcode" style="color: #3b82f6;"></i> Traceability Control</h2>
                <p style="color: #64748b; margin: 5px 0 0 0; font-size: 14px;">MM: {{ $wo->no_mm }} | <strong style="color: #0284c7;">{{ $wo->nama_material ?? 'Nama Produk' }}</strong></p>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 12px; color: #64748b;">Target Produksi:</div>
                <div style="font-size: 20px; font-weight: bold; color: #b45309;">{{ number_format($wo->qty_target) }} Pcs</div>
            </div>
        </div>
    </div>

    <!-- TABEL TRACEABILITY -->
    <div style="background: #fff; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 30px; padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
            <h3 style="margin: 0; color: #334155; font-size: 16px;"><i class="fas fa-clipboard-list"></i> Log Pemakaian Material</h3>
            
            @if($wo->status != 'Selesai')
                <button type="button" onclick="bukaModalTraceability()" 
                        style="background: #3b82f6; color: white; border: none; padding: 8px 15px; border-radius: 5px; font-weight: bold; cursor: pointer; font-size: 12px; box-shadow: 0 2px 4px rgba(59, 130, 246, 0.4);">
                    <i class="fas fa-plus"></i> Tambah Log Material
                </button>
            @else
                <span style="background: #e2e8f0; color: #64748b; padding: 8px 15px; border-radius: 5px; font-weight: bold; font-size: 12px;">
                    <i class="fas fa-lock"></i> WO Telah Selesai (Read-Only)
                </span>
            @endif
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: center; font-size: 12px; border: 1px solid #cbd5e1;">
                <thead style="background: #f8fafc; color: #0f172a;">
                    <tr>
                        <th rowspan="2" style="border: 1px solid #cbd5e1; padding: 10px;">Date</th>
                        <th rowspan="2" style="border: 1px solid #cbd5e1; padding: 10px;">Shift</th>
                        <th rowspan="2" style="border: 1px solid #cbd5e1; padding: 10px;">Group</th>
                        <th rowspan="2" style="border: 1px solid #cbd5e1; padding: 10px;">Batch<br>Num.</th>
                        
                        <th colspan="4" style="border: 1px solid #cbd5e1; padding: 8px; background: #eff6ff;">Printed Web</th>
                        <th colspan="4" style="border: 1px solid #cbd5e1; padding: 8px; background: #f0fdf4;">CAP</th>
                        
                        <th rowspan="2" style="border: 1px solid #cbd5e1; padding: 10px;">Lot Num. /<br>Batch (master)</th>
                        <th rowspan="2" style="border: 1px solid #cbd5e1; padding: 10px;">Lot Num. /<br>Batch (HDPE)</th>
                        <th rowspan="2" style="border: 1px solid #cbd5e1; padding: 10px;">Operator</th>
                        <th rowspan="2" style="border: 1px solid #cbd5e1; padding: 10px;">Checked<br>By QA</th>
                        <th rowspan="2" style="border: 1px solid #cbd5e1; padding: 10px;">Aksi</th>
                    </tr>
                    <tr>
                        <th style="border: 1px solid #cbd5e1; padding: 8px; font-weight: normal; background: #eff6ff;">Incoming Date</th>
                        <th style="border: 1px solid #cbd5e1; padding: 8px; font-weight: normal; background: #eff6ff;">Item Name</th>
                        <th style="border: 1px solid #cbd5e1; padding: 8px; font-weight: normal; background: #eff6ff;">Lot Num.</th>
                        <th style="border: 1px solid #cbd5e1; padding: 8px; font-weight: bold; background: #eff6ff; color:#0284c7;">Qty Pakai</th>
                        
                        <th style="border: 1px solid #cbd5e1; padding: 8px; font-weight: normal; background: #f0fdf4;">Incoming Date</th>
                        <th style="border: 1px solid #cbd5e1; padding: 8px; font-weight: normal; background: #f0fdf4;">Lot Num.</th>
                        <th style="border: 1px solid #cbd5e1; padding: 8px; font-weight: normal; background: #f0fdf4;">Color</th>
                        <th style="border: 1px solid #cbd5e1; padding: 8px; font-weight: bold; background: #f0fdf4; color:#166534;">Qty Pakai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="border: 1px solid #cbd5e1; padding: 6px;">{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 6px;">{{ $log->shift }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 6px;">{{ $log->grup ?? '-' }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 6px;">{{ $log->batch_num ?? '-' }}</td>
                        
                        <td style="border: 1px solid #cbd5e1; padding: 6px; background: #eff6ff;">{{ $log->web_incoming_date ? \Carbon\Carbon::parse($log->web_incoming_date)->format('d/m/Y') : '-' }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 6px; background: #eff6ff;">{{ $wo->nama_material ?? '-' }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 6px; background: #eff6ff;">{{ $log->web_lot_num ?? '-' }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 6px; background: #eff6ff; font-weight:bold; color: #0284c7;">{{ number_format($log->web_qty) }}</td>
                        
                        <td style="border: 1px solid #cbd5e1; padding: 6px; background: #f0fdf4;">{{ $log->cap_incoming_date ? \Carbon\Carbon::parse($log->cap_incoming_date)->format('d/m/Y') : '-' }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 6px; background: #f0fdf4;">{{ $log->cap_lot_num ?? '-' }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 6px; background: #f0fdf4;">{{ $log->cap_color ?? '-' }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 6px; background: #f0fdf4; font-weight:bold; color: #166534;">{{ number_format($log->cap_qty) }}</td>
                        
                        <td style="border: 1px solid #cbd5e1; padding: 6px;">{{ $log->lot_master_batch ?? '-' }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 6px;">{{ $log->lot_hdpe ?? '-' }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 6px;">{{ $log->operator }}</td>
                        
                        <td style="border: 1px solid #cbd5e1; padding: 6px;">
                            @if($log->qa_status == 'Pending')
                                <span style="background: #fef08a; color: #854d0e; padding: 3px 6px; border-radius: 12px; font-size: 10px; font-weight:bold;">Pending</span>
                            @else
                                <span style="background: #bbf7d0; color: #166534; padding: 3px 6px; border-radius: 12px; font-size: 10px; font-weight:bold;"><i class="fas fa-check"></i> {{ $log->qa_checked_by }}</span>
                            @endif
                        </td>
                        
                        <!-- TOMBOL EDIT -->
                        <td style="border: 1px solid #cbd5e1; padding: 6px;">
                            @if($wo->status != 'Selesai')
                                <button type="button" onclick="document.getElementById('modalEdit-{{ $log->id }}').style.display='block'" 
                                        style="background: #f59e0b; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 11px; font-weight: bold;">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="17" style="text-align: center; padding: 30px; color: #94a3b8; font-style: italic; border: 1px solid #cbd5e1;">
                            Belum ada log traceability yang diinput.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL POP-UP TAMBAH LOG -->
<!-- ========================================== -->
<div id="modalTraceability" style="display: none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); overflow-y: auto;">
    <div style="background-color: #fff; margin: 5vh auto; padding: 25px; border-radius: 8px; width: 85%; max-width: 900px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #1e293b; font-size: 18px;"><i class="fas fa-edit" style="color: #3b82f6;"></i> Input Log Material Baru</h3>
            <span onclick="tutupModalTraceability()" style="color: #94a3b8; font-size: 28px; font-weight: bold; cursor: pointer; line-height: 1;">&times;</span>
        </div>

        <form action="{{ route('produksi.wo.store_traceability', $wo->id) }}" method="POST">
            @csrf
            <div style="background: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <h4 style="margin: 0; color: #475569; font-size: 14px;">1. Identitas Pekerjaan</h4>
                    <span style="font-size: 10px; color: #10b981; font-weight: bold;"><i class="fas fa-bolt"></i> Auto-Saved</span>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                    <div><label style="font-size: 11px; font-weight:bold;">Date</label><input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;"></div>
                    <div><label style="font-size: 11px; font-weight:bold;">Shift</label>
                        <select name="shift" required onchange="simpanMemori(this)" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                            <option value="1">Shift 1</option><option value="2">Shift 2</option><option value="3">Shift 3</option>
                        </select>
                    </div>
                    <div><label style="font-size: 11px; font-weight:bold;">Group</label><input type="text" name="grup" oninput="simpanMemori(this)" placeholder="A/B/C/D" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;"></div>
                    <div><label style="font-size: 11px; font-weight:bold;">Batch Num (FG)</label><input type="text" name="batch_num" oninput="simpanMemori(this)" placeholder="No. Batch" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;"></div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
                <div style="background: #eff6ff; padding: 15px; border-radius: 6px; border: 1px solid #bfdbfe;">
                    <h4 style="margin: 0 0 10px 0; color: #1e3a8a; font-size: 14px;">2. Printed Web</h4>
                    <div style="margin-bottom: 10px;"><label style="font-size: 11px;">Incoming Date</label><input type="date" name="web_incoming_date" style="width: 100%; padding: 8px; border: 1px solid #93c5fd; border-radius: 4px;"></div>
                    <div style="margin-bottom: 10px;"><label style="font-size: 11px;">Item Name</label><input type="text" name="web_item_name" value="{{ $wo->nama_material ?? '' }}" readonly style="width: 100%; padding: 8px; border: 1px solid #93c5fd; border-radius: 4px; background: #e2e8f0; cursor: not-allowed; color: #475569;"></div>
                    <div style="margin-bottom: 10px;"><label style="font-size: 11px;">Lot Num</label><input type="text" name="web_lot_num" placeholder="Lot No." style="width: 100%; padding: 8px; border: 1px solid #93c5fd; border-radius: 4px;"></div>
                    <div><label style="font-size: 11px; font-weight:bold; color: #0284c7;">Qty Pakai *</label><input type="number" name="web_qty" value="0" required style="width: 100%; padding: 8px; border: 1px solid #3b82f6; border-radius: 4px; background: #fff;"></div>
                </div>

                <div style="background: #f0fdf4; padding: 15px; border-radius: 6px; border: 1px solid #bbf7d0;">
                    <h4 style="margin: 0 0 10px 0; color: #14532d; font-size: 14px;">3. CAP (Tutup)</h4>
                    <div style="margin-bottom: 10px;"><label style="font-size: 11px;">Incoming Date</label><input type="date" name="cap_incoming_date" style="width: 100%; padding: 8px; border: 1px solid #86efac; border-radius: 4px;"></div>
                    <div style="margin-bottom: 10px;"><label style="font-size: 11px;">Lot Num</label><input type="text" name="cap_lot_num" placeholder="Lot No." style="width: 100%; padding: 8px; border: 1px solid #86efac; border-radius: 4px;"></div>
                    <div style="margin-bottom: 10px;"><label style="font-size: 11px;">Color</label><input type="text" name="cap_color" placeholder="Warna" style="width: 100%; padding: 8px; border: 1px solid #86efac; border-radius: 4px;"></div>
                    <div><label style="font-size: 11px; font-weight:bold; color: #166534;">Qty Pakai *</label><input type="number" name="cap_qty" value="0" required style="width: 100%; padding: 8px; border: 1px solid #22c55e; border-radius: 4px; background: #fff;"></div>
                </div>
            </div>

            <div style="background: #fefce8; padding: 15px; border-radius: 6px; border: 1px solid #fef08a; margin-top: 15px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <h4 style="margin: 0; color: #854d0e; font-size: 14px;">4. Resin & Verifikasi Operator</h4>
                    <span style="font-size: 10px; color: #10b981; font-weight: bold;"><i class="fas fa-bolt"></i> Auto-Saved</span>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div><label style="font-size: 11px; font-weight:bold;">Lot Num (Master Batch)</label><input type="text" name="lot_master_batch" oninput="simpanMemori(this)" placeholder="Manual Input" style="width: 100%; padding: 8px; border: 1px solid #fde047; border-radius: 4px;"></div>
                    <div><label style="font-size: 11px; font-weight:bold;">Lot Num (HDPE)</label><input type="text" name="lot_hdpe" oninput="simpanMemori(this)" placeholder="Manual Input" style="width: 100%; padding: 8px; border: 1px solid #fde047; border-radius: 4px;"></div>
                    <div><label style="font-size: 11px; font-weight:bold; color:#991b1b;">Nama Operator *</label><input type="text" name="operator" oninput="simpanMemori(this)" required placeholder="Nama Anda" style="width: 100%; padding: 8px; border: 1px solid #fca5a5; border-radius: 4px;"></div>
                </div>
            </div>

            <div style="margin-top: 20px; text-align: right; border-top: 1px solid #e2e8f0; padding-top: 15px;">
                <button type="button" onclick="tutupModalTraceability()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 10px 15px; border-radius: 5px; font-weight: bold; cursor: pointer; margin-right: 10px;">Batal</button>
                <button type="submit" style="background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 5px; font-weight: bold; cursor: pointer;"><i class="fas fa-save"></i> Simpan Log</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL POP-UP EDIT -->
<!-- ========================================== -->
@foreach($logs as $log)
    @if($wo->status != 'Selesai')
    <div id="modalEdit-{{ $log->id }}" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); overflow-y: auto;">
        <div style="background-color: #fff; margin: 5vh auto; padding: 25px; border-radius: 8px; width: 85%; max-width: 900px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 20px;">
                <h3 style="margin: 0; color: #b45309; font-size: 18px;"><i class="fas fa-edit"></i> Edit Log Material</h3>
                <span onclick="document.getElementById('modalEdit-{{ $log->id }}').style.display='none'" style="color: #94a3b8; font-size: 28px; font-weight: bold; cursor: pointer; line-height: 1;">&times;</span>
            </div>

            <form action="{{ route('produksi.wo.update_traceability', ['id' => $wo->id, 'log_id' => $log->id]) }}" method="POST">
                @csrf
                <div style="background: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 15px;">
                    <h4 style="margin: 0 0 10px 0; color: #475569; font-size: 14px;">1. Identitas Pekerjaan</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                        <div><label style="font-size: 11px; font-weight:bold;">Date</label><input type="date" name="tanggal" required value="{{ $log->tanggal }}" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;"></div>
                        <div><label style="font-size: 11px; font-weight:bold;">Shift</label>
                            <select name="shift" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                                <option value="1" {{ $log->shift == 1 ? 'selected' : '' }}>Shift 1</option>
                                <option value="2" {{ $log->shift == 2 ? 'selected' : '' }}>Shift 2</option>
                                <option value="3" {{ $log->shift == 3 ? 'selected' : '' }}>Shift 3</option>
                            </select>
                        </div>
                        <div><label style="font-size: 11px; font-weight:bold;">Group</label><input type="text" name="grup" value="{{ $log->grup }}" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;"></div>
                        <div><label style="font-size: 11px; font-weight:bold;">Batch Num (FG)</label><input type="text" name="batch_num" value="{{ $log->batch_num }}" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;"></div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
                    <div style="background: #eff6ff; padding: 15px; border-radius: 6px; border: 1px solid #bfdbfe;">
                        <h4 style="margin: 0 0 10px 0; color: #1e3a8a; font-size: 14px;">2. Printed Web</h4>
                        <div style="margin-bottom: 10px;"><label style="font-size: 11px;">Incoming Date</label><input type="date" name="web_incoming_date" value="{{ $log->web_incoming_date }}" style="width: 100%; padding: 8px; border: 1px solid #93c5fd; border-radius: 4px;"></div>
                        <div style="margin-bottom: 10px;"><label style="font-size: 11px;">Item Name</label><input type="text" value="{{ $wo->nama_material ?? '' }}" readonly style="width: 100%; padding: 8px; border: 1px solid #93c5fd; border-radius: 4px; background: #e2e8f0; cursor: not-allowed; color: #475569;"></div>
                        <div style="margin-bottom: 10px;"><label style="font-size: 11px;">Lot Num</label><input type="text" name="web_lot_num" value="{{ $log->web_lot_num }}" style="width: 100%; padding: 8px; border: 1px solid #93c5fd; border-radius: 4px;"></div>
                        <div><label style="font-size: 11px; font-weight:bold; color: #0284c7;">Qty Pakai *</label><input type="number" name="web_qty" value="{{ $log->web_qty }}" required style="width: 100%; padding: 8px; border: 1px solid #3b82f6; border-radius: 4px; background: #fff;"></div>
                    </div>
                    <div style="background: #f0fdf4; padding: 15px; border-radius: 6px; border: 1px solid #bbf7d0;">
                        <h4 style="margin: 0 0 10px 0; color: #14532d; font-size: 14px;">3. CAP (Tutup)</h4>
                        <div style="margin-bottom: 10px;"><label style="font-size: 11px;">Incoming Date</label><input type="date" name="cap_incoming_date" value="{{ $log->cap_incoming_date }}" style="width: 100%; padding: 8px; border: 1px solid #86efac; border-radius: 4px;"></div>
                        <div style="margin-bottom: 10px;"><label style="font-size: 11px;">Lot Num</label><input type="text" name="cap_lot_num" value="{{ $log->cap_lot_num }}" style="width: 100%; padding: 8px; border: 1px solid #86efac; border-radius: 4px;"></div>
                        <div style="margin-bottom: 10px;"><label style="font-size: 11px;">Color</label><input type="text" name="cap_color" value="{{ $log->cap_color }}" style="width: 100%; padding: 8px; border: 1px solid #86efac; border-radius: 4px;"></div>
                        <div><label style="font-size: 11px; font-weight:bold; color: #166534;">Qty Pakai *</label><input type="number" name="cap_qty" value="{{ $log->cap_qty }}" required style="width: 100%; padding: 8px; border: 1px solid #22c55e; border-radius: 4px; background: #fff;"></div>
                    </div>
                </div>

                <div style="background: #fefce8; padding: 15px; border-radius: 6px; border: 1px solid #fef08a; margin-top: 15px;">
                    <h4 style="margin: 0 0 10px 0; color: #854d0e; font-size: 14px;">4. Resin & Verifikasi Operator</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <div><label style="font-size: 11px; font-weight:bold;">Lot Num (Master Batch)</label><input type="text" name="lot_master_batch" value="{{ $log->lot_master_batch }}" style="width: 100%; padding: 8px; border: 1px solid #fde047; border-radius: 4px;"></div>
                        <div><label style="font-size: 11px; font-weight:bold;">Lot Num (HDPE)</label><input type="text" name="lot_hdpe" value="{{ $log->lot_hdpe }}" style="width: 100%; padding: 8px; border: 1px solid #fde047; border-radius: 4px;"></div>
                        <div><label style="font-size: 11px; font-weight:bold; color:#991b1b;">Nama Operator *</label><input type="text" name="operator" value="{{ $log->operator }}" required style="width: 100%; padding: 8px; border: 1px solid #fca5a5; border-radius: 4px;"></div>
                    </div>
                </div>

                <div style="margin-top: 20px; text-align: right; border-top: 1px solid #e2e8f0; padding-top: 15px;">
                    <button type="button" onclick="document.getElementById('modalEdit-{{ $log->id }}').style.display='none'" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 10px 15px; border-radius: 5px; font-weight: bold; cursor: pointer; margin-right: 10px;">Batal</button>
                    <button type="submit" style="background: #f59e0b; color: white; border: none; padding: 10px 20px; border-radius: 5px; font-weight: bold; cursor: pointer;"><i class="fas fa-save"></i> Perbarui Log</button>
                </div>
            </form>
        </div>
    </div>
    @endif
@endforeach

<script>
    function bukaModalTraceability() { document.getElementById('modalTraceability').style.display = 'block'; }
    function tutupModalTraceability() { document.getElementById('modalTraceability').style.display = 'none'; }
    
    window.onclick = function(event) {
        if (event.target == document.getElementById('modalTraceability')) { document.getElementById('modalTraceability').style.display = "none"; }
        @foreach($logs as $log)
        if (event.target == document.getElementById('modalEdit-{{ $log->id }}')) { document.getElementById('modalEdit-{{ $log->id }}').style.display = "none"; }
        @endforeach
    }
    
    function simpanMemori(elemen) { localStorage.setItem('trace_' + elemen.name, elemen.value); }
    
    document.addEventListener('DOMContentLoaded', function() {
        const fieldsToRemember = ['shift', 'grup', 'batch_num', 'lot_master_batch', 'lot_hdpe', 'operator'];
        fieldsToRemember.forEach(function(fieldName) {
            let savedValue = localStorage.getItem('trace_' + fieldName);
            if (savedValue !== null) {
                let inputElement = document.querySelector('#modalTraceability [name="' + fieldName + '"]');
                if (inputElement) { inputElement.value = savedValue; }
            }
        });
    });
</script>
@endsection