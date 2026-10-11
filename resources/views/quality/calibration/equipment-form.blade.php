@extends('layouts.staff-layout')

@section('title', $equipment->exists ? 'Edit Alat Ukur' : 'Tambah Alat Ukur')

@section('konten')
<div style="padding: 20px; max-width: 1000px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <h2 style="margin: 0; color: #1e293b;">{{ $equipment->exists ? 'Edit Alat Ukur' : 'Tambah Alat Ukur' }}</h2>
        <p style="color: #64748b;">Lengkapi identitas alat serta data checklist kalibrasi.</p>
    </div>

    @if($errors->any())
        <div style="background: #fee2e2; color: #b91c1c; padding: 12px 16px; border-radius: 6px; margin-bottom: 15px;">
            <ul style="margin: 0; padding-left: 20px;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ $equipment->exists ? route('quality.calibration.checklist.update', $equipment) : route('quality.calibration.checklist.store') }}" method="POST" style="background: white; padding: 22px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,.05);">
        @csrf
        @if($equipment->exists) @method('PUT') @endif
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
            <label>No. Identitas *
                <input name="identity_number" required maxlength="100" value="{{ old('identity_number', $equipment->identity_number) }}" placeholder="Contoh: KPDT/TUBE/001" style="width: 100%; box-sizing: border-box; margin-top: 6px; padding: 9px; border: 1px solid #cbd5e1; border-radius: 5px;">
            </label>
            <label>Nama Item *
                <input name="item_name" required maxlength="255" value="{{ old('item_name', $equipment->item_name) }}" style="width: 100%; box-sizing: border-box; margin-top: 6px; padding: 9px; border: 1px solid #cbd5e1; border-radius: 5px;">
            </label>
            <label>Jenis/Type
                <input name="type" maxlength="255" value="{{ old('type', $equipment->type) }}" style="width: 100%; box-sizing: border-box; margin-top: 6px; padding: 9px; border: 1px solid #cbd5e1; border-radius: 5px;">
            </label>
            <label>No. Seri
                <input name="serial_number" maxlength="255" value="{{ old('serial_number', $equipment->serial_number) }}" style="width: 100%; box-sizing: border-box; margin-top: 6px; padding: 9px; border: 1px solid #cbd5e1; border-radius: 5px;">
            </label>
            <label>Tanggal Kadaluarsa
                <input type="date" name="expiration_date" value="{{ old('expiration_date', $equipment->expiration_date?->format('Y-m-d')) }}" style="width: 100%; box-sizing: border-box; margin-top: 6px; padding: 9px; border: 1px solid #cbd5e1; border-radius: 5px;">
            </label>
            <label>Kondisi Alat
                <input name="tool_condition" maxlength="100" value="{{ old('tool_condition', $equipment->tool_condition) }}" placeholder="Contoh: OK" style="width: 100%; box-sizing: border-box; margin-top: 6px; padding: 9px; border: 1px solid #cbd5e1; border-radius: 5px;">
            </label>
            <label>Kondisi Label
                <input name="label_condition" maxlength="100" value="{{ old('label_condition', $equipment->label_condition) }}" placeholder="Contoh: ADA" style="width: 100%; box-sizing: border-box; margin-top: 6px; padding: 9px; border: 1px solid #cbd5e1; border-radius: 5px;">
            </label>
            <label>Pemegang
                <input name="holder" maxlength="255" value="{{ old('holder', $equipment->holder) }}" style="width: 100%; box-sizing: border-box; margin-top: 6px; padding: 9px; border: 1px solid #cbd5e1; border-radius: 5px;">
            </label>
            <label style="grid-column: 1 / -1;">Keterangan
                <textarea name="notes" maxlength="2000" rows="3" style="width: 100%; box-sizing: border-box; margin-top: 6px; padding: 9px; border: 1px solid #cbd5e1; border-radius: 5px;">{{ old('notes', $equipment->notes) }}</textarea>
            </label>
        </div>
        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" style="background: #10b981; color: white; padding: 10px 16px; border: 0; border-radius: 5px; font-weight: bold; cursor: pointer;">Simpan</button>
            <a href="{{ route('quality.calibration.checklist.index') }}" style="padding: 10px 16px; color: #334155; border: 1px solid #cbd5e1; border-radius: 5px; text-decoration: none;">Batal</a>
        </div>
    </form>
</div>
@endsection
