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

    @if(session('success'))
        <div role="status" style="background: #d1fae5; color: #065f46; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div role="alert" style="background: #fee2e2; color: #991b1b; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px;">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

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
                        <option value="Intolerance">Intolerance</option>
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
                                <button type="button" class="edit-defect-button btn btn-primary btn-sm" data-update-url="{{ route('master-defect.update', $defect->id) }}" data-defect-id="{{ $defect->id }}" data-nama="{{ $defect->nama_defect }}" data-kategori="{{ $defect->kategori }}" aria-label="Edit {{ $defect->nama_defect }}" title="Edit defect" style="padding: 4px 8px; font-size: 12px; margin-right: 4px;">
                                    <i class="fas fa-edit"></i>
                                </button>
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

    <dialog id="edit-defect-dialog" style="width: min(440px, calc(100% - 32px)); border: 1px solid #cbd5e1; border-radius: 8px; padding: 24px;">
        <h5 style="font-size: 18px; margin: 0 0 20px; color: #334155;">Edit Master Defect</h5>
        <form id="edit-defect-form" method="POST" style="display: grid; gap: 14px;">
            @csrf
            @method('PUT')
            <input type="hidden" name="defect_id" id="edit-defect-id">
            <label style="font-size: 13px; font-weight: 600; color: #334155;">Nama Defect / Kerusakan
                <input type="text" name="nama_defect" id="edit-defect-name" value="{{ old('nama_defect') }}" required maxlength="255" class="form-control" style="margin-top: 6px;">
            </label>
            <label style="font-size: 13px; font-weight: 600; color: #334155;">Kategori
                <select name="kategori" id="edit-defect-category" required class="form-control" style="margin-top: 6px;">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Critical" @selected(old('kategori') === 'Critical')>Critical</option>
                    <option value="Major" @selected(old('kategori') === 'Major')>Major</option>
                    <option value="Minor" @selected(old('kategori') === 'Minor')>Minor</option>
                    <option value="Intolerance" @selected(old('kategori') === 'Intolerance')>Intolerance</option>
                </select>
            </label>
            <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 8px;">
                <button type="button" id="cancel-edit-defect" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary" style="background: #0ea5e9; border-color: #0ea5e9;">Simpan Perubahan</button>
            </div>
        </form>
    </dialog>
    <style>
        #edit-defect-dialog::backdrop { background: rgba(15, 23, 42, 0.45); }
    </style>
    <script>
        const editDefectDialog = document.getElementById('edit-defect-dialog');
        const editDefectForm = document.getElementById('edit-defect-form');

        document.addEventListener('click', (event) => {
            const button = event.target.closest('.edit-defect-button');
            if (!button) return;

            editDefectForm.action = button.dataset.updateUrl;
            document.getElementById('edit-defect-id').value = button.dataset.defectId;
            document.getElementById('edit-defect-name').value = button.dataset.nama;
            document.getElementById('edit-defect-category').value = button.dataset.kategori;
            editDefectDialog.showModal();
        });

        document.getElementById('cancel-edit-defect').addEventListener('click', () => editDefectDialog.close());

        @if($errors->any() && old('defect_id'))
            editDefectForm.action = @json(route('master-defect.update', old('defect_id')));
            editDefectDialog.showModal();
        @endif
    </script>
</div>
@endsection