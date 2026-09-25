@extends('layouts.staff-layout')

@section('title', 'Master Defect - PT KIMPAI DYNA TUBE')

@section('konten')
<div style="padding: 20px; max-width: 1000px; margin: 0 auto;">
    
    <!-- Header Judul Halaman -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 style="font-size: 24px; color: #334155; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-exclamation-triangle" style="color: #0ea5e9;"></i> Master Defect
            </h2>
            <p style="color: #64748b; font-size: 14px; margin: 5px 0 0 0;">Kelola daftar jenis kerusakan (defect) secara simpel dan terpusat.</p>
        </div>
        <a href="{{ url('/dashboard-qa') }}" style="background: #64748b; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Layout Split Kanan-Kiri -->
    <div style="display: flex; gap: 20px; flex-wrap: wrap; align-items: flex-start;">
        
        <!-- Kolom Kiri: Form Tambah Defect -->
        <div style="flex: 1; min-width: 300px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-top: 4px solid #0ea5e9;">
            <h5 style="font-size: 16px; font-weight: bold; color: #334155; margin-bottom: 20px;">
                <i class="fas fa-plus-circle text-primary mr-1"></i> Tambah Defect Baru
            </h5>

            <form action="{{ route('master-defect.store') }}" method="POST">
                @csrf
                
                <div class="form-group mb-3">
                    <label class="text-dark" style="font-weight: 500; font-size: 13px;">NAMA DEFECT / KERUSAKAN</label>
                    <input type="text" name="nama_defect" class="form-control" placeholder="Masukkan nama kerusakan..." required>
                </div>

                <div class="form-group mb-4">
                    <label class="text-dark" style="font-weight: 500; font-size: 13px;">KATEGORI</label>
                    <select name="kategori" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Critical">Critical</option>
                        <option value="Major">Major</option>
                        <option value="Minor">Minor</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="background: #0ea5e9; border: none; font-weight: bold; width: 100%;">
                    <i class="fas fa-save mr-1"></i> Simpan Defect
                </button>
            </form>
        </div>

        <!-- Kolom Kanan: Daftar Tabel Defect -->
        <div style="flex: 2; min-width: 400px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-top: 4px solid #0ea5e9;">
            <h5 style="font-size: 16px; font-weight: bold; color: #334155; margin-bottom: 20px;">
                Daftar Master Defect
            </h5>

            <div class="table-responsive">
                <table class="table table-bordered mb-0" style="font-size: 14px; width: 100%;">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 10%; text-align: left; padding: 10px;">No</th>
                            <th style="width: 50%; text-align: left; padding: 10px;">Nama Defect</th>
                            <th style="width: 20%; text-align: left; padding: 10px;">Kategori</th>
                            <th style="width: 20%; text-align: center; padding: 10px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($defects as $index => $defect)
                        <tr>
                            <td style="width: 10%; text-align: left; padding: 10px; vertical-align: middle;">{{ $index + 1 }}</td>
                            <td style="width: 50%; text-align: left; padding: 10px; vertical-align: middle; font-weight: 500;">{{ $defect->nama_defect }}</td>
                            <td style="width: 20%; text-align: left; padding: 10px; vertical-align: middle;">
                                <span style="background: #e0f2fe; color: #0369a1; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">
                                    {{ $defect->kategori }}
                                </span>
                            </td>
                            <td style="width: 20%; text-align: center; padding: 10px; vertical-align: middle;">
                                <form action="{{ route('master-defect.destroy', $defect->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 4px 8px; font-size: 12px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #64748b; font-style: italic; padding: 20px;">
                                Belum ada data master defect yang tersimpan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection