@extends('layouts.staff-layout')

@section('title', 'Mutasi Stok Quality - PT KIMPAI DYNA TUBE')

@push('styles')
<style>
    .card-table { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .btn-tambah { background-color: #d4a32a; color: white; padding: 10px 18px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 20px; transition: 0.2s; }
    .btn-tambah:hover { background-color: #b5891f; }
    
    .tabel-custom { width: 100%; border-collapse: collapse; }
    .tabel-custom th { background-color: #f8fafc; padding: 10px 8px; text-align: left; font-size: 11px; color: #64748b; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; white-space: nowrap; }
    .tabel-custom td { padding: 10px 8px; font-size: 12px; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .tabel-custom tr:hover td { background-color: #f8fafc; }
    
    .badge-status { padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: bold; white-space: nowrap; background-color: #fef08a; color: #854d0e; }
</style>
@endpush

@section('konten')
    
    <div style="margin-bottom: 20px;">
        <h1 style="font-size: 24px; color: var(--text-main); margin-bottom: 5px;">Mutasi Stok Quality</h1>
        <p style="color: var(--text-muted); font-size: 14px; margin: 0;">Pencatatan perpindahan dan status karantina stok barang di area quality.</p>
    </div>

    <a href="{{ url('/quality/mutasi-stok/tambah') }}" class="btn-tambah"><i class="fas fa-plus"></i> Catat Mutasi Baru</a>

    <div class="card-table">
        <div style="overflow-x: auto;">
            <table class="tabel-custom">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Jenis Mutasi (Masuk/Keluar)</th>
                        <th>Keterangan / Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mutasis ?? [] as $index => $mutasi)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $mutasi->tanggal ?? '-' }}</td>
                            <td>{{ $mutasi->kode_barang ?? '-' }}</td>
                            <td style="font-weight: 600;">{{ $mutasi->nama_barang ?? '-' }}</td>
                            <td>{{ $mutasi->jumlah ?? '-' }}</td>
                            <td>{{ $mutasi->jenis ?? '-' }}</td>
                            <td><span class="badge-status">{{ $mutasi->keterangan ?? 'Inspection' }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td>1</td>
                            <td>2026-08-01</td>
                            <td>MAT-992</td>
                            <td style="font-weight: 600;">Sample Pipa Karantina</td>
                            <td>10 Pcs</td>
                            <td>Masuk (Hold Area)</td>
                            <td><span class="badge-status">Menunggu Lab Test</span></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection