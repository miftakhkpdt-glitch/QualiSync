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

    .btn-aksi { padding: 6px 14px; border-radius: 6px; text-decoration: none; font-size: 11.5px; font-weight: bold; display: inline-flex; align-items: center; gap: 6px; border: none; cursor: pointer; }
    .btn-isi { background-color: #f59e0b; color: white; }
    .btn-isi:hover { background-color: #d97706; }
    .btn-edit-revisi { background-color: #0284c7; color: white; } /* Warna biru untuk edit revisi */
    .btn-edit-revisi:hover { background-color: #0369a1; }
    .btn-selesai { background-color: #10b981; color: white; }
</style>
@endpush

@section('konten')
<div class="fg-container">
    <div class="judul-form">
        <i class="fas fa-file-alt" style="color: #f59e0b; margin-right: 8px;"></i> Daftar Masuk CAPA 8D (Supplier Portal)
    </div>

    <table class="tabel-fg">
        <thead>
            <tr>
                <th>No</th>
                <th>No. CAPA</th>
                <th>Judul Masalah</th>
                <th>Tanggal Terbit</th>
                <th>Status</th>
                <th>Aksi / Isi Form</th>
            </tr>
        </thead>
        <tbody>
            @forelse($capas ?? [] as $index => $data)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $data->no_capa ?? '-' }}</td>
                    <td>{{ $data->tema_masalah ?? '-' }}</td>
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
                    <td>
                        @if($data->status == 'Open')
                            {{-- Jika status masih Open (belum pernah diisi) --}}
                            <a href="{{ url('/capa-8d/supplier/form/' . $data->id) }}" class="btn-aksi btn-isi">
                                <i class="fas fa-edit"></i> Isi Lembar 8D
                            </a>
                        @elseif($data->status == 'Review' || $data->status == 'Waiting Review')
                            {{-- Jika sudah dikirim dan masuk tahap Review, supplier tetap bisa mengedit/merevisi --}}
                            <a href="{{ url('/capa-8d/supplier/form/' . $data->id) }}" class="btn-aksi btn-edit-revisi" title="Revisi / Ubah Data CAPA">
                                <i class="fas fa-sync-alt"></i> Edit / Revisi 8D
                            </a>
                        @else
                            {{-- Jika sudah Closed oleh Admin --}}
                            <span class="btn-aksi btn-selesai">
                                <i class="fas fa-check-circle"></i> Selesai
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding: 30px; color: #64748b; font-style: italic;">
                        Belum ada dokumen CAPA masuk dari Quality.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection