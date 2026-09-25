@extends('layouts.staff-layout')

@push('styles')
<style>
    .approval-container { background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); max-width: 1300px; margin: 0 auto; border: 1px solid #e2e8f0; }
    .judul-halaman { font-size: 18px; font-weight: 700; margin-bottom: 20px; color: #1e293b; text-transform: uppercase; }
    .alert-success { background-color: #d1fae5; color: #065f46; padding: 12px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #a7f3d0; font-size: 14px; }
    .tabel-approval { width: 100%; border-collapse: collapse; font-size: 12px; text-align: center; color: #1e293b; }
    .tabel-approval th, .tabel-approval td { border: 1px solid #cbd5e1; padding: 10px 8px; vertical-align: middle; }
    .tabel-approval thead th { background-color: #0f172a; color: #ffffff; text-transform: uppercase; }
    .badge { padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; display: inline-block; }
    .badge-pending { background-color: #fef3c7; color: #d97706; }
    .badge-approved { background-color: #dcfce7; color: #16a34a; }
    .badge-rejected { background-color: #fee2e2; color: #dc2626; }
    .btn-action { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; cursor: pointer; border: none; color: white; }
    .btn-approve { background-color: #16a34a; }
    .btn-reject { background-color: #dc2626; }
</style>
@endpush

@section('konten')
<div class="approval-container">
    <div class="judul-halaman">
        <i class="fas fa-user-shield" style="color: #0f172a; margin-right: 8px;"></i> Approval Final Lembur (HRD & GA)
    </div>

    @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle"></i> <strong>Berhasil!</strong> {{ session('success') }}
        </div>
    @endif

    <table class="tabel-approval">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Karyawan</th>
                <th>Tanggal Lembur</th>
                <th>Jam</th>
                <th>Keterangan</th>
                <th>Status Dept</th>
                <th>Status HRD (Final)</th>
                <th>Aksi HRD</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lemburList ?? [] as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: bold; text-align: left; padding-left: 10px;">{{ $item->user->name ?? 'Karyawan' }}</td>
                    <td>{{ $item->tanggal_lembur }}</td>
                    <td>{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</td>
                    <td style="text-align: left; padding-left: 10px;">{{ $item->keterangan_pekerjaan }}</td>
                    <td><span class="badge badge-approved">Approved</span></td>
                    <td>
                        <span class="badge badge-{{ strtolower($item->status_hrd) == 'approved' ? 'approved' : (strtolower($item->status_hrd) == 'rejected' ? 'rejected' : 'pending') }}">
                            {{ $item->status_hrd }}
                        </span>
                    </td>
                    <td>
                        @if(strtolower($item->status_hrd) === 'pending')
                            <form action="{{ route('lembur.update.hrd', $item->id) }}" method="POST" style="display: inline-flex; gap: 5px; justify-content: center;">
                                @csrf
                                <input type="hidden" name="status" value="Approved">
                                <button type="submit" class="btn-action btn-approve"><i class="fas fa-check"></i> Approve</button>
                            </form>
                            <form action="{{ route('lembur.update.hrd', $item->id) }}" method="POST" style="display: inline-flex; gap: 5px; justify-content: center;">
                                @csrf
                                <input type="hidden" name="status" value="Rejected">
                                <button type="submit" class="btn-action btn-reject"><i class="fas fa-times"></i> Reject</button>
                            </form>
                        @else
                            <span style="color: #64748b; font-size: 11px; font-style: italic;"><i class="fas fa-lock"></i> Selesai</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="padding: 30px; color: #64748b; font-style: italic;">Belum ada data lembur dari departemen yang siap diproses HRD.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection