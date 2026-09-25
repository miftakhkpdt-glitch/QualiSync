@extends('layouts.staff-layout')

@push('styles')
<style>
    .card-table { 
        background: #ffffff; 
        padding: 30px; 
        border-radius: 12px; 
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }
    .btn-kembali { 
        background: #64748b; color: white; padding: 10px 18px; 
        text-decoration: none; border-radius: 8px; font-weight: 600; 
        display: inline-flex; align-items: center; gap: 8px; margin-bottom: 25px; transition: 0.2s; font-size: 14px;
    }
    .btn-kembali:hover { background: #475569; }
    
    .page-title { 
        margin-top: 0; border-bottom: 2px solid #3b82f6; 
        padding-bottom: 12px; color: #1e293b; font-size: 22px; margin-bottom: 20px;
    }
    
    .info-text { 
        font-size: 13px; color: #b91c1c; font-weight: 600; font-style: italic; 
        margin-bottom: 20px; background: #fef2f2; padding: 12px 15px; 
        border-left: 4px solid #ef4444; border-radius: 6px; line-height: 1.5;
    }
    
    .tabel-custom { width: 100%; border-collapse: collapse; font-size: 13px; }
    .tabel-custom th, .tabel-custom td { border: 1px solid #e2e8f0; padding: 12px 10px; text-align: center; vertical-align: middle; }
    .tabel-custom th { background-color: #f8fafc; font-weight: bold; color: #334155; text-transform: uppercase; font-size: 12px; }
    .tabel-custom tr:nth-child(even) { background-color: #f8fafc; }
    .tabel-custom tr:hover { background-color: #f1f5f9; }
    
    /* Tombol Lihat (Baru) */
    .btn-lihat { 
        background: #3b82f6; color: #ffffff; padding: 8px 12px; 
        text-decoration: none; border-radius: 6px; font-size: 12px; 
        font-weight: bold; display: inline-flex; align-items: center; gap: 5px; transition: 0.2s; border: none; 
    }
    .btn-lihat:hover { background: #2563eb; color: white; }

    .btn-edit { 
        background: #eab308; color: #ffffff; padding: 8px 12px; 
        text-decoration: none; border-radius: 6px; font-size: 12px; 
        font-weight: bold; display: inline-flex; align-items: center; gap: 5px; transition: 0.2s; border: none; 
    }
    .btn-edit:hover { background: #ca8a04; color: white; }

    .btn-hapus { 
        background: #ef4444; color: #ffffff; padding: 8px 12px; 
        text-decoration: none; border-radius: 6px; font-size: 12px; 
        font-weight: bold; display: inline-flex; align-items: center; gap: 5px; transition: 0.2s; border: none; 
    }
    .btn-hapus:hover { background: #dc2626; color: white; }
    
    .empty-row { text-align: center; padding: 30px; color: #64748b; font-style: italic; }
    
    .badge-shift { background: #e2e8f0; color: #334155; padding: 4px 10px; border-radius: 6px; font-weight: bold; font-size: 12px; }
</style>
@endpush

@section('konten')

    <div class="card-table">
        <a href="{{ url('/qir') }}" class="btn-kembali">
            <i class="fas fa-arrow-left"></i> Kembali ke Menu
        </a>

        <h3 class="page-title">
            <i class="fas fa-history" style="color: #3b82f6; margin-right: 8px;"></i> RIWAYAT INSPEKSI QUALITY INSPECTION RECORD (QIR)
        </h3>

        <!-- Kotak Keterangan Auto-Delete 2 Tahun -->
        <div class="info-text">
            <i class="fas fa-info-circle" style="margin-right: 5px;"></i> Catatan: Riwayat dokumen QIR akan dihapus secara otomatis oleh sistem apabila usianya telah melebihi 2 tahun dari tanggal pembuatan.
        </div>
        
        <!-- ========================================== -->
        <!-- FORM FILTER PENCARIAN (BARU)               -->
        <!-- ========================================== -->
        <div style="background: #f8fafc; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e2e8f0;">
            <form action="{{ url('/qir/riwayat') }}" method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; margin: 0;">
                
                <div style="flex: 1; min-width: 150px;">
                    <label style="font-size: 12px; font-weight: bold; color: #475569; display: block; margin-bottom: 5px;">TANGGAL</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; font-size: 13px;">
                </div>
                
                <div style="flex: 1; min-width: 150px;">
                    <label style="font-size: 12px; font-weight: bold; color: #475569; display: block; margin-bottom: 5px;">NO. BATCH</label>
                    <input type="text" name="no_batch" value="{{ request('no_batch') }}" placeholder="Cari No Batch..." style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; font-size: 13px;">
                </div>
                
                <div style="flex: 1; min-width: 150px;">
                    <label style="font-size: 12px; font-weight: bold; color: #475569; display: block; margin-bottom: 5px;">NO. MM</label>
                    <input type="text" name="no_mm" value="{{ request('no_mm') }}" placeholder="Cari No MM..." style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; font-size: 13px;">
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" style="background: #3b82f6; color: white; border: none; padding: 9px 18px; border-radius: 6px; cursor: pointer; font-weight: bold; display: inline-flex; align-items: center; gap: 6px; font-size: 13px; transition: 0.2s;">
                        <i class="fas fa-search"></i> Terapkan Filter
                    </button>
                    <a href="{{ url('/qir/riwayat') }}" style="background: #e2e8f0; color: #475569; text-decoration: none; padding: 9px 18px; border-radius: 6px; font-weight: bold; display: inline-flex; align-items: center; gap: 6px; font-size: 13px; transition: 0.2s;">
                        <i class="fas fa-sync-alt"></i> Reset
                    </a>
                </div>
            </form>
        </div>
        <!-- ========================================== -->

        <!-- Tabel Anda -->
        <div style="overflow-x: auto;">
            <table class="tabel-custom">
                <!-- ... header dan isi tabel Anda ... -->

        <div style="overflow-x: auto;">
            <table class="tabel-custom">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Tanggal</th>
                        <th>No. Batch</th>
                        <th>No. MM</th>
                        <th>Nama Item</th> <!-- Kolom Baru -->
                        <th>Shift</th>
                        <th>Status Edit</th>
                        <th style="width: 240px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}</td>
                        <td><strong>{{ $row->no_batch }}</strong></td>
                        <td>{{ $row->no_mm }}</td>
                        
                        <!-- Data Nama Item Baru -->
                        <td>{{ $row->nama_material ?? '-' }}</td> 
                        
                        <td><span class="badge-shift">Shift {{ $row->shift ?? '1' }}</span></td>
                        <td>
                            @if(isset($row->pernah_diedit) && $row->pernah_diedit)
                                <span style="color: #d97706; font-weight: bold;"><i class="fas fa-pen-alt"></i> Pernah Diedit</span>
                            @else
                                <span style="color: #16a34a; font-weight: bold;"><i class="fas fa-check-circle"></i> Baru</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <!-- Tombol Lihat -->
                                <a href="{{ url('/qir/detail/' . $row->id) }}" class="btn-lihat">
                                    <i class="fas fa-eye"></i> Lihat
                                </a>

                                <!-- Tombol Edit -->
                                <a href="{{ url('/qir/edit/' . $row->id) }}" class="btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>

                                <!-- Tombol Hapus (Wajib pakai Form agar aman dan sesuai Route::delete) -->
                                <form action="{{ url('/qir/delete/' . $row->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Yakin ingin menghapus seluruh riwayat QIR untuk Batch {{ $row->no_batch }} ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-hapus" style="cursor: pointer;">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <!-- Colspan diubah dari 7 menjadi 8 karena ada tambahan 1 kolom -->
                        <td colspan="8" class="empty-row"> 
                            <i class="fas fa-folder-open" style="font-size: 24px; color: #cbd5e1; display: block; margin-bottom: 10px;"></i>
                            Belum ada riwayat inspeksi QIR yang tersimpan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection