@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 1400px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    
    <!-- HEADER -->
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px;">
        <h3 style="margin: 0; color: #334155;"><i class="fas fa-boxes"></i> Mutasi (Raw Material)</h3>
        
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('warehouse.stok.raw_material.print', request()->query()) }}" target="_blank" style="background: #3b82f6; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 13px; text-decoration: none;">
                <i class="fas fa-print"></i> Cetak Hasil Filter
            </a>
            
            <button onclick="bukaModalMutasi()" style="background: #10b981; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 13px;">
                <i class="fas fa-exchange-alt"></i> Buat Mutasi Keluar
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- FITUR BARU: PAPAN CONTEKAN BOM (Muncul Jika Diarahkan dari Panduan WO) -->
    <!-- ========================================================================= -->
    @if(request()->has('fg_mm'))
        <div style="background: #f0f9ff; border-left: 5px solid #0ea5e9; padding: 15px; border-radius: 4px; margin-bottom: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <div style="font-weight: bold; color: #0284c7; font-size: 14px; margin-bottom: 5px;">
                <i class="fas fa-info-circle"></i> Panduan Pengeluaran Material Produksi
            </div>
            <div style="color: #334155; font-size: 13px; line-height: 1.5;">
                Anda diarahkan ke sini untuk memotong stok bahan baku guna keperluan produksi Barang Jadi (Finish Good) dengan Nomor MM: <b style="color: #0f172a; font-size: 14px; background: #e2e8f0; padding: 2px 6px; border-radius: 4px;">[{{ request('fg_mm') }}]</b>. 
                <br>Silakan klik tombol <b>"Buat Mutasi Keluar"</b> di pojok kanan atas, lalu lakukan mutasi pada komponen-komponen bahan bakunya sesuai dengan qty aktual per-box. Pastikan departemen tujuannya adalah <b>Produksi</b>.
            </div>
            <a href="{{ url('/warehouse/work-order') }}" style="display: inline-block; margin-top: 10px; font-size: 12px; color: #ef4444; text-decoration: none; font-weight: bold; padding: 4px 8px; border: 1px solid #fca5a5; border-radius: 4px; background: #fef2f2;">
                <i class="fas fa-times"></i> Tutup Panduan & Kembali ke Daftar WO
            </a>
        </div>
    @endif
    <!-- ========================================================================= -->
    <!-- ================= PESAN NOTIFIKASI (ALERT) ================= -->
    @if(session('success'))
        <div style="background: #dcfce7; border-left: 5px solid #10b981; color: #15803d; padding: 15px; border-radius: 4px; margin-bottom: 20px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #fee2e2; border-left: 5px solid #ef4444; color: #b91c1c; padding: 15px; border-radius: 4px; margin-bottom: 20px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <i class="fas fa-exclamation-triangle"></i> Oops! Gagal memproses mutasi. Periksa kembali isian Anda:
            <ul style="margin-top: 5px; margin-bottom: 0; padding-left: 20px; font-weight: normal; font-size: 13px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <!-- ========================================================== -->
    <!-- FORM FILTER -->
    <form action="{{ route('warehouse.stok.raw_material') }}" method="GET" style="background: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 20px; display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
        <!-- ... (Kode Form Filter Anda Tetap Sama) ... -->
        <div>
            <label style="display: block; font-size: 12px; font-weight: bold; color: #475569; margin-bottom: 5px;">Dari Tanggal</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" style="width: 140px; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>
        <div>
            <label style="display: block; font-size: 12px; font-weight: bold; color: #475569; margin-bottom: 5px;">Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" style="width: 140px; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>
        <div>
            <label style="display: block; font-size: 12px; font-weight: bold; color: #475569; margin-bottom: 5px;">Kategori</label>
            <select name="kategori" style="width: 160px; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                <option value="">Semua Kategori</option>
                <option value="Raw Material" {{ request('kategori') == 'Raw Material' ? 'selected' : '' }}>Raw Material</option>
                <option value="Finish Good" {{ request('kategori') == 'Finish Good' ? 'selected' : '' }}>Finish Good</option>
            </select>
        </div>
        <div>
            <label style="display: block; font-size: 12px; font-weight: bold; color: #475569; margin-bottom: 5px;">Pencarian</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari MM / Nama / Batch..." style="width: 250px; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>
        <!-- Tambahkan input hidden agar fg_mm tidak hilang saat tombol filter diklik -->
        @if(request()->has('fg_mm'))
            <input type="hidden" name="fg_mm" value="{{ request('fg_mm') }}">
        @endif
        <div style="display: flex; gap: 10px;">
            <button type="submit" style="background: #0ea5e9; color: white; border: none; padding: 9px 15px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold;">Filter</button>
            <a href="{{ route('warehouse.stok.raw_material') }}" style="background: #94a3b8; color: white; border: none; padding: 9px 15px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold; display: flex; align-items: center; justify-content: center;">Reset</a>
        </div>
    </form>

    <!-- TABEL RIWAYAT MUTASI -->
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #1e293b; color: white;">
                    <th style="padding: 12px 10px;">Tanggal & Shift</th>
                    <th style="padding: 12px 10px;">Alur Mutasi</th>
                    <th style="padding: 12px 10px;">MM, Item & Batch</th>
                    <th style="padding: 12px 10px;">Kategori</th>
                    <th style="padding: 12px 10px;">Qty Mutasi</th>
                    <th style="padding: 12px 10px;">Dibuat Oleh</th>
                    <th style="padding: 12px 10px; text-align: center;">Status Approval</th>
                    <th style="padding: 12px 10px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat_mutasi ?? [] as $mutasi)
                <tr style="border-bottom: 1px solid #e2e8f0; background: {{ $loop->iteration % 2 == 0 ? '#f8fafc' : '#ffffff' }};">
                    <td style="padding: 10px;">
                        <b>{{ date('d M Y', strtotime($mutasi->tanggal)) }}</b><br>
                        <span style="color: #64748b; font-size: 11px;">{{ $mutasi->shift }}</span>
                    </td>
                    <td style="padding: 10px;">
                        <span style="background: #e0f2fe; color: #0369a1; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">{{ $mutasi->dari_dept }}</span>
                        <i class="fas fa-arrow-right" style="margin: 0 5px; color: #94a3b8; font-size: 10px;"></i>
                        <span style="background: #fef3c7; color: #b45309; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">{{ $mutasi->ke_dept }}</span>
                    </td>
                    <td style="padding: 10px;">
                        <b>{{ $mutasi->mm }}</b><br>
                        <span style="color: #64748b; font-size: 11px;">{{ $mutasi->item_name ?? '-' }}</span><br>
                        <span style="color: #0ea5e9; font-size: 11px; font-weight: bold;">Batch: {{ $mutasi->batch ?? '-' }}</span>
                    </td>
                    <td style="padding: 10px;">
                        <span style="background: #f1f5f9; color: #475569; padding: 4px 8px; border-radius: 4px; font-size: 11px; border: 1px solid #cbd5e1;">
                            {{ $mutasi->kategori ?? '-' }}
                        </span>
                    </td>
                    <td style="padding: 10px; font-weight: bold; color: {{ $mutasi->dari_dept == 'Warehouse' ? '#b91c1c' : '#16a34a' }};">
                        {{ $mutasi->dari_dept == 'Warehouse' ? '-' : '+' }} {{ number_format($mutasi->qty, 2) }} {{ $mutasi->uom }}
                    </td>
                    <td style="padding: 10px;">{{ $mutasi->nama_pembuat ?? '-' }}</td>
                    <td style="padding: 10px; text-align: center;">
                        @if($mutasi->status_approval == 'Pending')
                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; background: #fef08a; color: #854d0e;"><i class="fas fa-clock"></i> Menunggu {{ $mutasi->ke_dept }}</span>
                        @else
                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; background: #dcfce7; color: #15803d;"><i class="fas fa-check-double"></i> Approved</span>
                        @endif
                    </td>
                    <td style="padding: 10px; text-align: center; display: flex; gap: 5px; justify-content: center;">
                        @if($mutasi->status_approval == 'Pending' && Auth::id() == $mutasi->pic_id)
                            <button onclick='bukaModalEdit(@json($mutasi))' style="background: #eab308; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;" title="Edit Data">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button style="background: #94a3b8; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: not-allowed; font-size: 12px;" disabled><i class="fas fa-print"></i></button>
                        @elseif($mutasi->status_approval == 'Approved')
                            <button onclick="alert('Mencetak Surat Jalan Internal...')" style="background: #3b82f6; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;" title="Print Bukti Fisik">
                                <i class="fas fa-print"></i> Cetak
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align: center; padding: 30px; color: #64748b;">Belum ada riwayat mutasi material.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODAL FORM INPUT MUTASI (CREATE) ================= -->
<div id="modalMutasi" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center;">
    <div style="background-color: #fff; width: 650px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); display: flex; flex-direction: column; max-height: 90vh;">
        <div style="background-color: #1e293b; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px;"><i class="fas fa-exchange-alt"></i> Form Mutasi Keluar (Multi-Item)</h3>
            <span onclick="tutupModalMutasi()" style="cursor: pointer; font-size: 20px; font-weight: bold;">&times;</span>
        </div>
        
        <!-- Tambahan overflow-y: auto agar form bisa di-scroll jika itemnya banyak -->
        <form action="{{ route('warehouse.stok.mutasi.store') }}" method="POST" style="padding: 20px; overflow-y: auto; flex: 1;">
            @csrf
            @if(request()->has('fg_mm'))
                <input type="hidden" name="fg_mm" value="{{ request('fg_mm') }}">
            @endif
            
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
                <div style="flex: 1.5;">
                    <label style="font-size: 12px; font-weight: bold;">Tujuan Departemen</label>
                    <select name="ke_dept" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                        <option value="Produksi" {{ request()->has('fg_mm') ? 'selected' : '' }}>Produksi</option>
                        <option value="QC">Quality Control (QC)</option>
                    </select>
                </div>
            </div>
            
            <hr style="border: none; border-top: 1px dashed #cbd5e1; margin: 20px 0;">
            
            <!-- WADAH MATERIAL DINAMIS -->
            <div id="dynamic_material_container">
                <!-- Baris Material 1 -->
                <div class="material-row" style="background: #f8fafc; padding: 15px; border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 15px;">
                    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <div style="flex: 1;">
                            <label style="font-size: 12px; font-weight: bold;">No. MM Komponen</label>
                            <input type="text" name="mm[]" required placeholder="Cari MM..." style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;" onkeyup="cariNamaItemDynamic(this)">
                        </div>
                        <div style="flex: 2;">
                            <label style="font-size: 12px; font-weight: bold;">Nama Item (Otomatis)</label>
                            <input type="text" name="item_name[]" placeholder="Otomatis..." readonly style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px; background-color: #f1f5f9;" class="input-item-name">
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 10px;">
                        <div style="flex: 1;">
                            <label style="font-size: 12px; font-weight: bold;">No. Batch (Opsional)</label>
                            <input type="text" name="batch[]" placeholder="No Batch..." style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                        </div>
                        <div style="flex: 1;">
                            <label style="font-size: 12px; font-weight: bold;">Qty (Aktual Box)</label>
                            <input type="number" step="0.01" name="qty[]" required placeholder="0.00" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                        </div>
                        <div style="flex: 1;">
                            <label style="font-size: 12px; font-weight: bold;">Satuan</label>
                            <input type="text" name="uom[]" value="Pcs" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="button" onclick="tambahBarisMaterial()" style="background: #e0f2fe; color: #0284c7; border: 1px dashed #0284c7; padding: 10px; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; font-size: 13px; margin-bottom: 10px;">
                <i class="fas fa-plus"></i> Tambah Material Lainnya
            </button>

            <div style="text-align: right; margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 15px;">
                <button type="button" onclick="tutupModalMutasi()" style="background: #94a3b8; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; margin-right: 5px;">Batal</button>
                <button type="submit" style="background: #10b981; color: white; border: none; padding: 10px 25px; border-radius: 4px; cursor: pointer; font-weight: bold;">Mutasikan Semua!</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL FORM EDIT MUTASI ================= -->
