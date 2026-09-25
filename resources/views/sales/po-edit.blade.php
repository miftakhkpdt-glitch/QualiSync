@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 1100px; margin: 20px auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h3 style="margin-bottom: 20px; color: #334155; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
        <i class="fas fa-edit"></i> Edit PO Customer (Multiple Items)
    </h3>

    <form action="{{ route('sales.po.update', $po->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label style="font-size: 13px; font-weight: bold;">Nama Customer</label>
                <input type="text" name="customer_name" value="{{ $po->customer_name ?? '' }}" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>
            <div>
                <label style="font-size: 13px; font-weight: bold;">Tanggal PO</label>
                <input type="date" name="po_date" value="{{ $po->po_date ?? '' }}" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>
            <div>
                <label style="font-size: 13px; font-weight: bold;">Nomor PO</label>
                <input type="text" name="no_po" value="{{ $po->no_po }}" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>
            <div>
                <label style="font-size: 13px; font-weight: bold;">Delivery Date</label>
                <input type="date" name="delivery_date" value="{{ $po->delivery_date }}" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin-bottom: 20px;">
        <label style="font-size: 14px; font-weight: bold; color: #334155; margin-bottom: 10px; display: block;">Daftar Item Produk (MM)</label>

        <div id="item-container">
            @foreach($poDetails as $index => $detail)
                <div class="item-row" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 10px; align-items: center; margin-bottom: 10px;">
                    <div>
                        <select name="items[{{ $index }}][no_mm]" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                            @foreach($materials as $mat)
                                <option value="{{ $mat->no_mm }}" {{ $detail->no_mm == $mat->no_mm ? 'selected' : '' }}>
                                    {{ $mat->no_mm }} - {{ $mat->nama_material }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <input type="number" step="0.01" name="items[{{ $index }}][price]" value="{{ $detail->price }}" placeholder="Harga" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                    </div>
                    <div>
                        <input type="number" name="items[{{ $index }}][qty]" value="{{ $detail->qty }}" placeholder="Qty" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                    </div>
                    <div>
                        <button type="button" onclick="removeItemRow(this)" style="background: #ef4444; color: white; border: none; padding: 9px 12px; border-radius: 4px; cursor: pointer;"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button" onclick="addItemRow()" style="background: #0ea5e9; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-size: 13px; margin-top: 10px; margin-bottom: 25px;">
            <i class="fas fa-plus"></i> Tambah Item Lain
        </button>

        <div>
            <button type="submit" style="background: #16a34a; color: white; padding: 10px 25px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
                <i class="fas fa-save"></i> Perbarui PO Customer
            </button>
        </div>
    </form>
</div>

<script>
    let itemIndex = {{ count($poDetails) }};

    function addItemRow() {
        let container = document.getElementById('item-container');
        let newRow = document.createElement('div');
        newRow.className = 'item-row';
        newRow.style.cssText = "display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 10px; align-items: center; margin-bottom: 10px;";
        
        newRow.innerHTML = `
            <div>
                <select name="items[${itemIndex}][no_mm]" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                    <option value="">-- Pilih Item --</option>
                    @foreach($materials as $mat)
                        <option value="{{ $mat->no_mm }}">{{ $mat->no_mm }} - {{ $mat->nama_material }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <input type="number" step="0.01" name="items[${itemIndex}][price]" placeholder="Harga" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>
            <div>
                <input type="number" name="items[${itemIndex}][qty]" placeholder="Qty" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>
            <div>
                <button type="button" onclick="removeItemRow(this)" style="background: #ef4444; color: white; border: none; padding: 9px 12px; border-radius: 4px; cursor: pointer;"><i class="fas fa-trash"></i></button>
            </div>
        `;
        container.appendChild(newRow);
        itemIndex++;
    }

    function removeItemRow(button) {
        let rows = document.getElementsByClassName('item-row');
        if (rows.length > 1) {
            button.closest('.item-row').remove();
        } else {
            alert('Minimal harus ada 1 item produk dalam PO!');
        }
    }
</script>
@endsection