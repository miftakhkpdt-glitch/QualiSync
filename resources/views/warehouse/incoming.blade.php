@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 1400px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h3 style="margin-bottom: 5px; color: #334155; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
        <i class="fas fa-truck-loading"></i> Incoming Material
    </h3>
    <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">Pencatatan material masuk dari supplier sebelum diverifikasi oleh QC.</p>

    @if(session('success'))
        <div style="background: #dcfce7; color: #16a34a; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #bbf7d0;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #fecaca;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- FORM INPUT INCOMING -->
    <form action="{{ route('warehouse.incoming.store') }}" method="POST" style="background: #f8fafc; padding: 20px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 30px;">
        @csrf
        
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 15px;">
            <!-- Date -->
            <div>
                <label style="font-size: 13px; font-weight: bold;">Date</label>
                <input type="date" name="date" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>

            <!-- MM & Item Name -->
            <div>
                <label style="font-size: 13px; font-weight: bold;">No. MM</label>
                <input type="text" name="mm" id="input_mm" list="list-mm" placeholder="Ketik/Pilih No. MM..." required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;" oninput="autofillMaterial(this)">
                <datalist id="list-mm">
                    @foreach($materials as $mat)
                        <option value="{{ $mat->no_mm }}">{{ $mat->no_mm }} - {{ $mat->nama_material }}</option>
                    @endforeach
                </datalist>
            </div>

            <div>
                <label style="font-size: 13px; font-weight: bold;">Item Name</label>
                <input type="text" name="item_name" id="input_item_name" placeholder="Otomatis terisi" readonly required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px; background: #f1f5f9;">
            </div>

            <!-- Quantity & Uom (UOM SEKARANG MENGGUNAKAN DROPDOWN SELECT) -->
            <div>
                <label style="font-size: 13px; font-weight: bold;">Quantity</label>
                <input type="number" step="0.01" name="quantity" placeholder="0" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>

            <div>
                <label style="font-size: 13px; font-weight: bold;">Uom</label>
                <select name="uom" id="input_uom" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px; background: #ffffff;">
                    <option value="" disabled selected>Pilih Satuan</option>
                    <option value="Pcs">Pcs</option>
                    <option value="Kg">Kg</option>
                    <option value="Gram">Gram</option>
                    <option value="Meter">Meter</option>
                    <option value="Roll">Roll</option>
                    <option value="Liter">Liter</option>
                    <option value="Set">Set</option>
                </select>
            </div>

            <!-- STPB Number -->
            <div>
                <label style="font-size: 13px; font-weight: bold;">STPB Number</label>
                <input type="text" name="stpb_number" placeholder="No. STPB" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>

            <!-- PO KPDT Number -->
            <div>
                <label style="font-size: 13px; font-weight: bold;">PO KPDT Number</label>
                <input type="text" name="po_kpdt_number" placeholder="No. PO KPDT" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>

            <!-- SJ Supplier Number -->
            <div>
                <label style="font-size: 13px; font-weight: bold;">SJ Supplier Number</label>
                <input type="text" name="sj_supplier_number" placeholder="No. SJ Supplier" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>

            <!-- Vendor Code, Name, Address -->
            <div>
                <label style="font-size: 13px; font-weight: bold;">Vendor Code</label>
                <input type="text" name="vendor_code" id="input_vendor_code" list="list-vendor" placeholder="Ketik Vendor Code..." required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;" oninput="autofillVendor(this)">
                <datalist id="list-vendor">
                    @foreach($vendors as $ven)
                        <option value="{{ $ven->vendor_code }}">{{ $ven->vendor_code }} - {{ $ven->vendor_name }}</option>
                    @endforeach
                </datalist>
            </div>

            <div>
                <label style="font-size: 13px; font-weight: bold;">Vendor Name</label>
                <input type="text" name="vendor_name" id="input_vendor_name" placeholder="Otomatis terisi" readonly required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px; background: #f1f5f9;">
            </div>

            <div style="grid-column: span 2;">
                <label style="font-size: 13px; font-weight: bold;">Address</label>
                <input type="text" name="address" id="input_address" placeholder="Otomatis terisi" readonly required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px; background: #f1f5f9;">
            </div>

            <!-- Remarks -->
            <div style="grid-column: span 4;">
                <label style="font-size: 13px; font-weight: bold;">Remarks</label>
                <input type="text" name="remarks" placeholder="Catatan tambahan (opsional)" style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>
        </div>

        <div>
            <button type="submit" style="background: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                <i class="fas fa-save"></i> Simpan STPB
            </button>
        </div>
    </form>

    <!-- Header & Filter Tabel Aktif -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h4 style="color: #334155; margin: 0;">1. Daftar Riwayat Incoming Aktif</h4>
        
        <form action="{{ route('warehouse.incoming.index') }}" method="GET" style="display: flex; gap: 8px; align-items: center;">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari No. MM / STPB..." style="padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px; width: 220px;">
            <button type="submit" style="background: #0ea5e9; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px;">
                <i class="fas fa-search"></i> Filter
            </button>
            @if(isset($search) && $search != '')
                <a href="{{ route('warehouse.incoming.index') }}" style="background: #64748b; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 13px; display: flex; align-items: center;" title="Reset Filter">
                    <i class="fas fa-redo"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <!-- FORM UNTUK BULK DELETE -->
    <form action="{{ route('warehouse.incoming.bulk_delete') }}" method="POST" id="form-bulk-delete">
        @csrf
        @method('DELETE')
        
        <div style="margin-bottom: 10px; min-height: 35px;">
            <button type="submit" id="btn-hapus-banyak" style="background: #ef4444; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold; display: none;" onclick="return confirm('Yakin ingin memindahkan data terpilih ke riwayat hapus?')">
                <i class="fas fa-trash-alt"></i> Hapus Terpilih (<span id="jumlah-pilih">0</span>)
            </button>
        </div>

        <div style="overflow-x: auto; margin-bottom: 40px;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead>
                    <tr style="background: #0ea5e9; color: white;">
                        <th style="padding: 10px; width: 40px; text-align: center;">
                            <input type="checkbox" id="check-all" style="cursor: pointer; transform: scale(1.2);">
                        </th>
                        <th style="padding: 10px;">Date</th>
                        <th style="padding: 10px;">MM</th>
                        <th style="padding: 10px;">Item Name</th>
                        <th style="padding: 10px;">Quantity</th>
                        <th style="padding: 10px;">Uom</th>
                        <th style="padding: 10px;">STPB No</th>
                        <th style="padding: 10px;">PO KPDT No</th>
                        <th style="padding: 10px;">SJ Supplier No</th>
                        <th style="padding: 10px;">Vendor Code</th>
                        <th style="padding: 10px;">Vendor Name</th>
                        <th style="padding: 10px;">Address</th>
                        <th style="padding: 10px;">Remarks</th>
                        <th style="padding: 10px;">Status QC</th>
                        <th style="padding: 10px;">Input By</th>
                        <th style="padding: 10px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incomingList as $row)
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 9px; text-align: center;">
                                <input type="checkbox" name="ids[]" value="{{ $row->id }}" class="check-item" style="cursor: pointer; transform: scale(1.2);">
                            </td>
                            <td style="padding: 9px;">{{ $row->date }}</td>
                            <td style="padding: 9px;">{{ $row->mm }}</td>
                            <td style="padding: 9px;">{{ $row->item_name }}</td>
                            <td style="padding: 9px;">{{ number_format($row->quantity, 2) }}</td>
                            <td style="padding: 9px;">{{ $row->uom }}</td>
                            <td style="padding: 9px;">{{ $row->stpb_number }}</td>
                            <td style="padding: 9px;">{{ $row->po_kpdt_number }}</td>
                            <td style="padding: 9px;">{{ $row->sj_supplier_number }}</td>
                            <td style="padding: 9px;">{{ $row->vendor_code }}</td>
                            <td style="padding: 9px;">{{ $row->vendor_name }}</td>
                            <td style="padding: 9px;">{{ $row->address }}</td>
                            <td style="padding: 9px;">{{ $row->remarks ?? '-' }}</td>
                            
                            <!-- DATA BADGE STATUS QC -->
                            <td style="padding: 9px;">
                                <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; background: {{ ($row->status_qc ?? 'Pending') == 'Pass' ? '#dcfce7; color: #15803d;' : (($row->status_qc ?? 'Pending') == 'NG' ? '#fee2e2; color: #b91c1c;' : '#fef3c7; color: #b45309;') }}">
                                    {{ $row->status_qc ?? 'Pending' }}
                                </span>
                            </td>

                            <td style="padding: 9px; font-weight: 500; color: #0284c7;">{{ $row->input_by ?? 'System' }}</td>
                            <td style="padding: 9px; text-align: center; display: flex; gap: 5px; justify-content: center; align-items: center;">
                                <a href="{{ route('warehouse.incoming.edit', $row->id) }}" style="background: #eab308; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: bold;" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" onclick="hapusSatuan({{ $row->id }})" style="background: #ef4444; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" style="text-align: center; padding: 20px; color: #64748b;">Belum ada data incoming.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <!-- Form Tersembunyi untuk Hapus Satuan -->
    <form id="form-hapus-satuan" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- ========================================== -->
    <!-- BAGIAN 2: RIWAYAT KEDATANGAN BARANG (FILTER) -->
    <!-- ========================================== -->
    <div style="margin-bottom: 40px;">
        <h4 style="color: #0f172a; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-filter"></i> 2. Rekapan & Filter Kedatangan Barang</h4>
        
        <!-- FORM FILTER KHUSUS INCOMING -->
        <form action="{{ url()->current() }}" method="GET" style="background: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 15px; display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end;">
            
            <div>
                <label style="font-size: 11px; font-weight: bold; color: #475569; display: block; margin-bottom: 3px;">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; width: 125px; font-size: 12px;">
            </div>
            
            <div>
                <label style="font-size: 11px; font-weight: bold; color: #475569; display: block; margin-bottom: 3px;">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; width: 125px; font-size: 12px;">
            </div>

            <div>
                <label style="font-size: 11px; font-weight: bold; color: #475569; display: block; margin-bottom: 3px;">Kategori Produk</label>
                <select name="in_kategori" style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; width: 140px; font-size: 12px;">
                    <option value="">-- Semua Kategori --</option>
                    <option value="Raw Material" {{ request('in_kategori') == 'Raw Material' ? 'selected' : '' }}>Raw Material</option>
                    <option value="Finish Good" {{ request('in_kategori') == 'Finish Good' ? 'selected' : '' }}>Finish Good</option>
                </select>
            </div>

            <div>
                <label style="font-size: 11px; font-weight: bold; color: #475569; display: block; margin-bottom: 3px;">No. PO</label>
                <input type="text" name="in_po" value="{{ request('in_po') }}" placeholder="Cari No. PO..." style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 12px;">
            </div>

            <div style="flex-grow: 1; min-width: 180px;">
                <label style="font-size: 11px; font-weight: bold; color: #475569; display: block; margin-bottom: 3px;">Pencarian (No. MM / Nama)</label>
                <input type="text" name="in_search" value="{{ request('in_search') }}" placeholder="Ketik MM / Nama..." style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 12px;">
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" style="background: #10b981; color: white; border: none; padding: 9px 15px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: bold;">Filter Kedatangan</button>
                <a href="{{ url()->current() }}" style="background: #94a3b8; color: white; border: none; padding: 9px 15px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: bold; display: flex; align-items: center; justify-content: center;">Reset</a>
                
                <!-- TOMBOL CETAK PDF -->
                <button type="submit" name="export_pdf" value="1" formtarget="_blank" style="background: #dc2626; color: white; border: none; padding: 9px 15px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: bold;">
                    <i class="fas fa-file-pdf"></i> Cetak PDF
                </button>
            </div>
        </form>

        <div style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 6px;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead>
                    <tr style="background: #0ea5e9; color: white;">
                        <th style="padding: 12px 10px;">Waktu Masuk</th>
                        <th style="padding: 12px 10px;">No. STPB</th>
                        <th style="padding: 12px 10px;">No. PO</th>
                        <th style="padding: 12px 10px;">No. SJ Supplier</th>
                        <th style="padding: 12px 10px;">No. MM</th>
                        <th style="padding: 12px 10px;">Nama Material</th>
                        <th style="padding: 12px 10px; text-align: center;">Kategori Produk</th>
                        <th style="padding: 12px 10px;">Supplier / Vendor</th>
                        <th style="padding: 12px 10px; text-align: right;">Qty Masuk</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalIncomingQty = 0; @endphp
                    @forelse($riwayatKedatangan ?? [] as $incoming)
                        @php $totalIncomingQty += $incoming->quantity; @endphp
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 10px; font-weight: bold; color: #475569;">
                                {{ date('d M Y, H:i', strtotime($incoming->updated_at)) }}
                            </td>
                            <td style="padding: 10px;">{{ $incoming->stpb_number ?? '-' }}</td>
                            <td style="padding: 10px; color: #0284c7; font-weight: 500;">
                                {{ $incoming->po_kpdt_number ?? '-' }}
                            </td>
                            <td style="padding: 10px;">{{ $incoming->sj_supplier_number ?? '-' }}</td>
                            <td style="padding: 10px; font-weight: bold;">{{ $incoming->mm }}</td>
                            <td style="padding: 10px;">{{ $incoming->item_name }}</td>
                            <td style="padding: 10px; text-align: center;">
                                <span style="background: #fef3c7; color: #b45309; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">
                                    {{ $incoming->kategori ?? '-' }}
                                </span>
                            </td>
                            <td style="padding: 10px;">{{ $incoming->vendor_name ?? '-' }}</td>
                            <td style="padding: 10px; text-align: right; font-weight: bold; color: #16a34a;">
                                + {{ number_format($incoming->quantity, 2) }} {{ $incoming->uom }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <!-- Colspan menjadi 9 karena ada 9 kolom total -->
                            <td colspan="9" style="text-align: center; padding: 20px; color: #64748b;">
                                Tidak ada data kedatangan barang pada rentang filter tersebut.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(isset($riwayatKedatangan) && count($riwayatKedatangan) > 0)
                <tfoot>
                    <tr style="background: #f0f9ff; font-weight: bold;">
                        <td colspan="8" style="padding: 12px 10px; text-align: right; color: #0369a1;">TOTAL QTY KEDATANGAN TERFILTER:</td>
                        <td style="padding: 12px 10px; text-align: right; color: #0ea5e9; font-size: 14px;">+ {{ number_format($totalIncomingQty, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <hr style="border: 0; border-top: 2px dashed #cbd5e1; margin: 40px 0;">

    <!-- ========================================== -->
    <!-- BAGIAN 3: TABEL RIWAYAT INCOMING DIHAPUS -->
    <!-- ========================================== -->
    <h4 style="margin-bottom: 15px; color: #b91c1c; font-size: 16px;"><i class="fas fa-history"></i> 3. Riwayat Incoming Material Dihapus</h4>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; background: #fff5f5;">
            <thead>
                <tr style="background: #fee2e2; color: #991b1b; border-bottom: 2px solid #fca5a5;">
                    <th style="padding: 10px;">Date</th>
                    <th style="padding: 10px;">MM & Item Name</th>
                    <th style="padding: 10px;">Quantity</th>
                    <th style="padding: 10px;">STPB No</th>
                    <th style="padding: 10px;">Vendor Name</th>
                    <th style="padding: 10px;">Dihapus Oleh</th>
                    <th style="padding: 10px;">Waktu Dihapus</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deletedIncoming ?? [] as $dinc)
                    <tr style="border-bottom: 1px solid #fecaca;">
                        <td style="padding: 10px;">{{ $dinc->date }}</td>
                        <td style="padding: 10px; color: #1e293b;">
                            <b>{{ $dinc->mm }}</b><br>
                            <span style="color: #64748b; font-size: 11px;">{{ $dinc->item_name }}</span>
                        </td>
                        <td style="padding: 10px; font-weight: bold;">
                            {{ number_format($dinc->quantity, 2) }} {{ $dinc->uom }}
                        </td>
                        <td style="padding: 10px;">{{ $dinc->stpb_number }}</td>
                        <td style="padding: 10px;">{{ $dinc->vendor_name }}</td>
                        <td style="padding: 10px;">
                            <span style="background: #f87171; color: white; padding: 3px 8px; border-radius: 4px; font-size: 11px;">
                                <i class="fas fa-user-times"></i> {{ $dinc->deleted_by ?? 'System' }}
                            </span>
                        </td>
                        <td style="padding: 10px; font-size: 12px; color: #64748b;">
                            {{ $dinc->deleted_at ? date('d M Y, H:i', strtotime($dinc->deleted_at)) : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px; color: #991b1b;">Belum ada riwayat incoming yang dihapus.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- JAVASCRIPT AUTOFILL & BULK DELETE -->
<script>
    const materialsData = @json($materials);
    const vendorsData = @json($vendors);

    function autofillMaterial(inputEl) {
        let val = inputEl.value;
        let matched = materialsData.find(m => m.no_mm == val);

        let nameInput = document.getElementById('input_item_name');
        let uomInput = document.getElementById('input_uom');

        if (matched) {
            nameInput.value = matched.nama_material; 
            uomInput.value = matched.satuan ?? 'Pcs';   
        } else {
            nameInput.value = '';
            uomInput.value = '';
        }
    }

    function autofillVendor(inputEl) {
        let val = inputEl.value;
        let matched = vendorsData.find(v => v.vendor_code == val);

        let nameInput = document.getElementById('input_vendor_name');
        let addrInput = document.getElementById('input_address');

        if (matched) {
            nameInput.value = matched.vendor_name; 
            addrInput.value = matched.address;    
        } else {
            nameInput.value = '';
            addrInput.value = '';
        }
    }

    // --- SKRIP FITUR CHECKBOX & BULK DELETE ---
    const checkAll = document.getElementById('check-all');
    const checkItems = document.querySelectorAll('.check-item');
    const btnHapusBanyak = document.getElementById('btn-hapus-banyak');
    const spanJumlahPilih = document.getElementById('jumlah-pilih');

    function toggleTombolHapus() {
        let jumlahDicentang = document.querySelectorAll('.check-item:checked').length;
        spanJumlahPilih.innerText = jumlahDicentang;
        
        if (jumlahDicentang > 0) {
            btnHapusBanyak.style.display = 'inline-block';
        } else {
            btnHapusBanyak.style.display = 'none';
            if(checkAll) checkAll.checked = false;
        }
    }

    if(checkAll) {
        checkAll.addEventListener('change', function() {
            checkItems.forEach(item => {
                item.checked = this.checked;
            });
            toggleTombolHapus();
        });
    }

    checkItems.forEach(item => {
        item.addEventListener('change', toggleTombolHapus);
    });

    function hapusSatuan(id) {
        if(confirm('Yakin ingin memindahkan data incoming ini ke riwayat hapus?')) {
            let formSatuan = document.getElementById('form-hapus-satuan');
            formSatuan.action = "{{ url('/warehouse/incoming') }}/" + id;
            formSatuan.submit();
        }
    }
</script>
@endsection