<div id="modalEdit" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center;">
    <div style="background-color: #fff; width: 500px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); overflow: hidden;">
        <div style="background-color: #eab308; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px;"><i class="fas fa-edit"></i> Edit Mutasi</h3>
            <span onclick="tutupModalEdit()" style="cursor: pointer; font-size: 20px; font-weight: bold;">&times;</span>
        </div>
        <form id="formEditMutasi" method="POST" style="padding: 20px;">
            @csrf
            @method('PUT')
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">Tanggal</label>
                    <input type="date" id="edit_tanggal" name="tanggal" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">Shift</label>
                    <select id="edit_shift" name="shift" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                        <option value="Shift 1">Shift 1 (Pagi)</option><option value="Shift 2">Shift 2 (Sore)</option><option value="Shift 3">Shift 3 (Malam)</option><option value="Non Shift">Non Shift</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="font-size: 12px; font-weight: bold;">Tujuan Departemen</label>
                <select id="edit_ke_dept" name="ke_dept" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                    <option value="Produksi">Produksi</option><option value="QC">Quality Control (QC)</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">No. MM</label>
                    <input type="text" id="edit_mm" name="mm" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;" onkeyup="cariNamaItemEdit(this.value)">
                </div>
                <div style="flex: 2;">
                    <label style="font-size: 12px; font-weight: bold;">Nama Item</label>
                    <input type="text" id="edit_item_name" name="item_name" readonly style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px; background-color: #f1f5f9;">
                </div>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">No. Batch</label>
                    <input type="text" id="edit_batch" name="batch" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">Qty</label>
                    <input type="number" step="0.01" id="edit_qty" name="qty" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">Satuan</label>
                    <input type="text" id="edit_uom" name="uom" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                </div>
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <button type="button" onclick="tutupModalEdit()" style="background: #94a3b8; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; margin-right: 5px;">Batal</button>
                <button type="submit" style="background: #eab308; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= SCRIPTS ================= -->
