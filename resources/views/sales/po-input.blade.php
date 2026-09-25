@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 950px; margin: 20px auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h3 id="form-title" style="margin-bottom: 20px; color: #334155; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
        <i class="fas fa-file-invoice"></i> Input Purchase Order (PO) Customer
    </h3>

    @if(session('success'))
        <div style="background: #dcfce7; color: #16a34a; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #bbf7d0;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #fecaca;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #fecaca;">
            <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
        </div>
    @endif

    <!-- Form Input & Update (Dinamis) -->
    <form id="po-form" action="{{ route('sales.po.store') }}" method="POST">
        @csrf
        <div id="method-container"></div> <!-- Tempat metode PUT jika mode edit -->

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
            <div>
                <label><b>Nama Customer</b></label>
                <input type="text" id="nama_customer" name="nama_customer" placeholder="Nama perusahaan customer" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>
            <div>
                <label><b>Tanggal PO (TGL)</b></label>
                <input type="date" id="tanggal_po" name="tanggal_po" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>
            <div>
                <label><b>Nomor PO</b></label>
                <input type="text" id="no_po" name="no_po" placeholder="Contoh: PO/2026/001" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>
            <div>
                <label><b>Delivery Date</b></label>
                <input type="date" id="delivery_date" name="delivery_date" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin-bottom: 20px;">

        <h4 style="color: #334155; margin-bottom: 10px;">Daftar Item Produk (MM)</h4>
        <div id="item-container">
            <!-- Baris Item dengan Kotak Split MM & Nama Item di Sampingnya -->
            <div class="item-row" style="background: #f8fafc; padding: 20px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 15px;">
                
                <!-- Kotak Split Baris Atas: No. MM & Nama Item Terisi Otomatis -->
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="font-size: 13px; color: #475569;"><b>Ketik / Pilih No. MM</b></label>
                        <input type="text" name="items[0][no_mm]" class="input-mm" list="list-mm" placeholder="Ketik No. MM..." required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;" oninput="updateItemDetails(this)">
                    </div>
                    <div>
                        <label style="font-size: 13px; color: #475569;"><b>Nama Item Otomatis</b></label>
                        <input type="text" name="items[0][nama_item]" class="input-nama-item" placeholder="Nama item akan muncul di sini..." required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px; background: #e2e8f0; color: #1e293b; font-weight: 500;" readonly>
                    </div>
                </div>

                <!-- Kotak Baris Bawah: Cust. Code, Price, Qty, dan Tombol Hapus -->
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 15px; align-items: end;">
                    <div>
                        <label style="font-size: 13px; color: #475569;"><b>Cust. Code (Opsional)</b></label>
                        <input type="text" name="items[0][kode_item_customer]" class="input-cust-code" placeholder="Contoh: 90021762" style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
                    </div>
                    <div>
                        <label style="font-size: 13px; color: #475569;"><b>Price (Pcs)</b></label>
                        <input type="number" step="0.01" name="items[0][price]" class="input-price" placeholder="0" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
                    </div>
                    <div>
                        <label style="font-size: 13px; color: #475569;"><b>Qty</b></label>
                        <input type="number" name="items[0][qty]" class="input-qty" placeholder="0" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;" oninput="calculatePriceByQty(this)">
                    </div>
                    <div>
                        <button type="button" onclick="removeItem(this)" style="background: #ef4444; color: white; border: none; padding: 9px 14px; border-radius: 4px; cursor: pointer; height: 38px;" title="Hapus Baris"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <datalist id="list-mm">
            @foreach($materials as $mat)
                <option value="{{ $mat->no_mm }}">{{ $mat->no_mm }} - {{ $mat->nama_material }}</option>
            @endforeach
        </datalist>

        <button type="button" onclick="addItemRow()" style="background: #0ea5e9; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; margin-bottom: 20px;">
            <i class="fas fa-plus"></i> Tambah Item Lain
        </button>

        <div style="display: flex; gap: 10px;">
            <button type="submit" id="submit-btn" style="background: #2563eb; color: white; padding: 12px 25px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                <i class="fas fa-save"></i> Simpan PO Customer
            </button>
            <button type="button" id="cancel-btn" onclick="resetFormToCreate()" style="background: #64748b; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; display: none;">
                Batal Edit
            </button>
        </div>
    </form>

    <hr style="border: 0; border-top: 2px dashed #cbd5e1; margin: 40px 0;">

    <h3 style="margin-bottom: 20px; color: #334155;"><i class="fas fa-history"></i> Riwayat Input PO Customer Aktif</h3>
    
    <!-- FORM UNTUK BULK DELETE -->
    <form action="{{ route('sales.po.bulk_delete') }}" method="POST" id="form-bulk-delete">
        @csrf
        @method('DELETE')
        
        <div style="margin-bottom: 10px; min-height: 35px;">
            <button type="submit" id="btn-hapus-banyak" style="background: #ef4444; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold; display: none;" onclick="return confirm('Yakin ingin memindahkan data terpilih ke riwayat hapus?')">
                <i class="fas fa-trash-alt"></i> Hapus Terpilih (<span id="jumlah-pilih">0</span>)
            </button>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                <thead>
                    <tr style="background: #f1f5f9; color: #334155; border-bottom: 2px solid #cbd5e1;">
                        <th style="padding: 12px 10px; width: 40px; text-align: center;">
                            <input type="checkbox" id="check-all" style="cursor: pointer; transform: scale(1.2);">
                        </th>
                        <th style="padding: 12px 10px;">Tanggal PO</th>
                        <th style="padding: 12px 10px;">No. PO</th>
                        <th style="padding: 12px 10px;">Customer</th>
                        <th style="padding: 12px 10px;">Cust. Code</th>
                        <th style="padding: 12px 10px;">No. MM</th>
                        <th style="padding: 12px 10px;">Nama Produk</th>
                        <th style="padding: 12px 10px; text-align: right;">Qty</th>
                        <th style="padding: 12px 10px; text-align: center;">Status</th>
                        <th style="padding: 12px 10px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatPO as $po)
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 10px; text-align: center;">
                                <input type="checkbox" name="ids[]" value="{{ $po->id }}" class="check-item" style="cursor: pointer; transform: scale(1.2);">
                            </td>
                            <td style="padding: 10px;">{{ date('d/m/Y', strtotime($po->tanggal_po)) }}</td>
                            <td style="padding: 10px;">{{ $po->no_po }}</td>
                            <td style="padding: 10px;">{{ $po->nama_customer }}</td>
                            <td style="padding: 10px; font-weight: bold;">{{ $po->kode_item_customer ?? '-' }}</td>
                            <td style="padding: 10px;">{{ $po->no_mm }}</td>
                            <td style="padding: 10px;">{{ $po->nama_produk }}</td>
                            <td style="padding: 10px; text-align: right;">{{ number_format($po->qty, 0, ',', '.') }}</td>
                            <td style="padding: 10px; text-align: center;">
                                <span style="background: #dbeafe; color: #1e40af; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">{{ $po->status }}</span>
                            </td>
                            <td style="padding: 10px; text-align: center; display: flex; gap: 5px; justify-content: center;">
                                <button type="button" onclick="editPoInline('{{ $po->id }}', '{{ $po->nama_customer }}', '{{ $po->tanggal_po }}', '{{ $po->no_po }}', '{{ $po->delivery_date ?? $po->tanggal_po }}', '{{ $po->no_mm }}', '{{ addslashes($po->nama_produk) }}', '{{ $po->qty }}', '{{ $po->kode_item_customer ?? '' }}')" style="background: #f59e0b; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: bold;" title="Edit PO">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button type="button" onclick="hapusSatuan({{ $po->id }})" style="background: #ef4444; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;" title="Hapus PO">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" style="text-align: center; padding: 20px; color: #64748b;">Tidak ada data riwayat PO.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <!-- Form tersembunyi untuk proses hapus satuan -->
    <form id="form-hapus-satuan" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <hr style="border: 0; border-top: 2px dashed #cbd5e1; margin: 40px 0;">

    <!-- TABEL RIWAYAT PO DIHAPUS -->
    <h4 style="margin-bottom: 15px; color: #b91c1c; font-size: 16px;"><i class="fas fa-history"></i> Riwayat PO Dihapus</h4>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; background: #fff5f5;">
            <thead>
                <tr style="background: #fee2e2; color: #991b1b; border-bottom: 2px solid #fca5a5;">
                    <th style="padding: 10px;">No. PO</th>
                    <th style="padding: 10px;">Customer</th>
                    <th style="padding: 10px;">No. MM & Produk</th>
                    <th style="padding: 10px; text-align: right;">Qty</th>
                    <th style="padding: 10px;">Dihapus Oleh</th>
                    <th style="padding: 10px;">Waktu Dihapus</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deletedPO ?? [] as $dpo)
                    <tr style="border-bottom: 1px solid #fecaca;">
                        <td style="padding: 10px; font-weight: bold;">{{ $dpo->no_po }}</td>
                        <td style="padding: 10px;">{{ $dpo->nama_customer }}</td>
                        <td style="padding: 10px; color: #1e293b;">
                            <b>{{ $dpo->no_mm }}</b><br>
                            <span style="color: #64748b; font-size: 12px;">{{ $dpo->nama_produk }}</span>
                        </td>
                        <td style="padding: 10px; text-align: right;">{{ number_format($dpo->qty, 0, ',', '.') }}</td>
                        <td style="padding: 10px;">
                            <span style="background: #f87171; color: white; padding: 3px 8px; border-radius: 4px; font-size: 12px;">
                                <i class="fas fa-user-times"></i> {{ $dpo->deleted_by ?? 'System' }}
                            </span>
                        </td>
                        <td style="padding: 10px; font-size: 12px; color: #64748b;">
                            {{ $dpo->deleted_at ? date('d M Y, H:i', strtotime($dpo->deleted_at)) : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px; color: #991b1b;">Belum ada riwayat PO yang dihapus.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<script>
    const materialsData = @json($materials);
    const priceTiers = @json($priceTiers);

    function editPoInline(id, customer, tglPo, noPo, deliveryDate, noMm, namaProduk, qty, custCode = '') {
        window.scrollTo({ top: 0, behavior: 'smooth' });

        let form = document.getElementById('po-form');
        form.action = "{{ url('/sales/po/update') }}/" + id;

        document.getElementById('method-container').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('form-title').innerHTML = '<i class="fas fa-edit"></i> Edit PO Customer: ' + noPo;
        let submitBtn = document.getElementById('submit-btn');
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Perbarui PO Customer';
        submitBtn.style.background = '#16a34a';
        document.getElementById('cancel-btn').style.display = 'inline-block';

        document.getElementById('nama_customer').value = customer;
        document.getElementById('tanggal_po').value = tglPo;
        document.getElementById('no_po').value = noPo;
        document.getElementById('delivery_date').value = deliveryDate;

        let firstRow = document.querySelector('.item-row');
        firstRow.querySelector('.input-mm').value = noMm;
        firstRow.querySelector('.input-nama-item').value = namaProduk;
        
        let qtyInput = firstRow.querySelector('.input-qty');
        qtyInput.value = qty;
        
        // Memasukkan nilai Cust Code ke dalam form edit
        let custCodeInput = firstRow.querySelector('.input-cust-code');
        if(custCodeInput) custCodeInput.value = (custCode !== '-' && custCode !== 'null') ? custCode : '';

        calculatePriceByQty(qtyInput);
    }

    function resetFormToCreate() {
        let form = document.getElementById('po-form');
        form.action = "{{ route('sales.po.store') }}";
        document.getElementById('method-container').innerHTML = '';

        document.getElementById('form-title').innerHTML = '<i class="fas fa-file-invoice"></i> Input Purchase Order (PO) Customer';
        let submitBtn = document.getElementById('submit-btn');
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Simpan PO Customer';
        submitBtn.style.background = '#2563eb';
        document.getElementById('cancel-btn').style.display = 'none';

        form.reset();
    }

    let rowIndex = 1;
    function addItemRow() {
        let container = document.getElementById('item-container');
        let newRow = document.createElement('div');
        newRow.className = 'item-row';
        newRow.style.cssText = "background: #f8fafc; padding: 20px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 15px;";
        
        newRow.innerHTML = `
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="font-size: 13px; color: #475569;"><b>Ketik / Pilih No. MM</b></label>
                    <input type="text" name="items[${rowIndex}][no_mm]" class="input-mm" list="list-mm" placeholder="Ketik No. MM..." required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;" oninput="updateItemDetails(this)">
                </div>
                <div>
                    <label style="font-size: 13px; color: #475569;"><b>Nama Item Otomatis</b></label>
                    <input type="text" name="items[${rowIndex}][nama_item]" class="input-nama-item" placeholder="Nama item akan muncul di sini..." required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px; background: #e2e8f0; color: #1e293b; font-weight: 500;" readonly>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 15px; align-items: end;">
                <div>
                    <label style="font-size: 13px; color: #475569;"><b>Cust. Code (Opsional)</b></label>
                    <input type="text" name="items[${rowIndex}][kode_item_customer]" class="input-cust-code" placeholder="Contoh: 90021762" style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div>
                    <label style="font-size: 13px; color: #475569;"><b>Price (Pcs)</b></label>
                    <input type="number" step="0.01" name="items[${rowIndex}][price]" class="input-price" placeholder="0" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div>
                    <label style="font-size: 13px; color: #475569;"><b>Qty</b></label>
                    <input type="number" name="items[${rowIndex}][qty]" class="input-qty" placeholder="0" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;" oninput="calculatePriceByQty(this)">
                </div>
                <div>
                    <button type="button" onclick="removeItem(this)" style="background: #ef4444; color: white; border: none; padding: 9px 14px; border-radius: 4px; cursor: pointer; height: 38px;" title="Hapus Baris"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        `;
        container.appendChild(newRow);
        rowIndex++;
    }

    function removeItem(button) {
        let rows = document.getElementsByClassName('item-row');
        if (rows.length > 1) {
            button.closest('.item-row').remove();
        } else {
            alert('Minimal harus ada 1 item produk dalam PO!');
        }
    }

    function updateItemDetails(inputElement) {
        let enteredValue = inputElement.value;
        let row = inputElement.closest('.item-row');
        let namaItemInput = row.querySelector('.input-nama-item');
        let matchedMaterial = materialsData.find(mat => mat.no_mm == enteredValue);

        if (matchedMaterial) {
            namaItemInput.value = matchedMaterial.nama_material;
            calculatePriceByQty(row.querySelector('.input-qty'));
        } else {
            namaItemInput.value = '';
        }
    }

    function calculatePriceByQty(qtyElement) {
        if (!qtyElement) return;
        
        let row = qtyElement.closest('.item-row');
        let mm = row.querySelector('.input-mm').value;
        let priceInput = row.querySelector('.input-price');
        let qty = parseInt(qtyElement.value) || 0;

        if (qty <= 0 || !mm) {
            priceInput.value = 0;
            return;
        }

        let matchedTier = priceTiers.find(tier => 
            tier.no_mm == mm && 
            qty >= parseInt(tier.min_qty) && 
            qty <= parseInt(tier.max_qty)
        );

        if (matchedTier) {
            priceInput.value = matchedTier.price;
        } else {
            priceInput.value = 0;
        }
    }

    // --- SKRIP FITUR CHECKBOX & BULK DELETE UNTUK PO ---
    const checkAll = document.getElementById('check-all');
    const checkItems = document.querySelectorAll('.check-item');
    const btnHapusBanyak = document.getElementById('btn-hapus-banyak');
    const spanJumlahPilih = document.getElementById('jumlah-pilih');

    function toggleTombolHapus() {
        let jumlahDicentang = document.querySelectorAll('.check-item:checked').length;
        spanJumlahPilih.innerText = jumlahDicentang;
        
        if (jumlahDicentang > 0) {
            btnHapusBanyak.style.display = 'inline-block';
        } else {
            btnHapusBanyak.style.display = 'none';
            if(checkAll) checkAll.checked = false;
        }
    }

    if(checkAll) {
        checkAll.addEventListener('change', function() {
            checkItems.forEach(item => {
                item.checked = this.checked;
            });
            toggleTombolHapus();
        });
    }

    checkItems.forEach(item => {
        item.addEventListener('change', toggleTombolHapus);
    });

    function hapusSatuan(id) {
        if(confirm('Yakin ingin memindahkan PO ini ke riwayat hapus?')) {
            let formSatuan = document.getElementById('form-hapus-satuan');
            formSatuan.action = "{{ url('/sales/po/delete') }}/" + id;
            formSatuan.submit();
        }
    }
</script>
@endsection