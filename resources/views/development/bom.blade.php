@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 1400px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h3 style="margin-bottom: 5px; color: #334155; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
        <i class="fas fa-project-diagram"></i> Bill of Materials (BOM) - Resep Produk
    </h3>
    <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">Pusat pengaturan resep komponen penyusun produk Finished Goods oleh Development.</p>

    @if(session('success'))
        <div style="background: #dcfce7; color: #16a34a; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #bbf7d0;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="background: #fee2e2; color: #dc2626; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #fecaca;">
            <ul style="margin: 0; padding-left: 15px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- FORM INPUT BOM BERSIH -->
    <form action="{{ route('development.bom.store') }}" method="POST" style="background: #f8fafc; padding: 20px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 30px;">
        @csrf
        
        <!-- PILIH FINISHED GOODS UTAMA -->
        <div style="margin-bottom: 20px; max-width: 600px;">
            <label style="font-size: 13px; font-weight: bold; color: #1e293b; display: block; margin-bottom: 5px;">Finished Goods (FG) Utama</label>
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px;">
                <input type="text" name="fg_mm" id="fg_mm_input" list="list-fg" placeholder="Ketik No. MM..." required oninput="updateItemName(this, 'fg_name_display')" style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-weight: bold; background: #ffffff;">
                <input type="text" id="fg_name_display" readonly placeholder="Nama Item Otomatis..." style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 4px; background: #f1f5f9; color: #64748b; font-weight: 500;">
            </div>
            <datalist id="list-fg">
                @foreach($finishGoods as $fg)
                    <option value="{{ $fg->no_mm }}">{{ $fg->nama_material }}</option>
                @endforeach
            </datalist>
        </div>

        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin-bottom: 15px;">
        <label style="font-size: 13px; font-weight: bold; color: #334155; display: block; margin-bottom: 10px;">Daftar Komponen Penyusun:</label>

        <!-- CONTAINER TABEL DINAMIS KOMPONEN -->
        <div id="component-container">
            <div class="component-row" style="display: grid; grid-template-columns: 1.2fr 2fr 0.8fr 1fr auto; gap: 10px; align-items: center; margin-bottom: 10px;">
                <div>
                    <input type="text" name="components[0][component_mm]" list="list-materials" placeholder="No. MM Komponen..." required oninput="updateRowItemName(this)" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; background: #ffffff;">
                </div>
                <div>
                    <input type="text" class="row-name-display" readonly placeholder="Nama Komponen Otomatis..." style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 4px; background: #f1f5f9; color: #64748b; font-size: 12px;">
                </div>
                <div>
                    <input type="number" step="0.0001" name="components[0][qty_usage]" placeholder="Qty Usage" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div>
                    <select name="components[0][satuan]" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; background: #ffffff; font-size: 12px;">
                        <option value="">Pilih Satuan</option>
                        <option value="Pcs">Pcs</option>
                        <option value="Kg">Kg</option>
                        <option value="Gram">Gram</option>
                        <option value="Meter">Meter</option>
                        <option value="Roll">Roll</option>
                        <option value="Liter">Liter</option>
                        <option value="Set">Set</option>
                    </select>
                </div>
                <div>
                    <button type="button" onclick="removeRow(this)" style="background: #ef4444; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; display: none;"><i class="fas fa-times"></i></button>
                </div>
            </div>
        </div>

        <!-- DATALIST GLOBAL -->
        <datalist id="list-materials">
            @foreach($materials as $mat)
                <option value="{{ $mat->no_mm }}">{{ $mat->nama_material }}</option>
            @endforeach
        </datalist>

        <!-- TOMBOL TAMBAH BARIS & SIMPAN -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
            <button type="button" onclick="addRow()" style="background: #0ea5e9; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold;">
                <i class="fas fa-plus"></i> Tambah Baris Komponen
            </button>

            <button type="submit" style="background: #16a34a; color: white; padding: 10px 25px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 14px;">
                <i class="fas fa-save"></i> Simpan Seluruh Resep BOM
            </button>
        </div>
    </form>

    <!-- FILTER & TABEL DAFTAR BOM AKTIF -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 10px;">
        <h4 style="color: #334155; margin: 0;">Daftar Resep BOM Aktif</h4>
        
        <!-- FORM FILTER BERDASARKAN FG (DITAMBAHKAN ID "select-fg") -->
        <form action="{{ route('development.bom.index') }}" method="GET" style="display: flex; gap: 8px; align-items: center;">
            <select name="filter_fg" id="select-fg" style="padding: 7px 12px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px; background: #ffffff; min-width: 250px;">
                <option value="">-- Semua Finished Goods --</option>
                @foreach($finishGoods as $fg)
                    <option value="{{ $fg->no_mm }}" {{ request('filter_fg') == $fg->no_mm ? 'selected' : '' }}>
                        {{ $fg->no_mm }} - {{ $fg->nama_material }}
                    </option>
                @endforeach
            </select>
            <button type="submit" style="background: #0ea5e9; color: white; border: none; padding: 7px 15px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold;">
                <i class="fas fa-filter"></i> Filter
            </button>
            @if(request('filter_fg'))
                <a href="{{ route('development.bom.index') }}" style="background: #64748b; color: white; padding: 7px 12px; border-radius: 4px; text-decoration: none; font-size: 13px;">
                    <i class="fas fa-redo"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #0ea5e9; color: white;">
                    <th style="padding: 10px;">Finished Goods (FG)</th>
                    <th style="padding: 10px;">Komponen Penyusun</th>
                    <th style="padding: 10px;">Qty Usage & Satuan</th>
                    <th style="padding: 10px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($boms as $row)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 9px; color: #1e293b;">
                            <b>{{ $row->fg_mm }}</b><br>
                            <span style="color: #64748b; font-size: 12px;">{{ $row->fg_name ?? '-' }}</span>
                        </td>
                        <td style="padding: 9px; color: #1e293b;">
                            <b>{{ $row->component_mm }}</b><br>
                            <span style="color: #64748b; font-size: 12px;">{{ $row->comp_name ?? '-' }}</span>
                        </td>
                        <td style="padding: 9px; font-weight: bold; color: #0284c7;">
                            {{ (float) $row->qty_usage }} {{ $row->satuan ?? '' }}
                        </td>
                        <td style="padding: 9px; text-align: center; display: flex; gap: 5px; justify-content: center;">
                            <!-- TOMBOL EDIT KUNING -->
                            <button type="button" onclick='bukaModalEdit(@json($row))' style="background: #eab308; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;" title="Edit Resep">
                                <i class="fas fa-edit"></i> Edit
                            </button>

                            <!-- TOMBOL HAPUS -->
                            <form action="{{ route('development.bom.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus resep ini?')" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: #ef4444; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px; color: #64748b;">Belum ada resep BOM yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODAL FORM EDIT BOM ================= -->
