@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 800px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h3 style="margin-bottom: 5px; color: #334155; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
        <i class="fas fa-edit"></i> Edit Data Supplier / Vendor
    </h3>
    
    <form action="{{ url('/purchasing/vendors/' . $vendor->id) }}" method="POST" style="margin-top: 20px;">
        @csrf
        @method('PUT') <!-- Wajib ditambahkan untuk proses Update di Laravel -->

        <div style="margin-bottom: 15px;">
            <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 5px;">Vendor Code</label>
            <input type="text" name="vendor_code" value="{{ $vendor->vendor_code }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 5px;">Vendor Name</label>
            <input type="text" name="vendor_name" value="{{ $vendor->vendor_name }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>

        <div style="margin-bottom: 25px;">
            <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 5px;">Address</label>
            <input type="text" name="address" value="{{ $vendor->address }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 5px;">No. Telp</label>
            <input type="text" name="no_telp" value="{{ $vendor->no_telp ?? '' }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" style="background: #0ea5e9; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                <i class="fas fa-save"></i> Update Data
            </button>
            <a href="{{ url('/purchasing/vendors') }}" style="background: #94a3b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection