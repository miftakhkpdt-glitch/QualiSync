@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 1400px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h3 style="margin-bottom: 5px; color: #334155; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
        <i class="fas fa-edit"></i> Edit Incoming Material & Karantina
    </h3>
    <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">Perbarui data pencatatan material masuk.</p>

    <!-- FORM EDIT INCOMING -->
    <form action="{{ route('warehouse.incoming.update', $incoming->id) }}" method="POST" style="background: #f8fafc; padding: 20px; border-radius: 6px; border: 1px solid #e2e8f0;">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 15px;">
            <div>
                <label style="font-size: 13px; font-weight: bold;">Date</label>
                <input type="date" name="date" value="{{ $incoming->date }}" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>

            <div>
                <label style="font-size: 13px; font-weight: bold;">No. MM</label>
                <input type="text" name="mm" id="input_mm" value="{{ $incoming->mm }}" list="list-mm" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;" oninput="autofillMaterial(this)">
                <datalist id="list-mm">
                    @foreach($materials as $mat)
                        <option value="{{ $mat->no_mm }}">{{ $mat->no_mm }} - {{ $mat->nama_material }}</option>
                    @endforeach
                </datalist>
            </div>

            <div>
                <label style="font-size: 13px; font-weight: bold;">Item Name</label>
                <input type="text" name="item_name" id="input_item_name" value="{{ $incoming->item_name }}" readonly required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px; background: #f1f5f9;">
            </div>

            <div>
                <label style="font-size: 13px; font-weight: bold;">Quantity</label>
                <input type="number" step="0.01" name="quantity" value="{{ $incoming->quantity }}" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>

            <div>
                <label style="font-size: 13px; font-weight: bold;">Uom</label>
                <input type="text" name="uom" id="input_uom" value="{{ $incoming->uom }}" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>

            <div>
                <label style="font-size: 13px; font-weight: bold;">STPB Number</label>
                <input type="text" name="stpb_number" value="{{ $incoming->stpb_number }}" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>

            <div>
                <label style="font-size: 13px; font-weight: bold;">PO KPDT Number</label>
                <input type="text" name="po_kpdt_number" value="{{ $incoming->po_kpdt_number }}" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>

            <div>
                <label style="font-size: 13px; font-weight: bold;">SJ Supplier Number</label>
                <input type="text" name="sj_supplier_number" value="{{ $incoming->sj_supplier_number }}" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>

            <div>
                <label style="font-size: 13px; font-weight: bold;">Vendor Code</label>
                <input type="text" name="vendor_code" id="input_vendor_code" value="{{ $incoming->vendor_code }}" list="list-vendor" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;" oninput="autofillVendor(this)">
                <datalist id="list-vendor">
                    @foreach($vendors as $ven)
                        <option value="{{ $ven->vendor_code }}">{{ $ven->vendor_code }} - {{ $ven->vendor_name }}</option>
                    @endforeach
                </datalist>
            </div>

            <div>
                <label style="font-size: 13px; font-weight: bold;">Vendor Name</label>
                <input type="text" name="vendor_name" id="input_vendor_name" value="{{ $incoming->vendor_name }}" readonly required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px; background: #f1f5f9;">
            </div>

            <div style="grid-column: span 2;">
                <label style="font-size: 13px; font-weight: bold;">Address</label>
                <input type="text" name="address" id="input_address" value="{{ $incoming->address }}" readonly required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px; background: #f1f5f9;">
            </div>

            <div style="grid-column: span 4;">
                <label style="font-size: 13px; font-weight: bold;">Remarks</label>
                <input type="text" name="remarks" value="{{ $incoming->remarks }}" style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" style="background: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                <i class="fas fa-save"></i> Perbarui Data
            </button>
            <a href="{{ route('warehouse.incoming.index') }}" style="background: #94a3b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">
    Batal
</a>
        </div>
    </form>
</div>

<script>
    const materialsData = @json($materials);
    const vendorsData = @json($vendors);

    function autofillMaterial(inputEl) {
        let val = inputEl.value;
        let matched = materialsData.find(m => m.no_mm == val);
        if (matched) {
            document.getElementById('input_item_name').value = matched.nama_material;
            document.getElementById('input_uom').value = matched.satuan ?? 'Pcs';
        } else {
            document.getElementById('input_item_name').value = '';
            document.getElementById('input_uom').value = '';
        }
    }

    function autofillVendor(inputEl) {
        let val = inputEl.value;
        let matched = vendorsData.find(v => v.vendor_code == val);
        if (matched) {
            document.getElementById('input_vendor_name').value = matched.vendor_name;
            document.getElementById('input_address').value = matched.address;
        } else {
            document.getElementById('input_vendor_name').value = '';
            document.getElementById('input_address').value = '';
        }
    }
</script>
@endsection