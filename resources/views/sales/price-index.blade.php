@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 950px; margin: 20px auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h3 style="margin-bottom: 20px; color: #334155; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
        <i class="fas fa-tags"></i> Pengaturan Harga Berjenjang (Price Tier)
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

    <!-- Form Input -->
    <form action="{{ route('sales.prices.store') }}" method="POST" style="background: #f8fafc; padding: 20px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 25px;">
        @csrf
        
        <!-- Kotak Split: No. MM & Nama Item (Baris Atas) -->
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label style="font-size: 13px;"><b>Ketik / Pilih No. MM</b></label>
                <input type="text" id="input-mm-price" name="no_mm" list="list-mm" placeholder="Ketik No. MM..." required style="width: 100%; padding: 9px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;" oninput="updateNamaItemPrice(this)">
            </div>
            <div>
                <label style="font-size: 13px; color: #475569;"><b>Nama Item Otomatis</b></label>
                <input type="text" id="input-nama-price" placeholder="Nama item akan muncul di sini..." required style="width: 100%; padding: 9px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px; background: #e2e8f0; color: #1e293b; font-weight: 500;" readonly>
            </div>
        </div>

        <datalist id="list-mm">
            @foreach($materials as $mat)
                <option value="{{ $mat->no_mm }}">{{ $mat->no_mm }} - {{ $mat->nama_material }}</option>
            @endforeach
        </datalist>

        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin-bottom: 15px;">
        <label style="font-size: 14px; color: #334155; margin-bottom: 10px; display: block;"><b>Aturan Range & Harga</b></label>

        <div id="range-container">
            <div class="range-row" style="display: grid; grid-template-columns: 1fr 1fr 1.5fr auto; gap: 10px; align-items: end; margin-bottom: 12px;">
                <div>
                    <label style="font-size: 12px; color: #64748b;">Range Min Qty</label>
                    <input type="number" name="ranges[0][min_qty]" placeholder="Contoh: 1" required style="width: 100%; padding: 8px; margin-top: 3px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div>
                    <label style="font-size: 12px; color: #64748b;">Range Max Qty</label>
                    <input type="number" name="ranges[0][max_qty]" placeholder="Contoh: 1000" required style="width: 100%; padding: 8px; margin-top: 3px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div>
                    <label style="font-size: 12px; color: #64748b;">Harga (Pcs)</label>
                    <input type="number" step="0.01" name="ranges[0][price]" placeholder="0" required style="width: 100%; padding: 8px; margin-top: 3px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div>
                    <button type="button" onclick="removeRangeRow(this)" style="background: #ef4444; color: white; border: none; padding: 9px 12px; border-radius: 4px; cursor: pointer;" title="Hapus Baris">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>

        <button type="button" onclick="addRangeRow()" style="background: #0ea5e9; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-size: 13px; margin-top: 5px; margin-bottom: 20px;">
            <i class="fas fa-plus"></i> Tambah Range
        </button>

        <div>
            <button type="submit" style="background: #2563eb; color: white; padding: 12px 25px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: bold;">
                <i class="fas fa-save"></i> Simpan Semua Aturan Harga
            </button>
        </div>
    </form>

    <!-- Header Tabel & Filter -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; margin-top: 30px;">
        <h4 style="margin: 0; color: #334155; font-size: 16px;">Daftar Harga Aktif di Sistem</h4>
        
        <form action="{{ route('sales.prices.index') }}" method="GET" style="display: flex; gap: 8px; align-items: center;">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Filter No. MM..." style="padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px; width: 200px;">
            <button type="submit" style="background: #0ea5e9; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px;">
                <i class="fas fa-search"></i> Filter
            </button>
            @if(isset($search) && $search != '')
                <a href="{{ route('sales.prices.index') }}" style="background: #64748b; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 13px; display: flex; align-items: center;" title="Reset Filter">
                    <i class="fas fa-redo"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <!-- FORM UNTUK BULK DELETE (Menghapus banyak) -->
    <form action="{{ route('sales.prices.bulk_delete') }}" method="POST" id="form-bulk-delete">
        @csrf
        @method('DELETE')
        
        <div style="margin-bottom: 10px; min-height: 35px;">
            <button type="submit" id="btn-hapus-banyak" style="background: #ef4444; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold; display: none;" onclick="return confirm('Yakin ingin memindahkan data terpilih ke riwayat hapus?')">
                <i class="fas fa-trash-alt"></i> Hapus Terpilih (<span id="jumlah-pilih">0</span>)
            </button>
        </div>

        <!-- Tabel Daftar Harga Aktif -->
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
            <thead>
                <tr style="background: #f1f5f9; color: #334155; border-bottom: 2px solid #cbd5e1;">
                    <th style="padding: 10px; width: 40px; text-align: center;">
                        <input type="checkbox" id="check-all" style="cursor: pointer; transform: scale(1.2);">
                    </th>
                    <th style="padding: 10px;">No. MM & Nama Item</th>
                    <th style="padding: 10px;">Min Qty</th>
                    <th style="padding: 10px;">Max Qty</th>
                    <th style="padding: 10px;">Harga (Pcs)</th>
                    <th style="padding: 10px;">Diinput Oleh</th>
                    <th style="padding: 10px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($prices as $p)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 10px; text-align: center;">
                            <input type="checkbox" name="ids[]" value="{{ $p->id }}" class="check-item" style="cursor: pointer; transform: scale(1.2);">
                        </td>
                        <td style="padding: 10px; color: #1e293b;">
                            <b>{{ $p->no_mm }}</b><br>
                            <span style="color: #64748b; font-size: 12px;">{{ $p->nama_material ?? '-' }}</span>
                        </td>
                        <td style="padding: 10px;">{{ number_format($p->min_qty) }}</td>
                        <td style="padding: 10px;">{{ $p->max_qty ? number_format($p->max_qty) : 'Tak Terbatas' }}</td>
                        <td style="padding: 10px; font-weight: bold; color: #16a34a;">Rp {{ number_format($p->price, 2, ',', '.') }}</td>
                        <td style="padding: 10px;">
                            <span style="background: #e0f2fe; color: #0284c7; padding: 3px 8px; border-radius: 4px; font-size: 12px;">
                                <i class="fas fa-user-edit"></i> {{ $p->created_by ?? 'System' }}
                            </span>
                        </td>
                        <td style="padding: 10px; text-align: center;">
                            <button type="button" onclick="hapusSatuan({{ $p->id }})" style="background: #ef4444; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer;" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px; color: #64748b;">Belum ada aturan harga yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </form>

    <form id="form-hapus-satuan" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <hr style="border: 0; border-top: 2px dashed #cbd5e1; margin: 40px 0;">

    <!-- TABEL RIWAYAT HARGA DIHAPUS -->
    <h4 style="margin-bottom: 15px; color: #b91c1c; font-size: 16px;"><i class="fas fa-history"></i> Riwayat Harga Dihapus</h4>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; background: #fff5f5;">
            <thead>
                <tr style="background: #fee2e2; color: #991b1b; border-bottom: 2px solid #fca5a5;">
                    <th style="padding: 10px;">No. MM & Nama Item</th>
                    <th style="padding: 10px;">Min Qty</th>
                    <th style="padding: 10px;">Max Qty</th>
                    <th style="padding: 10px;">Harga Lama</th>
                    <th style="padding: 10px;">Dihapus Oleh</th>
                    <th style="padding: 10px;">Waktu Dihapus</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deletedPrices ?? [] as $dp)
                    <tr style="border-bottom: 1px solid #fecaca;">
                        <td style="padding: 10px; color: #1e293b;">
                            <b>{{ $dp->no_mm }}</b><br>
                            <span style="color: #64748b; font-size: 12px;">{{ $dp->nama_material ?? '-' }}</span>
                        </td>
                        <td style="padding: 10px;">{{ number_format($dp->min_qty) }}</td>
                        <td style="padding: 10px;">{{ $dp->max_qty ? number_format($dp->max_qty) : 'Tak Terbatas' }}</td>
                        <td style="padding: 10px; font-weight: bold; color: #64748b;"><del>Rp {{ number_format($dp->price, 2, ',', '.') }}</del></td>
                        <td style="padding: 10px;">
                            <span style="background: #f87171; color: white; padding: 3px 8px; border-radius: 4px; font-size: 12px;">
                                <i class="fas fa-user-times"></i> {{ $dp->deleted_by ?? 'System' }}
                            </span>
                        </td>
                        <td style="padding: 10px; font-size: 12px; color: #64748b;">
                            {{ $dp->deleted_at ? date('d M Y, H:i', strtotime($dp->deleted_at)) : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px; color: #991b1b;">Belum ada riwayat harga yang dihapus.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<script>
    const materialsDataForPrice = @json($materials);

    function updateNamaItemPrice(inputElement) {
        let enteredValue = inputElement.value;
        let namaItemInput = document.getElementById('input-nama-price');
        let matchedMaterial = materialsDataForPrice.find(mat => mat.no_mm == enteredValue);

        if (matchedMaterial) {
            namaItemInput.value = matchedMaterial.nama_material;
        } else {
            namaItemInput.value = '';
        }
    }

    let rangeIndex = 1;
    function addRangeRow() {
        let container = document.getElementById('range-container');
        let newRow = document.createElement('div');
        newRow.className = 'range-row';
        newRow.style.cssText = "display: grid; grid-template-columns: 1fr 1fr 1.5fr auto; gap: 10px; align-items: end; margin-bottom: 12px;";
        newRow.innerHTML = `
            <div><input type="number" name="ranges[${rangeIndex}][min_qty]" placeholder="Contoh: 1001" required style="width: 100%; padding: 8px; margin-top: 3px; border: 1px solid #cbd5e1; border-radius: 4px;"></div>
            <div><input type="number" name="ranges[${rangeIndex}][max_qty]" placeholder="Contoh: 2000" required style="width: 100%; padding: 8px; margin-top: 3px; border: 1px solid #cbd5e1; border-radius: 4px;"></div>
            <div><input type="number" step="0.01" name="ranges[${rangeIndex}][price]" placeholder="0" required style="width: 100%; padding: 8px; margin-top: 3px; border: 1px solid #cbd5e1; border-radius: 4px;"></div>
            <div><button type="button" onclick="removeRangeRow(this)" style="background: #ef4444; color: white; border: none; padding: 9px 12px; border-radius: 4px; cursor: pointer;"><i class="fas fa-trash"></i></button></div>
        `;
        container.appendChild(newRow);
        rangeIndex++;
    }

    function removeRangeRow(button) {
        let rows = document.getElementsByClassName('range-row');
        if (rows.length > 1) { button.closest('.range-row').remove(); }
        else { alert('Minimal harus ada 1 baris range harga!'); }
    }

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
        if(confirm('Yakin ingin memindahkan data ini ke riwayat hapus?')) {
            let formSatuan = document.getElementById('form-hapus-satuan');
            formSatuan.action = "{{ url('/sales/prices') }}/" + id;
            formSatuan.submit();
        }
    }
</script>
@endsection