<script>
    function bukaModalMutasi() { document.getElementById('modalMutasi').style.display = 'flex'; }
    function tutupModalMutasi() { document.getElementById('modalMutasi').style.display = 'none'; }
    
    // --- FITUR BARU: TAMBAH MATERIAL DINAMIS ---
    function tambahBarisMaterial() {
        let container = document.getElementById('dynamic_material_container');
        let html = `
            <div class="material-row" style="background: #f8fafc; padding: 15px; border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 15px; position: relative;">
                <button type="button" onclick="this.closest('.material-row').remove()" style="position: absolute; top: -10px; right: -10px; background: #ef4444; color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2);" title="Hapus Baris ini">X</button>
                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <div style="flex: 1;">
                        <label style="font-size: 12px; font-weight: bold;">No. MM Komponen</label>
                        <input type="text" name="mm[]" required placeholder="Cari MM..." style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;" onkeyup="cariNamaItemDynamic(this)">
                    </div>
                    <div style="flex: 2;">
                        <label style="font-size: 12px; font-weight: bold;">Nama Item</label>
                        <input type="text" name="item_name[]" placeholder="Otomatis..." readonly style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px; background-color: #f1f5f9;" class="input-item-name">
                    </div>
                </div>
                <div style="display: flex; gap: 10px;">
                    <div style="flex: 1;">
                        <label style="font-size: 12px; font-weight: bold;">No. Batch (Opsional)</label>
                        <input type="text" name="batch[]" placeholder="No Batch..." style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                    </div>
                    <div style="flex: 1;">
                        <label style="font-size: 12px; font-weight: bold;">Qty (Aktual Box)</label>
                        <input type="number" step="0.01" name="qty[]" required placeholder="0.00" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                    </div>
                    <div style="flex: 1;">
                        <label style="font-size: 12px; font-weight: bold;">Satuan</label>
                        <input type="text" name="uom[]" value="Pcs" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    // --- AJAX PENCARIAN NAMA ITEM CERDAS (Mencari di dalam baris yang sama) ---
    function cariNamaItemDynamic(element) {
        let mm = element.value;
        // Cari class input-item-name di dalam blok form yang sedang diketik saja
        let barisForm = element.closest('.material-row');
        let inputNama = barisForm.querySelector('.input-item-name');
        
        if (mm.trim() === '') { inputNama.value = ''; return; }
        fetch(`{{ route('warehouse.stok.get_item') }}?mm=${mm}`)
            .then(res => res.json())
            .then(data => { inputNama.value = data.success ? data.item_name : ''; })
            .catch(err => console.error(err));
    }

    // --- FUNGSI MODAL EDIT (TETAP SAMA KARENA EDIT BIASANYA PER-SATUAN) ---
    function bukaModalEdit(data) {
        document.getElementById('formEditMutasi').action = `/warehouse/stok/mutasi/update/${data.id}`;
        let tgl = data.tanggal.split(' ')[0];
        document.getElementById('edit_tanggal').value = tgl;
        document.getElementById('edit_shift').value = data.shift;
        document.getElementById('edit_ke_dept').value = data.ke_dept;
        document.getElementById('edit_mm').value = data.mm;
        document.getElementById('edit_item_name').value = data.item_name;
        document.getElementById('edit_batch').value = data.batch;
        document.getElementById('edit_qty').value = data.qty;
        document.getElementById('edit_uom').value = data.uom;
        document.getElementById('modalEdit').style.display = 'flex';
    }
    function tutupModalEdit() { document.getElementById('modalEdit').style.display = 'none'; }

    function cariNamaItemEdit(mm) {
        let inputNama = document.getElementById('edit_item_name');
        if (mm.trim() === '') { inputNama.value = ''; return; }
        fetch(`{{ route('warehouse.stok.get_item') }}?mm=${mm}`)
            .then(res => res.json())
            .then(data => { inputNama.value = data.success ? data.item_name : ''; })
            .catch(err => console.error(err));
    }
</script>

<!-- ================= MODAL FORM EDIT MUTASI (Tetap Sama) ================= -->
<div id="modalEdit" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center;">
    <div style="background-color: #fff; width: 500px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); overflow: hidden;">
        <!-- Modal Edit Body (Tidak ada yang dirubah) -->
        <div style="background-color: #eab308; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px;"><i class="fas fa-edit"></i> Edit Mutasi</h3>
            <span onclick="tutupModalEdit()" style="cursor: pointer; font-size: 20px; font-weight: bold;">&times;</span>
        </div>
        <form id="formEditMutasi" method="POST" style="padding: 20px;">
            @csrf
            @method('PUT')
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">Tanggal</label>
                    <input type="date" id="edit_tanggal" name="tanggal" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">Shift</label>
                    <select id="edit_shift" name="shift" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                        <option value="Shift 1">Shift 1 (Pagi)</option><option value="Shift 2">Shift 2 (Sore)</option><option value="Shift 3">Shift 3 (Malam)</option><option value="Non Shift">Non Shift</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="font-size: 12px; font-weight: bold;">Tujuan Departemen</label>
                <select id="edit_ke_dept" name="ke_dept" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                    <option value="Produksi">Produksi</option><option value="QC">Quality Control (QC)</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">No. MM</label>
                    <input type="text" id="edit_mm" name="mm" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;" onkeyup="cariNamaItemEdit(this.value)">
                </div>
                <div style="flex: 2;">
                    <label style="font-size: 12px; font-weight: bold;">Nama Item</label>
                    <input type="text" id="edit_item_name" name="item_name" readonly style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px; background-color: #f1f5f9;">
                </div>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">No. Batch</label>
                    <input type="text" id="edit_batch" name="batch" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">Qty</label>
                    <input type="number" step="0.01" id="edit_qty" name="qty" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">Satuan</label>
                    <input type="text" id="edit_uom" name="uom" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                </div>
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <button type="button" onclick="tutupModalEdit()" style="background: #94a3b8; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; margin-right: 5px;">Batal</button>
                <button type="submit" style="background: #eab308; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= SCRIPTS ================= -->
<script>
    function bukaModalMutasi() { document.getElementById('modalMutasi').style.display = 'flex'; }
    function tutupModalMutasi() { document.getElementById('modalMutasi').style.display = 'none'; }
    
    function bukaModalEdit(data) {
        document.getElementById('formEditMutasi').action = `/warehouse/stok/mutasi/update/${data.id}`;
        
        let tgl = data.tanggal.split(' ')[0];
        document.getElementById('edit_tanggal').value = tgl;
        document.getElementById('edit_shift').value = data.shift;
        document.getElementById('edit_ke_dept').value = data.ke_dept;
        document.getElementById('edit_mm').value = data.mm;
        document.getElementById('edit_item_name').value = data.item_name;
        document.getElementById('edit_batch').value = data.batch;
        document.getElementById('edit_qty').value = data.qty;
        document.getElementById('edit_uom').value = data.uom;

        document.getElementById('modalEdit').style.display = 'flex';
    }
    function tutupModalEdit() { document.getElementById('modalEdit').style.display = 'none'; }

    function cariNamaItem(mm) {
        let inputNama = document.getElementById('input_item_name');
        if (mm.trim() === '') { inputNama.value = ''; return; }
        fetch(`{{ route('warehouse.stok.get_item') }}?mm=${mm}`)
            .then(res => res.json())
            .then(data => { inputNama.value = data.success ? data.item_name : ''; })
            .catch(err => console.error(err));
    }

    function cariNamaItemEdit(mm) {
        let inputNama = document.getElementById('edit_item_name');
        if (mm.trim() === '') { inputNama.value = ''; return; }
        fetch(`{{ route('warehouse.stok.get_item') }}?mm=${mm}`)
            .then(res => res.json())
            .then(data => { inputNama.value = data.success ? data.item_name : ''; })
            .catch(err => console.error(err));
    }
</script>
@endsection