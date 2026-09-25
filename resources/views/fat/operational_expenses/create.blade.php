@extends('layouts.staff-layout')

@section('konten')
<div style="padding: 30px; background-color: #f8fafc; min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-size: 22px; font-weight: bold; color: #1e293b; margin: 0 0 5px 0;">
                <i class="fas fa-edit" style="color: #f59e0b; margin-right: 8px;"></i> Input Pengeluaran Baru
            </h2>
        </div>
        <a href="{{ route('fat.operational_expenses.index') }}" style="background-color: #e2e8f0; color: #475569; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div style="background: white; border-radius: 10px; padding: 30px; max-width: 600px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <form action="{{ route('fat.operational_expenses.store') }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 20px;">
                <label style="font-size: 13px; font-weight: bold; color: #64748b; display: block; margin-bottom: 8px;">Tanggal Pengeluaran *</label>
                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; font-family: inherit;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="font-size: 13px; font-weight: bold; color: #64748b; display: block; margin-bottom: 8px;">Kategori *</label>
                <select name="kategori" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; background-color: white;">
                    <option value="HRD / Gaji">HRD / Gaji</option>
                    <option value="Utilitas (Listrik/Air/Internet)">Utilitas (Listrik/Air/Internet)</option>
                    <option value="Transportasi / Bensin">Transportasi / Bensin</option>
                    <option value="Konsumsi / Rapat">Konsumsi / Rapat</option>
                    <option value="Perlengkapan Kantor (ATK)">Perlengkapan Kantor (ATK)</option>
                    <option value="Lain-lain">Lain-lain</option>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="font-size: 13px; font-weight: bold; color: #64748b; display: block; margin-bottom: 8px;">Nama / Deskripsi Biaya *</label>
                <input type="text" name="nama_biaya" placeholder="Contoh: Beli token listrik bulan Agustus" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="font-size: 13px; font-weight: bold; color: #64748b; display: block; margin-bottom: 8px;">Nominal (Rp) *</label>
                <input type="number" name="nominal" placeholder="0" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 18px; font-weight: bold; color: #dc2626; outline: none;">
            </div>

            <div style="margin-bottom: 30px;">
                <label style="font-size: 13px; font-weight: bold; color: #64748b; display: block; margin-bottom: 8px;">Keterangan Tambahan (Opsional)</label>
                <textarea name="keterangan" rows="3" placeholder="Catatan tambahan..." style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; font-family: inherit;"></textarea>
            </div>

            <button type="submit" style="width: 100%; background-color: #f59e0b; color: white; border: none; padding: 14px; border-radius: 6px; font-size: 15px; font-weight: bold; cursor: pointer; transition: 0.3s;" onmouseover="this.style.backgroundColor='#d97706'" onmouseout="this.style.backgroundColor='#f59e0b'">
                <i class="fas fa-save" style="margin-right: 5px;"></i> Simpan Pengeluaran
            </button>
        </form>
    </div>
</div>
@endsection