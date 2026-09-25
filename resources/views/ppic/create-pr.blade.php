@extends('layouts.staff-layout')

@section('title', 'Buat Purchase Request (PR)')

@section('konten')
<div style="max-width: 600px; margin: 20px auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    <h3 style="margin-bottom: 20px; color: #334155; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
        <i class="fas fa-file-signature" style="color: #8b5cf6;"></i> Form Purchase Request (PR)
    </h3>

    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-size: 13px; color: #166534;">
        <i class="fas fa-info-circle"></i> <b>Dokumen Internal:</b> Request material ini akan masuk ke antrean Departemen Purchasing. Kebutuhan minimum dari sistem: <b>{{ request('qty') }} {{ $satuan ?? 'Unit' }}</b>
    </div>

    <!-- Arahkan ke rute POST untuk menyimpan PR -->
    <form action="{{ url('/ppic/store-pr') }}" method="POST">
        @csrf
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #334155;">Nomor Material (MM)</label>
            <input type="text" name="no_mm" value="{{ request('no_mm') }}" readonly 
                   style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; background: #f1f5f9; color: #64748b; font-weight: bold;">
        </div>
        <!-- TAMBAHKAN KOTAK NAMA MATERIAL DI SINI -->
<div style="margin-bottom: 15px;">
    <label style="font-weight: bold; color: #475569;">Nama Material / Item</label>
    <input type="text" value="{{ $nama_material ?? '' }}" readonly 
           style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; background-color: #f8fafc; color: #64748b; font-weight: bold;">
</div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #334155;">Jumlah Request (Qty) - Bisa Diedit</label>
            <input type="number" name="qty" value="{{ request('qty') }}" required min="{{ request('qty') }}"
                   style="width: 100%; padding: 10px; border: 2px solid #8b5cf6; border-radius: 4px; font-size: 16px; font-weight: bold; color: #1e293b;">
        </div>
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #334155;">Tanggal Estimasi Tiba (Kebutuhan Pabrik)</label>
            <input type="date" name="estimasi_tiba" required
                   style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px; color: #1e293b;">
        </div>
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #334155;">Kategori PR</label>
            <select name="kategori" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px; background: white;">
                <option value="">-- Pilih Kategori --</option>
                <option value="M">M - Material</option>
                <option value="S">S - Sparepart</option>
                <option value="J">J - Jasa</option>
                <option value="O">O - Other</option>
                <option value="T">T - Trial</option>
                <option value="AU">AU - Alat Ukur</option>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #334155;">Keterangan / Catatan Tambahan</label>
            <textarea name="catatan" rows="3" placeholder="Tulis keterangan untuk Purchasing / Plan Manager di sini..." 
                      style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px;"></textarea>
        </div>

        <button type="submit" 
                style="background: #8b5cf6; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; font-size: 14px; transition: background 0.2s;">
            <i class="fas fa-paper-plane"></i> Kirim PR ke Purchasing
        </button>
    </form>
</div>
@endsection