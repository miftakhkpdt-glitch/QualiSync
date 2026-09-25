@extends('layouts.staff-layout')

@section('title', 'Form Pengajuan Cuti Karyawan - PT KIMPAI DYNA TUBE')

@section('konten')
<div class="container-fluid" style="padding: 10px 0;">
    <h1 style="font-size: 24px; font-weight: bold; margin-bottom: 20px; color: #1e293b;">Form Pengajuan Cuti Karyawan</h1>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Card Form Pengajuan -->
    <div style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.04); margin-bottom: 25px; border: 1px solid #e2e8f0;">
        <form action="{{ url('/pengajuan-cuti/store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; font-size: 13px; color: #444; margin-bottom: 6px;">Nama Lengkap Karyawan</label>
                <input type="text" name="nama_karyawan" class="form-control" value="{{ Auth::user()->name ?? '' }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px; box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; font-size: 13px; color: #444; margin-bottom: 6px;">Departemen</label>
                <input type="text" name="departemen" class="form-control" placeholder="Contoh: Quality Control" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px; box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; font-size: 13px; color: #444; margin-bottom: 6px;">Jumlah Hari Cuti</label>
                <input type="number" name="jumlah_hari" class="form-control" min="1" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px; box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; font-size: 13px; color: #444; margin-bottom: 6px;">Tanggal Mulai Cuti</label>
                <input type="date" name="tanggal_mulai" class="form-control" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px; box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; font-size: 13px; color: #444; margin-bottom: 6px;">Tanggal Selesai Cuti</label>
                <input type="date" name="tanggal_selesai" class="form-control" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px; box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; font-size: 13px; color: #444; margin-bottom: 6px;">Alasan Cuti</label>
                <textarea name="alasan" class="form-control" rows="3" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px; box-sizing: border-box;"></textarea>
            </div>
            <button type="submit" style="background-color: #2563eb; color: #fff; border: none; padding: 10px 20px; font-weight: bold; border-radius: 5px; cursor: pointer;"><i class="fas fa-paper-plane"></i> Kirim Pengajuan</button>
        </form>
    </div>

    <!-- Card Tabel Riwayat -->
    <div style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.04); margin-bottom: 25px; border: 1px solid #e2e8f0;">
        <h3 style="font-size: 18px; margin-bottom: 15px; color: #1e293b;">Riwayat Pengajuan Cuti Anda</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 10px 12px; text-align: left; font-size: 13px;">No</th>
                        <th style="padding: 10px 12px; text-align: left; font-size: 13px;">Departemen</th>
                        <th style="padding: 10px 12px; text-align: left; font-size: 13px;">Tanggal Mulai</th>
                        <th style="padding: 10px 12px; text-align: left; font-size: 13px;">Tanggal Selesai</th>
                        <th style="padding: 10px 12px; text-align: left; font-size: 13px;">Jumlah Hari</th>
                        <th style="padding: 10px 12px; text-align: left; font-size: 13px;">Alasan</th>
                        <th style="padding: 10px 12px; text-align: left; font-size: 13px;">Status HRD</th>
                        <th style="padding: 10px 12px; text-align: center; width: 130px; font-size: 13px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatPengajuan as $index => $row)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px 12px; font-size: 13px;">{{ $index + 1 }}</td>
                        <td style="padding: 10px 12px; font-size: 13px;">{{ $row->departemen }}</td>
                        <td style="padding: 10px 12px; font-size: 13px;">{{ $row->tanggal_mulai }}</td>
                        <td style="padding: 10px 12px; font-size: 13px;">{{ $row->tanggal_selesai }}</td>
                        <td style="padding: 10px 12px; font-size: 13px;">{{ $row->jumlah_hari }} Hari</td>
                        <td style="padding: 10px 12px; font-size: 13px;">{{ $row->alasan }}</td>
                        <td style="padding: 10px 12px; font-size: 13px;">
                            @if($row->status_hrd == 'Disetujui')
                                <span style="background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">Disetujui</span>
                            @elseif($row->status_hrd == 'Ditolak')
                                <span style="background: #f8d7da; color: #721c24; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">Ditolak</span>
                            @else
                                <span style="background: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">Pending / Menunggu</span>
                            @endif
                        </td>
                        <td style="padding: 10px 12px; text-align: center;">
                            <a href="{{ url('/pengajuan-cuti/edit/' . $row->id) }}" style="background-color: #ffc107; color: #333; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: bold; display: inline-block; margin-right: 4px;"><i class="fas fa-edit"></i> Edit</a>
                            <a href="{{ url('/pengajuan-cuti/delete/' . $row->id) }}" onclick="return confirm('Yakin ingin menghapus pengajuan cuti ini?')" style="background-color: #dc3545; color: #fff; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: bold; display: inline-block;"><i class="fas fa-trash"></i> Hapus</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: #777; padding: 20px; font-size: 13px;">Belum ada riwayat pengajuan cuti.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection