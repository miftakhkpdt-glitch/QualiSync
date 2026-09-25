@extends('layouts.staff-layout')

@push('styles')
<style>
    .lembur-container { background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); max-width: 1200px; margin: 0 auto; border: 1px solid #e2e8f0; }
    .judul-halaman { font-size: 18px; font-weight: 700; margin-bottom: 20px; color: #1e293b; text-transform: uppercase; }
    .form-box { background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 30px; }
    .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 15px; }
    .form-group label { display: block; font-weight: 600; color: #334155; margin-bottom: 6px; font-size: 13px; }
    .form-control { width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; box-sizing: border-box; }
    .btn-simpan { background-color: #10b981; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 13px; }
    .btn-simpan:hover { background-color: #059669; }
    
    .tabel-lembur { width: 100%; border-collapse: collapse; font-size: 12px; text-align: center; color: #1e293b; margin-top: 10px; }
    .tabel-lembur th, .tabel-lembur td { border: 1px solid #cbd5e1; padding: 10px 8px; vertical-align: middle; }
    .tabel-lembur thead th { background-color: #1e293b; color: #ffffff; text-transform: uppercase; }
    
    .badge { padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; display: inline-block; }
    .badge-pending { background-color: #fef3c7; color: #d97706; }
    .badge-approved { background-color: #dcfce7; color: #16a34a; }
    .badge-rejected { background-color: #fee2e2; color: #dc2626; }
</style>
@endpush

@section('konten')
<div class="lembur-container">
    <div class="judul-halaman">
        <i class="fas fa-clock" style="color: #10b981; margin-right: 8px;"></i> Form Pengajuan Lembur Karyawan
    </div>

    <!-- Form Input Pengajuan Lembur -->
    <div class="form-box">
        <form action="{{ route('lembur.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div>
                    <label>Tanggal Lembur</label>
                    <input type="date" name="tanggal_lembur" class="form-control" required>
                </div>
                <div>
                    <label>Jam Mulai</label>
                    <input type="time" name="jam_mulai" class="form-control" required>
                </div>
                <div>
                    <label>Jam Selesai</label>
                    <input type="time" name="jam_selesai" class="form-control" required>
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Keterangan / Pekerjaan yang Dilakukan</label>
                <textarea name="keterangan_pekerjaan" class="form-control" rows="2" placeholder="Tuliskan uraian pekerjaan lembur..." required></textarea>
            </div>
            <button type="submit" class="btn-simpan">
                <i class="fas fa-paper-plane"></i> Kirim Pengajuan Lembur
            </button>
        </form>
    </div>

    <!-- Riwayat Lembur Pribadi -->
    <div style="font-weight: bold; margin-bottom: 10px; color: #334155; font-size: 15px;">Riwayat Pengajuan Lembur Anda</div>
    <table class="tabel-lembur">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Keterangan</th>
                <th>Status Dept</th>
                <th>Status HRD</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lemburList ?? [] as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->tanggal_lembur }}</td>
                    <td>{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</td>
                    <td style="text-align: left; padding-left: 10px;">{{ $item->keterangan_pekerjaan }}</td>
                    <td>
                        <span class="badge badge-{{ strtolower($item->status_dept) == 'approved' ? 'approved' : (strtolower($item->status_dept) == 'rejected' ? 'rejected' : 'pending') }}">
                            {{ $item->status_dept }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-{{ strtolower($item->status_hrd) == 'approved' ? 'approved' : (strtolower($item->status_hrd) == 'rejected' ? 'rejected' : 'pending') }}">
                            {{ $item->status_hrd }}
                        </span>
                    </td>
                    <td>
                        @if($item->status_hrd == 'Pending')
                            <a href="{{ route('lembur.delete', $item->id) }}" onclick="return confirm('Yakin ingin membatalkan pengajuan ini?')" style="color: #ef4444; font-weight: bold; text-decoration: none; font-size: 11px;">
                                <i class="fas fa-trash"></i> Batal
                            </a>
                        @else
                            <span style="color: #94a3b8; font-size: 11px;">Locked</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="padding: 25px; color: #64748b; font-style: italic;">Belum ada riwayat pengajuan lembur.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection