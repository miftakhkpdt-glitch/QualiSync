{{-- Ganti 'layout' di bawah ini dengan nama file layout utama Anda --}}
{{-- Contoh: jika nama file layout Anda layout.blade.php, biarkan begini. Jika nama filenya app.blade.php, ganti jadi 'app' --}}
@extends('layouts.staff-layout') 

@push('styles')
<style>
    .approval-container { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .btn-kembali { background: #6c757d; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block; margin-bottom: 20px; }
    .approval-table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 13px; color: #1e293b; }
    .approval-table th, .approval-table td { padding: 12px; border: 1px solid #e2e8f0; text-align: center; }
    .approval-table th { background-color: #f1f5f9; color: #334155; }
    .btn-setuju { background: #198754; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
    .btn-tolak { background: #dc3545; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
    .badge-selesai { color: #64748b; font-style: italic; font-weight: 600; background: #f1f5f9; padding: 5px 10px; border-radius: 4px; display: inline-block; }
</style>
@endpush

@section('konten')
<div class="approval-container">
    <h3><i class="fas fa-check-circle" style="color: #198754;"></i> Approval Cuti Karyawan</h3>
    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin-bottom: 20px;">

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 10px 15px; border-radius: 4px; margin-bottom: 15px; border-left: 4px solid #28a745;">
            <i class="fas fa-check"></i> {{ session('success') }}
        </div>
    @endif

    <div style="overflow-x: auto;">
        <table class="approval-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Karyawan</th>
                    <th>Departemen</th>
                    <th>Jumlah Hari</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Alasan</th>
                    <th>Status Dept</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($listApproval as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $row->nama_karyawan }}</strong></td>
                    <td>{{ $row->departemen }}</td>
                    <td>{{ $row->jumlah_hari }} Hari</td>
                    <td>{{ $row->tanggal_mulai }}</td>
                    <td>{{ $row->tanggal_selesai }}</td>
                    <td>{{ $row->alasan }}</td>
                    <td>
                        <span style="font-weight: bold; color: {{ $row->status_dept == 'Disetujui' ? '#198754' : ($row->status_dept == 'Ditolak' ? '#dc3545' : '#f59e0b') }}">
                            {{ $row->status_dept ?? 'Pending' }}
                        </span>
                    </td>
                    <td>
                        @if($row->status_dept == 'Pending' || empty($row->status_dept))
                            <form action="{{ url('/cuti-approval/update-status/' . $row->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="status" value="Disetujui">
                                <button type="submit" class="btn-setuju"><i class="fas fa-check"></i></button>
                            </form>
                            <form action="{{ url('/cuti-approval/update-status/' . $row->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="status" value="Ditolak">
                                <button type="submit" class="btn-tolak"><i class="fas fa-times"></i></button>
                            </form>
                        @else
                            <span class="badge-selesai">Sudah Diproses</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; color: #94a3b8; padding: 20px;">
                        <i class="fas fa-inbox" style="font-size: 24px; display: block; margin-bottom: 10px;"></i>
                        Belum ada pengajuan cuti yang perlu di-approve.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection