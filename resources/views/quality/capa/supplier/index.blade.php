@extends('layouts.staff-layout')

@push('styles')
<style>
    .fg-container { background-color: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); width: 100%; max-width: 1400px; margin: 0 auto; border: 1px solid #e2e8f0; }
    .judul-form { font-size: 18px; font-weight: 700; margin-bottom: 20px; text-transform: uppercase; color: #1e293b; }
    .tabel-fg { width: 100%; border-collapse: collapse; font-size: 12px; text-align: center; color: #1e293b; }
    .tabel-fg th, .tabel-fg td { border: 1px solid #cbd5e1; padding: 10px 8px; vertical-align: middle; }
    .tabel-fg thead th { font-weight: bold; background-color: #f1f5f9; font-size: 12px; color: #334155; }
    
    .badge-status { padding: 5px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; display: inline-block; }
    .badge-open { background-color: #fee2e2; color: #dc2626; }
    .badge-review { background-color: #fef3c7; color: #d97706; }
    .badge-closed { background-color: #dcfce7; color: #16a34a; }

    .btn-aksi { padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 11px; font-weight: bold; display: inline-flex; align-items: center; gap: 4px; border: none; cursor: pointer; }
    .btn-edit { background-color: #eab308; color: white; }
    .btn-close-action { background-color: #10b981; color: white; }
    .btn-open-action { background-color: #64748b; color: white; }
    
    .btn-tambah { background-color: #d97706; color: white; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 20px; transition: background 0.2s; }
    .btn-tambah:hover { background-color: #b45309; }
</style>
@endpush

@section('konten')
<div class="fg-container">
    <div class="judul-form">
        <i class="fas fa-file-alt" style="color: #198754; margin-right: 8px;"></i> Corrective and Preventive Action (CAPA 8D)
    </div>

    <!-- Tombol Buat Dokumen CAPA -->
    <a href="{{ url('/capa-8d/supplier/create') }}" class="btn-tambah">
        <i class="fas fa-plus-circle"></i> Buat Dokumen CAPA
    </a>

    <table class="tabel-fg">
        <thead>
            <tr>
                <th>No</th>
                <th>No. CAPA</th>
                <th>Judul Masalah</th>
                <th>Supplier</th>
                <th>Tanggal Terbit</th>
                <th>Status</th>
                <th>File Supplier</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($capas ?? [] as $index => $data)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $data->no_capa ?? '-' }}</td>
                    <td>{{ $data->tema_masalah ?? '-' }}</td>
                    <td>{{ $data->nama_supplier ?? '-' }}</td>
                    <td>{{ $data->tanggal_temuan ?? '-' }}</td>
                    <td>
                        @if($data->status == 'Open')
                            <span class="badge-status badge-open">Open</span>
                        @elseif($data->status == 'Review' || $data->status == 'Waiting Review')
                            <span class="badge-status badge-review">Review</span>
                        @else
                            <span class="badge-status badge-closed">Closed</span>
                        @endif
                    </td>
                    
                    {{-- Tombol Lihat Data Isian --}}
                    <td>
                        @if($data->status == 'Review' || $data->status == 'Closed')
                            <a href="{{ url('/capa-8d/supplier/print/' . $data->id) }}" target="_blank" class="btn-aksi" style="background-color: #0284c7; color: white;">
                                <i class="fas fa-eye"></i> Lihat Data Isian
                            </a>
                        @else
                            <span style="color: #94a3b8; font-style: italic;">Belum diisi</span>
                        @endif
                    </td>

                    <td>
                        @if($data->status != 'Closed')
                            <a href="{{ url('/capa-8d/supplier/edit/' . $data->id) }}" class="btn-aksi btn-edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        @endif

                        <a href="{{ url('/capa-8d/supplier/toggle-close/' . $data->id) }}" class="btn-aksi {{ $data->status == 'Closed' ? 'btn-open-action' : 'btn-close-action' }}" title="Ubah Status Close/Open" onclick="return confirm('Ubah status CAPA ini?')">
                            <i class="fas {{ $data->status == 'Closed' ? 'fa-folder-open' : 'fa-check-circle' }}"></i> {{ $data->status == 'Closed' ? 'Open' : 'Close' }}
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="padding: 30px; color: #64748b; font-style: italic;">
                        Belum ada data CAPA Supplier yang tersedia.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection