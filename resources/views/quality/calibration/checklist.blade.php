@extends('layouts.staff-layout')

@section('title', 'Checklist Alat Ukur')

@section('konten')
<div class="calibration-page" style="padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 15px; flex-wrap: wrap; margin-bottom: 20px;">
        <div>
            <h2 style="margin: 0; color: #1e293b;"><i class="fas fa-ruler-combined" style="color: #0ea5e9;"></i> Checklist Alat Ukur</h2>
            <p style="margin: 6px 0 0; color: #64748b;">Daftar identitas dan kondisi alat ukur.</p>
        </div>
        <a href="{{ route('quality.calibration.checklist.create') }}" style="background: #10b981; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: bold;">
            <i class="fas fa-plus"></i> Tambah Alat Ukur
        </a>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 6px; margin-bottom: 15px;">{{ session('success') }}</div>
    @endif

    <div style="background: white; padding: 16px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,.05); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 1100px; font-size: 13px;">
            <thead>
                <tr style="background: #cbd5e1; color: #1e293b; text-align: center;">
                    <th style="padding: 10px;">No.</th>
                    <th style="padding: 10px;">No. Identitas</th>
                    <th style="padding: 10px;">Nama Item</th>
                    <th style="padding: 10px;">Jenis/Type</th>
                    <th style="padding: 10px;">No. Seri</th>
                    <th style="padding: 10px;">Tanggal Kadaluarsa</th>
                    <th style="padding: 10px;">Kondisi Alat</th>
                    <th style="padding: 10px;">Label</th>
                    <th style="padding: 10px;">Pemegang</th>
                    <th style="padding: 10px;">Keterangan</th>
                    <th style="padding: 10px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipment as $index => $item)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 9px; text-align: center;">{{ $equipment->firstItem() + $index }}</td>
                        <td style="padding: 9px; white-space: nowrap;">{{ $item->identity_number }}</td>
                        <td style="padding: 9px;">{{ $item->item_name }}</td>
                        <td style="padding: 9px;">{{ $item->type ?: '-' }}</td>
                        <td style="padding: 9px;">{{ $item->serial_number ?: '-' }}</td>
                        <td style="padding: 9px; white-space: nowrap;">{{ $item->expiration_date?->format('d-M-y') ?? '-' }}</td>
                        <td style="padding: 9px; text-align: center;">{{ $item->tool_condition ?: '-' }}</td>
                        <td style="padding: 9px; text-align: center;">{{ $item->label_condition ?: '-' }}</td>
                        <td style="padding: 9px;">{{ $item->holder ?: '-' }}</td>
                        <td style="padding: 9px;">{{ $item->notes ?: '-' }}</td>
                        <td style="padding: 9px; white-space: nowrap;">
                            <a href="{{ route('quality.calibration.checklist.edit', $item) }}" style="color: #2563eb; margin-right: 8px;">Edit</a>
                            <form action="{{ route('quality.calibration.checklist.destroy', $item) }}" method="POST" style="display: inline;" onsubmit="return confirm('Hapus alat ukur ini beserta semua data verifikasinya?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="color: #dc2626; border: 0; background: none; cursor: pointer; padding: 0;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="11" style="padding: 24px; text-align: center; color: #64748b;">Belum ada alat ukur. Tambahkan data alat pertama.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 15px;">{{ $equipment->links() }}</div>
    </div>
</div>
@endsection