<div id="modalEditBom" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center;">
    <div style="background-color: #fff; width: 450px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); overflow: hidden;">
        <div style="background-color: #eab308; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px;"><i class="fas fa-edit"></i> Edit Resep BOM</h3>
            <span onclick="tutupModalEdit()" style="cursor: pointer; font-size: 20px; font-weight: bold; padding: 0 5px;" title="Tutup">&times;</span>
        </div>
        <form id="formEditBom" method="POST" style="padding: 20px;">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom: 15px;">
                <label style="font-size: 12px; font-weight: bold; color: #334155; display: block; margin-bottom: 5px;">Finished Goods (FG)</label>
                <input type="text" id="edit_fg_display" readonly style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; background: #f1f5f9; color: #64748b;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-size: 12px; font-weight: bold; color: #334155; display: block; margin-bottom: 5px;">Komponen Penyusun</label>
                <input type="text" id="edit_comp_display" readonly style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; background: #f1f5f9; color: #64748b;">
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 2;">
                    <label style="font-size: 12px; font-weight: bold; color: #334155; display: block; margin-bottom: 5px;">Qty Usage</label>
                    <input type="number" step="0.0001" id="edit_qty_usage" name="qty_usage" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold; color: #334155; display: block; margin-bottom: 5px;">Satuan</label>
                    <select id="edit_satuan" name="satuan" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; background: #ffffff;">
                        <option value="Pcs">Pcs</option>
                        <option value="Kg">Kg</option>
                        <option value="Gram">Gram</option>
                        <option value="Meter">Meter</option>
                        <option value="Roll">Roll</option>
                        <option value="Liter">Liter</option>
                        <option value="Set">Set</option>
                    </select>
                </div>
            </div>

            <div style="text-align: right; margin-top: 20px;">
                <button type="button" onclick="tutupModalEdit()" style="background: #94a3b8; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; margin-right: 5px; font-weight: bold;">Batal</button>
                <button type="submit" style="background: #eab308; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- JAVASCRIPT BAWAAN -->
