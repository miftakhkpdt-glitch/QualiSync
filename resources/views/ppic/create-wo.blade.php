@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 600px; margin: 20px auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h3 style="margin-bottom: 20px; color: #334155; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
        <i class="fas fa-clipboard-check" style="color: #0284c7;"></i> Form Rilis Work Order (WO)
    </h3>

    <!-- TAMPILAN INFORMASI (Ditarik otomatis dari URL) -->
    <div style="background: #f0f9ff; border: 1px solid #bae6fd; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-size: 14px;">
        <div style="margin-bottom: 10px;">
            <span style="color: #64748b; font-weight: bold;">Nomor Material (MM):</span>
            <span style="font-size: 16px; color: #0369a1; font-weight: bold; display: block;">{{ request('no_mm') }}</span>
        </div>
        <div>
            <span style="color: #64748b; font-weight: bold;">Target Kebutuhan Produksi:</span>
            <span style="font-size: 16px; color: #dc2626; font-weight: bold; display: block;">{{ number_format((float)request('qty')) }} Pcs</span>
        </div>
    </div>

    <form action="{{ route('ppic.wo.store') }}" method="POST">
        @csrf
        
        <!-- DATA TERSEMBUNYI UNTUK DISIMPAN KE DATABASE -->
        <input type="hidden" name="no_mm" value="{{ request('no_mm') }}">
        <input type="hidden" name="qty_target" value="{{ request('qty') }}">
        <input type="hidden" name="sales_order_id" value=""> <!-- Dikosongkan sementara -->

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #334155;">Tanggal Mulai Produksi (Start Date)</label>
            <input type="date" name="start_date" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box; font-family: inherit;">
        </div>

        <button type="submit" style="background: #0284c7; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; font-size: 14px; transition: background 0.2s;">
            <i class="fas fa-industry"></i> Terbitkan Work Order
        </button>
    </form>
</div>
@endsection