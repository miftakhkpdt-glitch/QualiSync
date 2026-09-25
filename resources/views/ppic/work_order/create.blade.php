@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 800px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    
    <!-- HEADER -->
    <div style="border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
        <a href="{{ url('/ppic/work-order') }}" style="background: #f1f5f9; color: #475569; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; text-decoration: none;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h3 style="margin: 0; color: #1e293b; font-size: 20px; font-weight: bold;">
            <i class="fas fa-clipboard-list" style="color: #0ea5e9; margin-right: 8px;"></i> Buat Work Order (WO) Baru
        </h3>
    </div>

    <!-- FORM INPUT -->
    <form action="{{ url('/ppic/work-order') }}" method="POST">
        @csrf

        <div style="margin-bottom: 20px;">
            <label style="font-weight: bold; font-size: 13px; color: #475569; display: block; margin-bottom: 8px;">Referensi Pesanan (Sales Order) *</label>
            <select name="sales_order_id" id="sales_order_id" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 5px; font-size: 14px;">
                <option value="">-- Pilih Dokumen Sales Order --</option>
                @foreach($salesOrders as $so)
                    <!-- Data atribut qty dikirim ke javascript -->
                    <option value="{{ $so->id }}" data-qty="{{ $so->qty }}">
                        PO-{{ $so->id }} | {{ $so->no_mm }} - {{ $so->nama_material }} (Qty Pesanan: {{ number_format($so->qty, 0) }})
                    </option>
                @endforeach
            </select>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
            <div>
                <label style="font-weight: bold; font-size: 13px; color: #475569; display: block; margin-bottom: 8px;">Target Produksi (Qty) *</label>
                <input type="number" name="qty_target" id="qty_target" required min="1" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 5px; font-size: 14px;" placeholder="Contoh: 10000">
                <small style="color: #64748b; font-size: 11px; margin-top: 4px; display: block;">*Otomatis mengikuti Qty SO, tapi bisa diedit parsial.</small>
            </div>

            <div>
                <label style="font-weight: bold; font-size: 13px; color: #475569; display: block; margin-bottom: 8px;">Rencana Tanggal Mulai *</label>
                <input type="date" name="start_date" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 5px; font-size: 14px;">
            </div>
        </div>

        <div style="text-align: right; border-top: 2px solid #e2e8f0; padding-top: 20px;">
            <button type="submit" style="background: #0ea5e9; color: white; padding: 12px 25px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; box-shadow: 0 2px 4px rgba(14,165,233,0.3); font-size: 14px;">
                <i class="fas fa-save" style="margin-right: 5px;"></i> Terbitkan Work Order
            </button>
        </div>
    </form>
</div>

<!-- SCRIPT PENGISIAN QTY OTOMATIS -->
<script>
    document.getElementById('sales_order_id').addEventListener('change', function() {
        // Ambil elemen option yang sedang dipilih
        var selectedOption = this.options[this.selectedIndex];
        // Tarik data qty dari option tersebut
        var qty = selectedOption.getAttribute('data-qty');
        
        // Masukkan ke input qty target
        if(qty) {
            document.getElementById('qty_target').value = qty;
        } else {
            document.getElementById('qty_target').value = '';
        }
    });
</script>
@endsection