@extends('layouts.staff-layout')

@section('title', 'Verifikasi Sertifikat Kalibrasi')

@section('konten')
<div class="calibration-page" style="padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 15px; flex-wrap: wrap; margin-bottom: 20px;">
        <div>
            <h2 style="margin: 0; color: #1e293b;"><i class="fas fa-file-circle-check" style="color: #0ea5e9;"></i> Verifikasi Sertifikat Kalibrasi</h2>
            <p style="margin: 6px 0 0; color: #64748b;">Catat hasil pemeriksaan sertifikat dan performa alat ukur.</p>
        </div>
        <a href="{{ route('quality.calibration.verification.create') }}" style="background: #10b981; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: bold;">
            <i class="fas fa-plus"></i> Tambah Verifikasi
        </a>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 6px; margin-bottom: 15px;">{{ session('success') }}</div>
    @endif

    <div style="background: white; padding: 16px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,.05); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 1500px; font-size: 12px;">
            <thead>
                <tr style="background: #9fc5e8; color: #1e293b; text-align: center;">
                    <th style="padding: 9px;">No.</th>
                    <th style="padding: 9px;">No. Alat</th>
                    <th style="padding: 9px;">Nama Item</th>
                    <th style="padding: 9px;">Titik Ukur</th>
                    <th style="padding: 9px;">Jenis/Type</th>
                    <th style="padding: 9px;">No. Seri</th>
                    <th style="padding: 9px;">Kapasitas</th>
                    <th style="padding: 9px;">Resolusi</th>
                    <th style="padding: 9px;">Internal - LoP</th>
                    <th style="padding: 9px;">Nilai Koreksi Sertifikat</th>
                    <th style="padding: 9px;">Nilai Uncertainty Sertifikat</th>
                    <th style="padding: 9px;">TE</th>
                    <th style="padding: 9px;">Hasil Verifikasi</th>
                    <th style="padding: 9px;">Status Alat</th>
                    <th style="padding: 9px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($verifications as $index => $verification)
                    <tr style="border-bottom: 1px solid #cbd5e1;">
                        <td style="padding: 8px; text-align: center;">{{ $verifications->firstItem() + $index }}</td>
                        <td style="padding: 8px; white-space: nowrap;">{{ $verification->equipment->identity_number }}</td>
                        <td style="padding: 8px;">{{ $verification->equipment->item_name }}</td>
                        <td style="padding: 8px; text-align: center;">{{ $verification->measurement_point ?: '-' }}</td>
                        <td style="padding: 8px;">{{ $verification->equipment->type ?: '-' }}</td>
                        <td style="padding: 8px;">{{ $verification->equipment->serial_number ?: '-' }}</td>
                        <td style="padding: 8px; text-align: center;">{{ $verification->capacity ?: '-' }}</td>
                        <td style="padding: 8px; text-align: center;">{{ $verification->resolution ?: '-' }}</td>
                        <td style="padding: 8px; text-align: center;">{{ $verification->internal_lop ?: '-' }}</td>
                        <td style="padding: 8px; text-align: center;">{{ $verification->certificate_correction ?: '-' }}</td>
                        <td style="padding: 8px; text-align: center;">{{ $verification->certificate_uncertainty ?: '-' }}</td>
                        <td style="padding: 8px; text-align: center;">{{ $verification->te ?: '-' }}</td>
                        <td style="padding: 8px; text-align: center;">{{ $verification->verification_result ?: '-' }}</td>
                        <td style="padding: 8px;">{{ $verification->equipment_status ?: '-' }}</td>
                        <td style="padding: 8px; white-space: nowrap;">
                            <a href="{{ route('quality.calibration.verification.edit', $verification) }}" style="color: #2563eb; margin-right: 8px;">Edit</a>
                            <form action="{{ route('quality.calibration.verification.destroy', $verification) }}" method="POST" style="display: inline;" onsubmit="return confirm('Hapus data verifikasi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="color: #dc2626; border: 0; background: none; cursor: pointer; padding: 0;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="15" style="padding: 24px; text-align: center; color: #64748b;">Belum ada data verifikasi sertifikat.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 15px;">{{ $verifications->links() }}</div>
    </div>
</div>
@endsection
