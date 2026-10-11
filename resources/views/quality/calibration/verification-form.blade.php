@extends('layouts.staff-layout')

@section('title', $verification->exists ? 'Edit Verifikasi Sertifikat' : 'Tambah Verifikasi Sertifikat')

@section('konten')
<div style="padding: 20px; max-width: 1100px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <h2 style="margin: 0; color: #1e293b;">{{ $verification->exists ? 'Edit Verifikasi Sertifikat' : 'Tambah Verifikasi Sertifikat' }}</h2>
        <p style="color: #64748b;">Satu alat dapat memiliki beberapa baris titik ukur, misalnya In dan Out.</p>
    </div>

    @if($errors->any())
        <div style="background: #fee2e2; color: #b91c1c; padding: 12px 16px; border-radius: 6px; margin-bottom: 15px;">
            <ul style="margin: 0; padding-left: 20px;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ $verification->exists ? route('quality.calibration.verification.update', $verification) : route('quality.calibration.verification.store') }}" method="POST" style="background: white; padding: 22px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,.05);">
        @csrf
        @if($verification->exists) @method('PUT') @endif
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
            <label style="grid-column: 1 / -1;">Pilih Alat *
                <select name="equipment_id" required style="width: 100%; box-sizing: border-box; margin-top: 6px; padding: 9px; border: 1px solid #cbd5e1; border-radius: 5px;">
                    <option value="">-- Pilih alat ukur --</option>
                    @foreach($equipmentList as $item)
                        <option value="{{ $item->id }}" @selected(old('equipment_id', $verification->equipment_id) == $item->id)>{{ $item->identity_number }} — {{ $item->item_name }}{{ $item->serial_number ? ' (Seri ' . $item->serial_number . ')' : '' }}</option>
                    @endforeach
                </select>
                @if($equipmentList->isEmpty())
                    <small style="display: block; margin-top: 6px; color: #b91c1c;">Tambahkan alat ukur pada menu Checklist Alat Ukur terlebih dahulu.</small>
                @endif
            </label>
            <label>Titik Ukur
                <input name="measurement_point" maxlength="100" value="{{ old('measurement_point', $verification->measurement_point) }}" placeholder="Contoh: In / Out" style="width: 100%; box-sizing: border-box; margin-top: 6px; padding: 9px; border: 1px solid #cbd5e1; border-radius: 5px;">
            </label>
            <label>Kapasitas
                <input name="capacity" maxlength="100" value="{{ old('capacity', $verification->capacity) }}" style="width: 100%; box-sizing: border-box; margin-top: 6px; padding: 9px; border: 1px solid #cbd5e1; border-radius: 5px;">
            </label>
            <label>Resolusi
                <input name="resolution" maxlength="100" value="{{ old('resolution', $verification->resolution) }}" style="width: 100%; box-sizing: border-box; margin-top: 6px; padding: 9px; border: 1px solid #cbd5e1; border-radius: 5px;">
            </label>
            <label>Internal - LoP (Limit of Performance)
                <input name="internal_lop" maxlength="100" value="{{ old('internal_lop', $verification->internal_lop) }}" style="width: 100%; box-sizing: border-box; margin-top: 6px; padding: 9px; border: 1px solid #cbd5e1; border-radius: 5px;">
            </label>
            <label>Nilai Koreksi pada Sertifikat
                <input name="certificate_correction" maxlength="100" value="{{ old('certificate_correction', $verification->certificate_correction) }}" style="width: 100%; box-sizing: border-box; margin-top: 6px; padding: 9px; border: 1px solid #cbd5e1; border-radius: 5px;">
            </label>
            <label>Nilai Uncertainty pada Sertifikat
                <input name="certificate_uncertainty" maxlength="100" value="{{ old('certificate_uncertainty', $verification->certificate_uncertainty) }}" style="width: 100%; box-sizing: border-box; margin-top: 6px; padding: 9px; border: 1px solid #cbd5e1; border-radius: 5px;">
            </label>
            <label>TE = Koreksi + Uncertainty
                <input name="te" maxlength="100" value="{{ old('te', $verification->te) }}" style="width: 100%; box-sizing: border-box; margin-top: 6px; padding: 9px; border: 1px solid #cbd5e1; border-radius: 5px;">
            </label>
            <label>Hasil Verifikasi
                <input name="verification_result" maxlength="100" value="{{ old('verification_result', $verification->verification_result) }}" placeholder="Contoh: Terlampir / -" style="width: 100%; box-sizing: border-box; margin-top: 6px; padding: 9px; border: 1px solid #cbd5e1; border-radius: 5px;">
            </label>
            <label>Status Alat
                <input name="equipment_status" maxlength="255" value="{{ old('equipment_status', $verification->equipment_status) }}" placeholder="Contoh: Alat layak digunakan" style="width: 100%; box-sizing: border-box; margin-top: 6px; padding: 9px; border: 1px solid #cbd5e1; border-radius: 5px;">
            </label>
        </div>
        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" @disabled($equipmentList->isEmpty()) style="background: #10b981; color: white; padding: 10px 16px; border: 0; border-radius: 5px; font-weight: bold; cursor: pointer;">Simpan</button>
            <a href="{{ route('quality.calibration.verification.index') }}" style="padding: 10px 16px; color: #334155; border: 1px solid #cbd5e1; border-radius: 5px; text-decoration: none;">Batal</a>
        </div>
    </form>
</div>
@endsection
