@extends('layouts.staff-layout')

@push('styles')
<style>
    .fg-container { 
        background-color: #ffffff; padding: 25px; border-radius: 8px; 
        box-shadow: 0 4px 6px rgba(0,0,0,0.05); width: 100%; max-width: 1400px; 
        margin: 0 auto; border: 1px solid #e2e8f0;
    }
    .nav-top { margin-bottom: 20px; display: flex; gap: 10px; }
    .btn-kembali { display: inline-flex; align-items: center; gap: 6px; text-decoration: none; background-color: #64748b; color: white; padding: 8px 14px; border-radius: 6px; font-size: 13px; font-weight: bold; transition: background 0.2s; }
    .btn-kembali:hover { background-color: #475569; }

    .judul-form { font-size: 18px; font-weight: 700; margin-bottom: 20px; text-transform: uppercase; color: #1e293b; }
    
    .tabel-fg { width: 100%; border-collapse: collapse; font-size: 12px; text-align: center; color: #1e293b; }
    .tabel-fg th, .tabel-fg td { border: 1px solid #cbd5e1; padding: 10px 8px; vertical-align: middle; }
    .tabel-fg thead th { font-weight: bold; background-color: #f1f5f9; font-size: 12px; color: #334155; }
    
    .badge-status { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
    .badge-ok { background-color: #dcfce7; color: #16a34a; }
    .badge-reject { background-color: #fee2e2; color: #dc2626; }

    .btn-aksi { padding: 6px 10px; background-color: #2563eb; color: white; border-radius: 4px; text-decoration: none; font-size: 12px; display: inline-flex; align-items: center; justify-content: center; transition: 0.2s; }
    .btn-aksi:hover { background-color: #1d4ed8; color: white; }

    /* Tambahan style untuk tombol edit dan hapus */
    .btn-edit { background-color: #f59e0b; }
    .btn-edit:hover { background-color: #d97706; }
    
    .btn-delete { background-color: #ef4444; border: none; cursor: pointer; margin: 0; }
    .btn-delete:hover { background-color: #dc2626; }
    
    .aksi-group { display: flex; gap: 6px; justify-content: center; align-items: center; }
</style>
@endpush

@section('konten')
<div class="fg-container">
    <div class="nav-top">
        <a href="{{ url('/in-proses/fg/menu') }}" class="btn-kembali">
            <i class="fas fa-arrow-left"></i> Kembali ke Menu
        </a>
    </div>

    <div class="judul-form"><i class="fas fa-history" style="color: #16a34a; margin-right: 8px;"></i> Riwayat Data Inspeksi Finished Goods (FG)</div>

    <!-- Menampilkan Alert Success/Error jika ada -->
    @if(session('success'))
        <div style="background-color: #dcfce7; color: #16a34a; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; border-left: 4px solid #16a34a;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background-color: #fee2e2; color: #dc2626; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; border-left: 4px solid #dc2626;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <table class="tabel-fg">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No. Batch</th>
                <th>No. MM</th>
                <th>Nama Item</th>
                <th>Shift</th>
                <th>Line Produksi</th>
                <th>Tipe Inspeksi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayat as $index => $data)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($data->tanggal)->format('d-m-Y') }}</td>
                    <td>{{ $data->no_batch ?? '-' }}</td>
                    <td>{{ $data->no_mm ?? '-' }}</td>
                    
                    <!-- Diperbarui agar membaca variabel hasil join controller -->
                    <td style="text-align: left;">{{ $data->nama_material ?? $data->nama_item ?? '-' }}</td>
                    
                    <td>{{ $data->shift ?? '-' }}</td>
                    
                    <!-- Diperbarui agar membaca kolom line_produksi atau line -->
                    <td>{{ $data->line_produksi ?? $data->line ?? '-' }}</td>
                    
                    <td>
                        @if($data->inspection_type == 'Normal Inspection')
                            <span style="background: #e0e7ff; color: #4f46e5; padding: 4px 8px; border-radius: 4px; font-weight: bold;">Normal</span>
                        @else
                            <span style="background: #fef08a; color: #854d0e; padding: 4px 8px; border-radius: 4px; font-weight: bold;">Tightened</span>
                        @endif
                    </td>
                    <td>
                        <div class="aksi-group">
                            <!-- Tombol Lihat -->
                            <a href="{{ url('/in-proses/fg/detail/' . $data->id) }}" class="btn-aksi" title="Lihat / Cetak">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <!-- Tombol Edit -->
                            <a href="{{ url('/in-proses/fg/edit/' . $data->id) }}" class="btn-aksi btn-edit" title="Edit Data">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <!-- Tombol Hapus -->
                            <form action="{{ url('/in-proses/fg/delete/' . $data->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Yakin ingin menghapus data inspeksi ini beserta seluruh detail temuan di dalamnya? Data yang dihapus tidak dapat dikembalikan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-aksi btn-delete" title="Hapus Data">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="padding: 30px; color: #64748b; font-style: italic;">
                        Belum ada riwayat data inspeksi Finished Goods yang tersimpan. Silakan lakukan input data baru melalui menu utama.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection