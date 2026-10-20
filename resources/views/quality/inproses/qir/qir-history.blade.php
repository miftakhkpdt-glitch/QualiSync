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

    .filter-toolbar {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-bottom: 12px;
    }
    .filter-toolbar button,
    .filter-toolbar a {
        border: 0;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: bold;
        text-decoration: none;
        cursor: pointer;
    }
    .filter-toolbar button { background: #3b82f6; color: #fff; }
    .filter-toolbar a { background: #e2e8f0; color: #475569; }
    .filter-row th { background: #fff; text-transform: none; }
    .filter-input {
        width: 100%;
        min-width: 95px;
        box-sizing: border-box;
        padding: 7px 8px;
        border: 1px solid #cbd5e1;
        border-radius: 5px;
        background: #fff;
        color: #334155;
        font: inherit;
        font-weight: normal;
        text-transform: none;
    }
    .filter-date-range { display: grid; gap: 5px; min-width: 130px; }
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
        
        <form id="qir-filters" action="{{ url('/qir/riwayat') }}" method="GET">
            <div class="filter-toolbar">
                <button type="submit"><i class="fas fa-search"></i> Terapkan Filter</button>
                <a href="{{ url('/qir/riwayat') }}"><i class="fas fa-sync-alt"></i> Reset</a>
            </div>
        </form>
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
                        <th>Line</th>
                        <th>Status Edit</th>
                        @if($canSeeOutOfStandard)
                        <th>Hasil QIR</th>
                        @endif
                        <th style="width: 240px;">Aksi</th>
                    </tr>
                    <tr class="filter-row">
                        <th></th>
                        <th>
                            <div class="filter-date-range">
                                <input class="filter-input" form="qir-filters" type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" aria-label="Tanggal dari" title="Tanggal dari">
                                <input class="filter-input" form="qir-filters" type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" aria-label="Tanggal sampai" title="Tanggal sampai">
                            </div>
                        </th>
                        <th><input class="filter-input" form="qir-filters" type="search" name="no_batch" value="{{ request('no_batch') }}" placeholder="Cari batch..." aria-label="Filter No. Batch"></th>
                        <th><input class="filter-input" form="qir-filters" type="search" name="no_mm" value="{{ request('no_mm') }}" placeholder="Cari No. MM..." aria-label="Filter No. MM"></th>
                        <th><input class="filter-input" form="qir-filters" type="search" name="nama_item" value="{{ request('nama_item') }}" placeholder="Cari nama item..." aria-label="Filter nama item"></th>
                        <th>
                            <select class="filter-input" form="qir-filters" name="shift" aria-label="Filter shift">
                                <option value="">Semua</option>
                                @foreach($shiftOptions as $shift)
                                    <option value="{{ $shift }}" @selected(request('shift') === $shift)>{{ $shift }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            <select class="filter-input" form="qir-filters" name="line" aria-label="Filter line">
                                <option value="">Semua</option>
                                @foreach($lineOptions as $line)
                                    <option value="{{ $line }}" @selected(request('line') === $line)>{{ $line }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            <select class="filter-input" form="qir-filters" name="status_edit" aria-label="Filter status edit">
                                <option value="">Semua</option>
                                <option value="baru" @selected(request('status_edit') === 'baru')>Baru</option>
                                <option value="diedit" @selected(request('status_edit') === 'diedit')>Pernah Diedit</option>
                            </select>
                        </th>
                        @if($canSeeOutOfStandard)
                        <th>
                            <select class="filter-input" form="qir-filters" name="hasil_qir" aria-label="Filter hasil QIR">
                                <option value="">Semua</option>
                                <option value="di_luar_standar" @selected(request('hasil_qir') === 'di_luar_standar')>Di Luar Standar</option>
                                <option value="sesuai_standar" @selected(request('hasil_qir') === 'sesuai_standar')>Sesuai Standar</option>
                            </select>
                        </th>
                        @endif
                        <th></th>
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
                        
                        <td><span class="badge-shift">{{ $row->shift ?? 'Shift 1' }}</span></td>
                        <td>{{ $row->line_produksi ?? '-' }}</td>
                        <td>
                            @if(isset($row->pernah_diedit) && $row->pernah_diedit)
                                <span style="color: #d97706; font-weight: bold;"><i class="fas fa-pen-alt"></i> Pernah Diedit</span>
                            @else
                                <span style="color: #16a34a; font-weight: bold;"><i class="fas fa-check-circle"></i> Baru</span>
                            @endif
                        </td>
                        @if($canSeeOutOfStandard)
                        <td>
                            @if($row->ada_nilai_di_luar_standar ?? false)
                                <span style="color: #b91c1c; font-weight: bold;" title="Ada hasil inspeksi di luar standar">
                                    <i class="fas fa-exclamation-triangle"></i> Di Luar Standar
                                </span>
                            @else
                                <span style="color: #16a34a; font-weight: bold;">
                                    <i class="fas fa-check-circle"></i> Sesuai Standar
                                </span>
                            @endif
                        </td>
                        @endif
                        <td>
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <!-- Tombol Lihat -->
                                <a href="{{ url('/qir/detail/' . $row->id) }}" class="btn-lihat">
                                    <i class="fas fa-eye"></i> Lihat
                                </a>

                                @if(in_array(strtolower(auth()->user()->role ?? ''), ['admin', 'staff_quality'], true))
                                <a href="{{ url('/qir/edit/' . $row->id) }}" class="btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @endif

                                @if(strtolower(auth()->user()->role ?? '') === 'admin')
                                <form action="{{ url('/qir/delete/' . $row->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Yakin ingin menghapus seluruh riwayat QIR untuk Batch {{ $row->no_batch }} ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-hapus" style="cursor: pointer;">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <!-- Colspan menyesuaikan jumlah kolom tabel -->
                        <td colspan="{{ $canSeeOutOfStandard ? 10 : 9 }}" class="empty-row">
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