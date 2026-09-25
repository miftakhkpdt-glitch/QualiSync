@extends('layouts.staff-layout')

@section('title', 'Master Parameter QIR - PT KIMPAI DYNA TUBE')

@section('konten')
<div style="padding: 20px; max-width: 1000px; margin: 0 auto;">
    
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="color: #1e293b; margin: 0;"><i class="fas fa-sliders-h" style="color: #3b82f6;"></i> Master Parameter QIR</h2>
            <p style="color: #64748b; font-size: 14px; margin: 5px 0 0 0;">Kelola jenis pengujian atau parameter ukur yang tersedia untuk seluruh produk.</p>
        </div>
        <a href="{{ url('/dashboard-qa') }}" style="background: #64748b; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
    <i class="fas fa-arrow-left"></i> Kembali
</a>
    </div>

    @if(session('success'))
        <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: 500;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
        
        <!-- KOLOM KIRI: FORM TAMBAH PARAMETER -->
        <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; height: fit-content;">
            <h3 style="margin-top: 0; font-size: 16px; color: #1e293b; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                <i class="fas fa-plus-circle" style="color: #10b981;"></i> Tambah Parameter Baru
            </h3>

            <form action="{{ url('/master-parameters/store') }}" method="POST">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: 600; font-size: 12px; display: block; margin-bottom: 5px; color: #334155;">Nama Parameter *</label>
                    <input type="text" name="nama_parameter" class="form-control" placeholder="Cth: Ketebalan, Warna, dll" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="font-weight: 600; font-size: 12px; display: block; margin-bottom: 5px; color: #334155;">Tipe Input *</label>
                    <select name="tipe_input" class="form-control" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;" required>
                        <option value="Angka">Angka (Nilai Min & Max)</option>
                        <option value="Teks">Teks/Visual (OK / NG)</option>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="font-weight: 600; font-size: 12px; display: block; margin-bottom: 5px; color: #334155;">Satuan (UOM)</label>
                    <input type="text" name="satuan" class="form-control" placeholder="Cth: mm, gr, %, dll (Opsional)" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>

                <button type="submit" style="width: 100%; background: #10b981; color: white; padding: 10px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
                    <i class="fas fa-save"></i> Simpan Parameter
                </button>
            </form>
        </div>

        <!-- KOLOM KANAN: TABEL DAFTAR PARAMETER -->
        <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
            <h3 style="margin-top: 0; font-size: 16px; color: #1e293b; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                <i class="fas fa-list" style="color: #3b82f6;">}</i> Daftar Parameter Aktif
            </h3>

            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;">
                        <th style="padding: 8px; width: 10%;">No</th>
                        <th style="padding: 8px; width: 40%;">Nama Parameter</th>
                        <th style="padding: 8px; width: 25%;">Tipe Input</th>
                        <th style="padding: 8px; width: 15%;">Satuan</th>
                        <th style="padding: 8px; width: 10%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($parameters as $index => $param)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 8px;">{{ $index + 1 }}</td>
                            <td style="padding: 8px; font-weight: bold; color: #1e293b;">{{ $param->nama_parameter }}</td>
                            <td style="padding: 8px;">
                                <span style="background: {{ $param->tipe_input == 'Angka' ? '#e0f2fe' : '#fef3c7' }}; color: {{ $param->tipe_input == 'Angka' ? '#0369a1' : '#b45309' }}; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">
                                    {{ $param->tipe_input }}
                                </span>
                            </td>
                            <td style="padding: 8px;">{{ $param->satuan ?? '-' }}</td>
                            <td style="padding: 8px; text-align: center;">
                                <form action="{{ url('/master-parameters/delete/' . $param->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus parameter ini?')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: #ef4444; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 11px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection