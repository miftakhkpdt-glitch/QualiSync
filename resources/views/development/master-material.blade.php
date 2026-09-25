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
</div>
@endsection