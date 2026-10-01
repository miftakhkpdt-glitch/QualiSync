@extends('layouts.staff-layout')

@section('konten')
<div style="padding: 20px;">
    <h3><i class="fas fa-boxes"></i> Master Material (MM) & Finish Goods</h3>
    <p style="color: #64748b;">Pusat pengelolaan nomor MM material dan produk oleh Departemen Development.</p>

    @if(session('success'))
        <div style="background: #d1fae5; color: #065f46; padding: 10px; margin-top: 10px; border-radius: 5px;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: #fee2e2; color: #991b1b; padding: 10px; margin-top: 10px; border-radius: 5px;">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background: #fee2e2; color: #991b1b; padding: 10px; margin-top: 10px; border-radius: 5px;">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <!-- Form Tambah Master Material -->
    <div style="background: #ffffff; padding: 20px; margin-top: 20px; border: 1px solid #e2e8f0; border-radius: 8px;">
        <h4>Tambah Master Material Baru</h4>
        <form action="{{ route('development.master-material.store') }}" method="POST" style="display: flex; gap: 10px; margin-top: 15px; flex-wrap: wrap;">
            @csrf
            <input type="text" name="no_mm" placeholder="No. MM (Contoh: MM-001)" required style="padding: 8px; flex: 1; min-width: 180px;">
            <input type="text" name="nama_material" placeholder="Nama Material / Produk" required style="padding: 8px; flex: 2; min-width: 220px;">
            <select name="kategori" required style="padding: 8px; flex: 1; min-width: 150px;">
                <option value="">Pilih Kategori</option>
                <option value="Raw Material">Raw Material</option>
                <option value="Finish Good">Finish Good</option>
                <option value="Packaging">Packaging Material</option>
                <option value="CSM">Customer Supplied Material (CSM)</option>
            </select>
            
            <select name="satuan" required style="padding: 8px; flex: 1; min-width: 140px; background: #ffffff;">
                <option value="">Pilih Satuan</option>
                <option value="Pcs">Pcs</option>
                <option value="Kg">Kg</option>
                <option value="Gram">Gram</option>
                <option value="Meter">Meter</option>
                <option value="Roll">Roll</option>
                <option value="Liter">Liter</option>
                <option value="Set">Set</option>
            </select>

            <button type="submit" style="background: #2563eb; color: white; border: none; padding: 8px 20px; cursor: pointer; border-radius: 4px; font-weight: 600;">Simpan MM</button>
        </form>
    </div>

    <!-- Tabel Daftar Master Material (DIPERBARUI DENGAN DATATABLES) -->
    <div style="background: #ffffff; padding: 20px; margin-top: 20px; border: 1px solid #e2e8f0; border-radius: 8px;">
        <table class="datatable" border="1" style="width: 100%; border-collapse: collapse; background: #fff;">
            <thead style="background: #f1f5f9; text-align: left;">
                <tr>
                    <th style="padding: 10px;">No. MM</th>
                    <th style="padding: 10px;">Nama Material / Produk</th>
                    <th style="padding: 10px;">Kategori</th>
                    <th style="padding: 10px;">Satuan</th>
                    <th style="padding: 10px; width: 100px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($materials as $mat)
                <tr>
                    <td style="padding: 10px;"><b>{{ $mat->no_mm }}</b></td>
                    <td style="padding: 10px;">{{ $mat->nama_material }}</td>
                    <td style="padding: 10px;">
                        <span style="padding: 3px 8px; border-radius: 4px; font-size: 12px; background: {{ $mat->kategori == 'Raw Material' ? '#e0f2fe; color: #0369a1;' : '#dcfce7; color: #15803d;' }}">
                            {{ $mat->kategori }}
                        </span>
                    </td>
                    <td style="padding: 10px;">{{ $mat->satuan }}</td>
                    <td style="padding: 10px; text-align: center;">
                        <button type="button" class="edit-material-button" data-update-url="{{ route('development.master-material.update', $mat->id) }}" data-no-mm="{{ $mat->no_mm }}" data-nama-material="{{ $mat->nama_material }}" data-kategori="{{ $mat->kategori }}" data-satuan="{{ $mat->satuan }}" style="background-color: #2563eb; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; margin-right: 4px;">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <form action="{{ url('/development/master-material/' . $mat->id) }}" method="POST" style="display:inline-block;" 
                              onsubmit="return confirm('PERINGATAN!\n\nYakin ingin menghapus Material ini? Data tidak bisa dikembalikan.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="no-disable" style="background-color: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <dialog id="edit-material-dialog" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 24px; width: min(560px, calc(100% - 32px));">
        <h4 style="margin-top: 0;">Edit Master Material</h4>
        <form id="edit-material-form" method="POST" style="display: grid; gap: 12px;">
            @csrf
            @method('PUT')
            <input type="hidden" name="material_id" id="edit-material-id">
            <label>No. MM
                <input type="text" name="no_mm" id="edit-no-mm" value="{{ old('no_mm') }}" required style="display: block; box-sizing: border-box; width: 100%; padding: 8px; margin-top: 4px;">
            </label>
            <label>Nama Material / Produk
                <input type="text" name="nama_material" id="edit-nama-material" value="{{ old('nama_material') }}" required style="display: block; box-sizing: border-box; width: 100%; padding: 8px; margin-top: 4px;">
            </label>
            <label>Kategori
                <select name="kategori" id="edit-kategori" required style="display: block; box-sizing: border-box; width: 100%; padding: 8px; margin-top: 4px;">
                    <option value="">Pilih Kategori</option>
                    <option value="Raw Material" @selected(old('kategori') === 'Raw Material')>Raw Material</option>
                    <option value="Finish Good" @selected(old('kategori') === 'Finish Good')>Finish Good</option>
                    <option value="Packaging" @selected(old('kategori') === 'Packaging')>Packaging Material</option>
                    <option value="CSM" @selected(old('kategori') === 'CSM')>Customer Supplied Material (CSM)</option>
                </select>
            </label>
            <label>Satuan
                <select name="satuan" id="edit-satuan" required style="display: block; box-sizing: border-box; width: 100%; padding: 8px; margin-top: 4px;">
                    <option value="">Pilih Satuan</option>
                    <option value="Pcs" @selected(old('satuan') === 'Pcs')>Pcs</option>
                    <option value="Kg" @selected(old('satuan') === 'Kg')>Kg</option>
                    <option value="Gram" @selected(old('satuan') === 'Gram')>Gram</option>
                    <option value="Meter" @selected(old('satuan') === 'Meter')>Meter</option>
                    <option value="Roll" @selected(old('satuan') === 'Roll')>Roll</option>
                    <option value="Liter" @selected(old('satuan') === 'Liter')>Liter</option>
                    <option value="Set" @selected(old('satuan') === 'Set')>Set</option>
                </select>
            </label>
            <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 8px;">
                <button type="button" id="close-edit-material" style="background: #e2e8f0; border: none; padding: 8px 14px; border-radius: 4px; cursor: pointer;">Batal</button>
                <button type="submit" style="background: #2563eb; color: white; border: none; padding: 8px 14px; border-radius: 4px; cursor: pointer;">Simpan Perubahan</button>
            </div>
        </form>
    </dialog>

    <style>
        #edit-material-dialog::backdrop { background: rgba(15, 23, 42, 0.45); }
    </style>
    <script>
        const editMaterialDialog = document.getElementById('edit-material-dialog');
        const editMaterialForm = document.getElementById('edit-material-form');

        document.addEventListener('click', (event) => {
            const button = event.target.closest('.edit-material-button');
            if (!button) return;

            editMaterialForm.action = button.dataset.updateUrl;
            document.getElementById('edit-material-id').value = button.dataset.updateUrl.split('/').pop();
            document.getElementById('edit-no-mm').value = button.dataset.noMm;
            document.getElementById('edit-nama-material').value = button.dataset.namaMaterial;
            document.getElementById('edit-kategori').value = button.dataset.kategori;
            document.getElementById('edit-satuan').value = button.dataset.satuan;
            editMaterialDialog.showModal();
        });

        document.getElementById('close-edit-material').addEventListener('click', () => editMaterialDialog.close());

        @if($errors->any() && old('material_id'))
            editMaterialForm.action = @json(route('development.master-material.update', old('material_id')));
            editMaterialDialog.showModal();
        @endif
    </script>
</div>
@endsection