<script>
    const materialMap = {
        @foreach($materials as $mat)
            "{{ $mat->no_mm }}": "{{ addslashes($mat->nama_material) }}",
        @endforeach
        @foreach($finishGoods as $fg)
            "{{ $fg->no_mm }}": "{{ addslashes($fg->nama_material) }}",
        @endforeach
    };

    function updateItemName(inputElement, targetId) {
        let val = inputElement.value.trim();
        let nameDisplay = document.getElementById(targetId);
        if (materialMap[val]) {
            nameDisplay.value = materialMap[val];
        } else {
            nameDisplay.value = val ? "Item tidak ditemukan" : "";
        }
    }

    function updateRowItemName(inputElement) {
        let val = inputElement.value.trim();
        let row = inputElement.closest('.component-row');
        let nameDisplay = row.querySelector('.row-name-display');
        if (materialMap[val]) {
            nameDisplay.value = materialMap[val];
        } else {
            nameDisplay.value = val ? "Item tidak ditemukan" : "";
        }
    }

    let rowIndex = 1;

    function addRow() {
        let container = document.getElementById('component-container');
        let firstRow = container.querySelector('.component-row');
        let newRow = firstRow.cloneNode(true);

        newRow.querySelectorAll('input').forEach(input => {
            input.value = '';
        });
        newRow.querySelector('select').selectedIndex = 0;

        let textInput = newRow.querySelector('input[type="text"]');
        let numberInput = newRow.querySelector('input[type="number"]');
        let selectSatuan = newRow.querySelector('select');

        textInput.name = `components[${rowIndex}][component_mm]`;
        numberInput.name = `components[${rowIndex}][qty_usage]`;
        selectSatuan.name = `components[${rowIndex}][satuan]`;

        let deleteBtn = newRow.querySelector('button');
        deleteBtn.style.display = 'block';

        container.appendChild(newRow);
        rowIndex++;
    }

    function removeRow(button) {
        let row = button.closest('.component-row');
        row.remove();
    }

    function bukaModalEdit(row) {
        document.getElementById('formEditBom').action = `/development/bom/update/${row.id}`;
        document.getElementById('edit_fg_display').value = row.fg_mm + ' - ' + (row.fg_name || '');
        document.getElementById('edit_comp_display').value = row.component_mm + ' - ' + (row.comp_name || '');
        document.getElementById('edit_qty_usage').value = row.qty_usage;
        document.getElementById('edit_satuan').value = row.satuan;
        document.getElementById('modalEditBom').style.display = 'flex';
    }

    function tutupModalEdit() {
        document.getElementById('modalEditBom').style.display = 'none';
    }
</script>
@endsection

<!-- SUNTIKAN CSS & JS UNTUK SELECT2 (SEARCH DROPDOWN) -->
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        color: #334155;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Mengubah ID select-fg menjadi Select2 dengan kotak pencarian
        $('#select-fg').select2({
            placeholder: "-- Semua Finished Goods --",
            width: '350px', // Lebar agar tulisan panjang terlihat jelas
            allowClear: true
        });
    });
</script>
@endpush