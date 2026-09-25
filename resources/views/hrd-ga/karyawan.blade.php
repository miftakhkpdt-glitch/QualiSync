@extends('layouts.staff-layout')

@section('title', 'Data Karyawan - PT KIMPAI DYNA TUBE')

@push('styles')
<style>
    .card-table { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .btn-tambah { background-color: #d4a32a; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: 0.2s; border: none; cursor: pointer; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; }
    .btn-tambah:hover { background-color: #b5891f; }
    .btn-kembali { background-color: #64748b; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: 0.2s; border: none; cursor: pointer; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; }
    .btn-kembali:hover { background-color: #475569; }
    
    /* Tabel Dibuat Padat Agar Muat Tanpa Scrollbar */
    .tabel-karyawan { width: 100%; border-collapse: collapse; }
    .tabel-karyawan th { background-color: #f8fafc; padding: 10px 8px; text-align: left; font-size: 11px; color: #64748b; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; white-space: nowrap; }
    .tabel-karyawan td { padding: 10px 8px; font-size: 12px; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .tabel-karyawan tr:hover td { background-color: #f8fafc; }
    
    .badge { padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: bold; white-space: nowrap; }
    .badge-status { background-color: #e2e8f0; color: #475569; }
    .badge-cuti { background-color: #e0f2fe; color: #0284c7; }
    
    .link-dokumen { color: #0ea5e9; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 4px; font-size: 12px; white-space: nowrap; }
    .link-dokumen:hover { text-decoration: underline; }
    
    .btn-aksi { padding: 5px 9px; border-radius: 5px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; height: 26px; }
</style>
@endpush

@section('konten')
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="{{ url('/home') }}" class="btn-kembali"><i class="fas fa-arrow-left"></i> Kembali</a>
            <h2 style="margin: 0; color: var(--text-main); font-size: 22px;">Data Karyawan PT Kimpai Dyna Tube</h2>
        </div>
        <a href="{{ url('/hrd-ga/karyawan/tambah') }}" class="btn-tambah"><i class="fas fa-plus"></i> Tambah Karyawan</a>
    </div>

    <div class="card-table">
        <div>
            <table class="tabel-karyawan">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIK</th>
                        <th>Nama Karyawan</th>
                        <th>Departemen</th>
                        <th>Jabatan</th>
                        <th>Tanggal Masuk</th>
                        <th>Status</th>
                        <th>Sisa Cuti</th>
                        <th>KTP</th>
                        <th>KK</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    
                    @forelse ($karyawans ?? [] as $index => $karyawan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $karyawan->nik ?? '-' }}</td>
                            <td style="font-weight: 600;">{{ $karyawan->nama_karyawan ?? '-' }}</td>
                            <td>{{ $karyawan->departemen ?? '-' }}</td>
                            <td>{{ $karyawan->jabatan ?? '-' }}</td>
                            <td>{{ $karyawan->tanggal_masuk ?? '-' }}</td>
                            <td><span class="badge badge-status">{{ $karyawan->status_karyawan ?? '-' }}</span></td>
                            <td><span class="badge badge-cuti">{{ $karyawan->sisa_cuti ?? '0' }} Hari</span></td>
                            
                            <!-- Link KTP -->
                            <td>
                                @if($karyawan->ktp)
                                    <a href="{{ asset('storage/' . $karyawan->ktp) }}" target="_blank" class="link-dokumen"><i class="fas fa-file-pdf"></i> Lihat</a>
                                @else
                                    -
                                @endif
                            </td>
                            
                            <!-- Link KK -->
                            <td>
                                @if($karyawan->kk)
                                    <a href="{{ asset('storage/' . $karyawan->kk) }}" target="_blank" class="link-dokumen"><i class="fas fa-file-pdf"></i> Lihat</a>
                                @else
                                    -
                                @endif
                            </td>
                            
                            <!-- TOMBOL AKSI (SUDAH DIPERBAIKI) -->
                            <td style="text-align: center; white-space: nowrap; display: flex; justify-content: center; gap: 5px;">
                                <!-- Tombol Edit -->
                                <a href="{{ url('/hrd-ga/karyawan/edit/'.$karyawan->id) }}" class="btn-aksi" style="background: #eab308; color: white; text-decoration: none;"><i class="fas fa-edit"></i></a>
                                
                                <!-- Tombol Delete -->
                                <form action="{{ url('/hrd-ga/karyawan/delete/'.$karyawan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data karyawan ini? Data lampiran juga akan ikut terhapus permanen.');" style="margin: 0; padding: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-aksi" style="background: #ef4444; color: white; border: none;"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    
                    @empty
                        <tr>
                            <td colspan="11" style="text-align: center; padding: 20px;">Belum ada data karyawan. Silakan tambahkan data baru.</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>

@endsection