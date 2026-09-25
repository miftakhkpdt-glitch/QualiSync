@extends('layouts.staff-layout')

@section('konten')
<!-- LOAD LIBRARY CSS SELECT2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- STYLE TAMBAHAN UNTUK MEMPERBAIKI TEKS PANJANG (AGAR TIDAK JEBOL) -->
<style>
    /* Mengunci batas maksimal Select2 agar tidak melebihi kolom */
    .select2-container {
        max-width: 100% !important;
    }
    
    /* Memaksa tinggi kotak Select2 menjadi dinamis mengikuti panjang teks */
    .select2-container .select2-selection--single {
        height: auto !important;
        min-height: 38px;
        border: 1px solid #ccc;
    }
    
    /* Memaksa teks yang panjang agar turun ke bawah (text-wrap) */
    .select2-container .select2-selection--single .select2-selection__rendered {
        white-space: normal !important;
        word-wrap: break-word;
        line-height: 1.5 !important;
        padding: 6px 12px;
        color: #334155;
    }
    
    /* Menyesuaikan posisi panah dropdown agar tetap di tengah vertikal */
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100% !important;
        top: 0;
    }
</style>

<div style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    <h3><i class="fas fa-truck-loading"></i> Warehouse Outgoing (Pengiriman Barang)</h3>
    <p style="color: #64748b;">Pencatatan pengiriman barang keluar yang otomatis mengurangi Outstanding PO (OSPO).</p>

    @if(session('success'))
        <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
        </div>
    @endif

    <!-- Form Input Outgoing -->
    <div style="background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h4 style="margin-top: 0; color: #1e293b;">Form Pengiriman Barang</h4>
        <form action="{{ route('warehouse.outgoing.store') }}" method="POST">
            @csrf
            
            <!-- BARIS 1: Informasi Dokumen & Item -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="font-weight: bold; font-size: 13px;">Tanggal Pengiriman <span style="color:red">*</span></label>
                    <input type="date" name="tanggal_pengiriman" class="form-control" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <div>
                    <label style="font-weight: bold; font-size: 13px;">Pilih No. PO & MM (Outstanding) <span style="color:red">*</span></label>
                    
                    <select id="pilih_po" name="no_po" class="form-control" required style="width: 100%;">
                        <option value="">-- Ketik No PO atau MM di sini --</option>
                        
                        <!-- DATA PO ASLI DARI DATABASE -->
                        @foreach($pos as $po)
                            <option value="{{ $po->no_po }}" 
                                    data-po="{{ $po->no_po }}" 
                                    data-mm="{{ $po->no_mm }}" 
                                    data-ospo="{{ $po->ospo }}"
                                    data-customer="{{ $po->nama_customer }}"
                                    data-alamat="{{ $po->alamat }}">
                                {{ $po->no_po }} | MM: {{ $po->no_mm }} | {{ $po->nama_produk }}
                            </option>
                        @endforeach
                    </select>
                    
                    <!-- Input tersembunyi hanya untuk no_mm saja -->
                    <input type="hidden" name="no_mm" id="no_mm">
                </div>
            </div>

            <!-- BARIS INFORMASI CUSTOMER (Otomatis Terisi) -->
            <div style="background: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 25px;">
                <h6 style="margin-top: 0; font-size: 13px; color: #475569; margin-bottom: 15px;"><i class="fas fa-building"></i> Tujuan Pengiriman (Otomatis)</h6>
                
                <div style="margin-bottom: 10px;">
                    <label style="font-size: 12px; font-weight: bold;">Nama Customer</label>
                    <input type="text" id="nama_customer" class="form-control" readonly style="width: 100%; padding: 8px; border: 1px solid #ccc; background: #e2e8f0; border-radius: 4px;" placeholder="-">
                </div>
                <div>
                    <label style="font-size: 12px; font-weight: bold;">Alamat Lengkap</label>
                    <textarea id="alamat_customer" class="form-control" rows="2" readonly style="width: 100%; padding: 8px; border: 1px solid #ccc; background: #e2e8f0; border-radius: 4px;" placeholder="-"></textarea>
                </div>
            </div>

            <!-- BARIS 2: Batch & Qty -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                <div>
                    <label style="font-weight: bold; font-size: 13px;">No. Batch <span style="color:red">*</span></label>
                    <input type="text" name="batch_number" id="batch_number" class="form-control" placeholder="Nomor batch produksi/material" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    
                    <!-- CATATAN KECIL UNTUK MULTI-BATCH -->
                    <small style="color: #64748b; display: block; margin-top: 5px; font-style: italic;">
                        *Catatan: Jika mengirim barang dari 2 Batch berbeda, harap input satu per satu (buat form baru).
                    </small>
                </div>
                <div>
                    <label style="font-weight: bold; font-size: 13px;">Qty Kirim (Pcs) <span style="color:red">*</span></label>
                    <input type="number" name="qty_kirim" id="qty_kirim" class="form-control" placeholder="Jumlah yang dikirim" required min="1" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    
                    <!-- Tempat munculnya teks Sisa OSPO & Peringatan -->
                    <small id="info_ospo" style="color: #b91c1c; font-weight: bold; display: block; margin-top: 5px;"></small>
                </div>
            </div>

            <hr style="border-top: 1px dashed #cbd5e1; margin-bottom: 20px;">

            <!-- BARIS 3: Tambahan Informasi Logistik -->
            <h5 style="font-size: 14px; font-weight: bold; margin-bottom: 15px; color: #334155;">Informasi Ekspedisi / Kendaraan (Opsional)</h5>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                <div>
                    <label style="font-size: 13px;">No. Shipment</label>
                    <input type="text" name="no_shipment" class="form-control" placeholder="Contoh: SHP-001" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <div>
                    <label style="font-size: 13px;">Fwd Agent / Ekspedisi</label>
                    <input type="text" name="fwd_agent" class="form-control" placeholder="Nama Ekspedisi" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                <div>
                    <label style="font-size: 13px;">No. Polisi</label>
                    <input type="text" name="no_polisi" class="form-control" placeholder="Contoh: B 1234 CD" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <div>
                    <label style="font-size: 13px;">Nama Supir</label>
                    <input type="text" name="nama_supir" class="form-control" placeholder="Nama Supir" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
            </div>

            <div style="margin-bottom: 25px;">
                <label style="font-size: 13px;">Keterangan Tambahan</label>
                <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan opsional..." style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;"></textarea>
            </div>

            <!-- TOMBOL SUBMIT -->
            <button type="submit" style="background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 4px; font-weight: bold; cursor: pointer;">
                <i class="fas fa-paper-plane"></i> Simpan Draft Pengiriman
            </button>
        </form>
    </div>

    <!-- Tabel Riwayat Outgoing -->
    <div style="background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h4 style="margin-top: 0; color: #1e293b;">Riwayat Pengiriman Warehouse Outgoing</h4>
        <table border="1" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr style="background: #f1f5f9; text-align: left;">
                    <th style="padding: 10px;">Tanggal Kirim</th>
                    <th style="padding: 10px;">No. PO</th>
                    <th style="padding: 10px;">No. MM</th>
                    <th style="padding: 10px;">Nama Item</th> <!-- Kolom Nama Item Ditambahkan -->
                    <th style="padding: 10px;">No. Batch</th>
                    <th style="padding: 10px;">Qty Kirim</th>
                </tr>
            </thead>
            <tbody>
                @forelse($outgoings as $out)
                <tr>
                    <td style="padding: 10px;">{{ \Carbon\Carbon::parse($out->tanggal_pengiriman)->format('d M Y') }}</td>
                    <td style="padding: 10px;"><b>{{ $out->no_po }}</b></td>
                    <td style="padding: 10px;">{{ $out->no_mm ?? '-' }}</td>
                    <td style="padding: 10px;">{{ $out->nama_produk ?? '-' }}</td> <!-- Menampilkan Nama Produk -->
                    <td style="padding: 10px;">{{ $out->batch_number }}</td>
                    <td style="padding: 10px; color: #16a34a; font-weight: bold;">{{ number_format($out->qty_kirim, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #64748b;">Belum ada riwayat pengiriman barang keluar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- LOAD LIBRARY JAVASCRIPT UNTUK SELECT2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // 1. Sulap dropdown menjadi fitur pencarian canggih
        $('#pilih_po').select2({
            placeholder: "-- Ketik No PO atau MM di sini --",
            allowClear: true,
            width: '100%' 
        });

        // 2. Aksi otomatis saat opsi dipilih
        $('#pilih_po').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var mm = selectedOption.data('mm');
            var ospo = selectedOption.data('ospo');
            var customer = selectedOption.data('customer');
            var alamat = selectedOption.data('alamat');

            // Isi input tersembunyi untuk proses penyimpanan data
            $('#no_mm').val(mm ? mm : '');
            
            // Isi otomatis informasi customer ke tampilan
            $('#nama_customer').val(customer ? customer : '');
            $('#alamat_customer').val(alamat ? alamat : '');
            
            // Atur batas maksimal input qty dan tampilkan informasi sisa OSPO
            if(ospo) {
                $('#info_ospo').text('Maksimal Qty Kirim (Sisa OSPO): ' + ospo);
                $('#qty_kirim').attr('max', ospo);
            } else {
                $('#info_ospo').text('');
                $('#qty_kirim').removeAttr('max');
            }
        });

        // 3. Peringatan instan jika Qty Kirim melebihi batas maksimal OSPO
        $('#qty_kirim').on('input', function() {
            var maxOspo = parseInt($(this).attr('max')) || 0;
            var inputQty = parseInt($(this).val()) || 0;

            if (maxOspo > 0 && inputQty > maxOspo) {
                $('#info_ospo').html('<span style="color: #b91c1c;">⚠️ Peringatan: Qty kirim (' + inputQty + ') melebihi sisa OSPO (' + maxOspo + ')!</span>');
            } else if (maxOspo > 0) {
                $('#info_ospo').text('Maksimal Qty Kirim (Sisa OSPO): ' + maxOspo);
            }
        });
    });
</script>
@endsection