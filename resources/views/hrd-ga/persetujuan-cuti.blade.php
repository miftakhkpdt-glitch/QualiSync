@extends('layouts.staff-layout')

@section('title', 'Persetujuan Cuti - PT KIMPAI DYNA TUBE')

@push('styles')
<style>
    .card-table { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    
    .tabel-cuti { width: 100%; border-collapse: collapse; }
    .tabel-cuti th { background-color: #f8fafc; padding: 12px 10px; text-align: left; font-size: 11px; color: #64748b; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; white-space: nowrap; }
    .tabel-cuti td { padding: 12px 10px; font-size: 13px; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .tabel-cuti tr:hover td { background-color: #f8fafc; }
    
    .badge { padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: bold; white-space: nowrap; }
    .badge-success { background-color: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
    .badge-warning { background-color: #fef08a; color: #ca8a04; border: 1px solid #fde047; }
    .badge-danger { background-color: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
    
    .btn-aksi { padding: 6px 12px; border-radius: 6px; border: none; cursor: pointer; font-size: 12px; font-weight: 600; transition: 0.2s; color: white; margin-right: 5px; }
    .btn-setuju { background-color: #10b981; }
    .btn-setuju:hover { background-color: #059669; }
    .btn-tolak { background-color: #ef4444; }
    .btn-tolak:hover { background-color: #dc2626; }
</style>
@endpush

@section('konten')
    
    <div style="margin-bottom: 25px;">
        <h1 style="font-size: 24px; color: var(--text-main); margin-bottom: 5px;">Persetujuan Pengajuan Cuti Karyawan</h1>
        <p style="color: var(--text-muted); font-size: 14px; margin: 0;">Monitoring dan validasi permohonan cuti tingkat HRD.</p>
    </div>

    <!-- Menampilkan Notifikasi Sukses/Error -->
    @if(session('success'))
        <div style="background-color: #dcfce7; color: #16a34a; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background-color: #fee2e2; color: #dc2626; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card-table">
        <div style="overflow-x: auto;">
            <table class="tabel-cuti">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tgl Pengajuan</th>
                        <th>Nama Karyawan</th>
                        <th>Departemen</th>
                        <th>Tanggal Cuti</th>
                        <th>Alasan</th>
                        <th>Status Dept</th>
                        <th>Status HRD</th>
                        <th style="text-align: center;">Aksi HRD</th>
                    </tr>
                </thead>
                <tbody>
                    
                    @forelse ($daftarCuti ?? [] as $index => $cuti)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            
                            <!-- Menampilkan tanggal pembuatan data dengan fungsi pengaman !empty -->
                            <td style="font-weight: 600; color: #475569;">
                                {{ !empty($cuti->created_at) ? \Carbon\Carbon::parse($cuti->created_at)->format('Y-m-d') : '-' }}
                            </td>
                            
                            <td style="font-weight: 600;">{{ $cuti->nama_karyawan }}</td>
                            <td>{{ $cuti->departemen }}</td>
                            <td>{{ $cuti->tanggal_mulai }} s/d {{ $cuti->tanggal_selesai }}</td>
                            <td>{{ $cuti->alasan }}</td>
                            
                            <!-- Status Departemen -->
                            <td>
                                @if($cuti->status_dept == 'Disetujui')
                                    <span class="badge badge-success"><i class="fas fa-check"></i> Disetujui Dept</span>
                                @elseif($cuti->status_dept == 'Ditolak')
                                    <span class="badge badge-danger"><i class="fas fa-times"></i> Ditolak Dept</span>
                                @else
                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Pending Dept</span>
                                @endif
                            </td>

                            <!-- Status HRD -->
                            <td>
                                @if($cuti->status_hrd == 'Disetujui')
                                    <span class="badge badge-success"><i class="fas fa-check"></i> Disetujui HRD</span>
                                @elseif($cuti->status_hrd == 'Ditolak')
                                    <span class="badge badge-danger"><i class="fas fa-times"></i> Ditolak HRD</span>
                                @else
                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Pending HRD</span>
                                @endif
                            </td>

                            <!-- Tombol Aksi HRD -->
                            <td style="text-align: center; white-space: nowrap;">
                                @if($cuti->status_hrd != 'Disetujui' && $cuti->status_hrd != 'Ditolak')
                                    <form action="{{ url('/hrd-ga/persetujuan-cuti/update/' . $cuti->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        <input type="hidden" name="status_hrd" value="Disetujui">
                                        <button type="submit" class="btn-aksi btn-setuju" onclick="return confirm('Setujui pengajuan cuti ini? Sisa cuti akan otomatis terpotong.');"><i class="fas fa-check"></i> Setujui</button>
                                    </form>
                                    
                                    <form action="{{ url('/hrd-ga/persetujuan-cuti/update/' . $cuti->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        <input type="hidden" name="status_hrd" value="Ditolak">
                                        <button type="submit" class="btn-aksi btn-tolak" onclick="return confirm('Tolak pengajuan cuti ini?');"><i class="fas fa-times"></i> Tolak</button>
                                    </form>
                                @else
                                    <span style="color: #10b981; font-weight: bold; font-size: 12px;"><i class="fas fa-check-circle"></i> Selesai ({{ $cuti->status_hrd }})</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 30px; color: #64748b;">Belum ada data pengajuan cuti yang masuk.</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>

@endsection