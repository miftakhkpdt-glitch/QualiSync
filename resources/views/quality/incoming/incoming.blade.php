@extends('layouts.staff-layout')

@section('title', 'Incoming Material - PT KIMPAI DYNA TUBE')

@push('styles')
<style>
    .card-table { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .btn-tambah { background-color: #d4a32a; color: white; padding: 10px 18px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 20px; transition: 0.2s; }
    .btn-tambah:hover { background-color: #b5891f; }
    
    .tabel-custom { width: 100%; border-collapse: collapse; }
    .tabel-custom th { background-color: #f8fafc; padding: 10px 8px; text-align: left; font-size: 11px; color: #64748b; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; white-space: nowrap; }
    .tabel-custom td { padding: 10px 8px; font-size: 12px; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .tabel-custom tr:hover td { background-color: #f8fafc; }
    
    .badge { padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: bold; white-space: nowrap; }
    .badge-ok { background-color: #dcfce7; color: #15803d; }
    
    .btn-aksi { padding: 5px 9px; border-radius: 5px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; }
</style>
@endpush

@section('konten')
    
    <div style="margin-bottom: 20px;">
        <h1 style="font-size: 24px; color: var(--text-main); margin-bottom: 5px;">Incoming Material Quality</h1>
        <p style="color: var(--text-muted); font-size: 14px; margin: 0;">Monitoring dan pengecekan material masuk.</p>
    </div>

    <a href="{{ url('/incoming/tambah') }}" class="btn-tambah"><i class="fas fa-plus"></i> Tambah Data Incoming</a>

    <div class="card-table">
        <div style="overflow-x: auto;">
            <table class="tabel-custom">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Material</th>
                        <th>Supplier</th>
                        <th>No. Surat Jalan</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($incomings ?? [] as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->tanggal ?? '-' }}</td>
                            <td style="font-weight: 600;">{{ $item->nama_material ?? '-' }}</td>
                            <td>{{ $item->supplier ?? '-' }}</td>
                            <td>{{ $item->no_surat_jalan ?? '-' }}</td>
                            <td>{{ $item->jumlah ?? '-' }}</td>
                            <td><span class="badge badge-ok">{{ $item->status ?? 'OK' }}</span></td>
                            <td style="text-align: center; white-space: nowrap;">
                                <button class="btn-aksi" style="background: #eab308; color: white; border: none;"><i class="fas fa-edit"></i></button>
                                <button class="btn-aksi" style="background: #ef4444; color: white; border: none;"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td>1</td>
                            <td>2026-08-01</td>
                            <td style="font-weight: 600;">Steel Coil SPHC</td>
                            <td>PT Krakatau Steel</td>
                            <td>SJ-88291</td>
                            <td>5 Ton</td>
                            <td><span class="badge badge-ok">Passed / OK</span></td>
                            <td style="text-align: center; white-space: nowrap;">
                                <button class="btn-aksi" style="background: #eab308; color: white; border: none;"><i class="fas fa-edit"></i></button>
                                <button class="btn-aksi" style="background: #ef4444; color: white; border: none;"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection