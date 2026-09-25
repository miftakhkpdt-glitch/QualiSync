@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 1400px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    
    <div style="margin-bottom: 25px; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px;">
        <h3 style="margin: 0; color: #1e293b; font-size: 20px;">
            <i class="fas fa-boxes"></i> Manajemen Stok Karantina (Hold / NG)
        </h3>
        <p style="color: #64748b; font-size: 13px; margin: 5px 0 0 0;">Dikelola oleh Purchasing. Lakukan Mutasi untuk mengembalikan barang ke departemen lain.</p>
    </div>

    <!-- Alert Sukses / Error -->
    @if(session('success'))
        <div style="background: #dcfce7; color: #16a34a; padding: 10px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #bbf7d0; font-size: 13px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: #fee2e2; color: #dc2626; padding: 10px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #fecaca; font-size: 13px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- ========================================== -->
    <!-- TABEL 1: STOK KARANTINA (AKUMULASI TOTAL)  -->
    <!-- ========================================== -->
    <div style="margin-bottom: 40px;">
        <h4 style="color: #0f172a; margin-bottom: 10px; font-size: 16px;">1. Stok Aktual Karantina (Akumulasi)</h4>
        <div style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 6px;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead>
                    <tr style="background: #1e293b; color: white;">
                        <th style="padding: 12px 10px; text-align: center;">No</th>
                        <th style="padding: 12px 10px; text-align: center;">Status</th>
                        <th style="padding: 12px 10px;">No. MM</th>
                        <th style="padding: 12px 10px;">Nama Material</th>
                        <th style="padding: 12px 10px;">Keterangan / Alasan</th>
                        <th style="padding: 12px 10px; text-align: right;">Total Qty (Hold/NG)</th>
                        <th style="padding: 12px 10px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stokKarantina as $index => $stok)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 10px; text-align: center;">{{ $index + 1 }}</td>
                        <td style="padding: 10px; text-align: center;">
                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; background: {{ $stok->status_karantina == 'Hold' ? '#fef3c7; color: #d97706;' : '#fee2e2; color: #b91c1c;' }}">
                                {{ $stok->status_karantina }}
                            </span>
                        </td>
                        <td style="padding: 10px; font-weight: bold;">{{ $stok->no_mm }}</td>
                        <td style="padding: 10px;">{{ $stok->nama_material ?? '-' }}</td>
                        <td style="padding: 10px; color: #64748b; font-size: 12px;">{{ $stok->keterangan ?? '-' }}</td>
                        <td style="padding: 10px; text-align: right; font-weight: bold; color: #b91c1c;">
                            {{ number_format($stok->qty, 2) }}
                        </td>
                        <td style="padding: 10px; text-align: center;">
                            <!-- Tombol ini memicu Modal Mutasi -->
                            <button type="button" onclick="bukaModalMutasi('{{ $stok->no_mm }}', '{{ $stok->status_karantina }}')" style="background: #0ea5e9; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: bold;">
                                <i class="fas fa-exchange-alt"></i> Mutasi Keluar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px; color: #64748b;">Karantina kosong. Tidak ada stok Hold/NG.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TABEL 2: RIWAYAT MASUK KE KARANTINA        -->
    <!-- ========================================== -->
    <div style="margin-bottom: 40px;">
        <h4 style="color: #0f172a; margin-bottom: 10px; font-size: 16px;">2. Riwayat Masuk ke Karantina (Detail per Transaksi)</h4>
        <div style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 6px;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead>
                    <tr style="background: #f8fafc; color: #334155; border-bottom: 2px solid #cbd5e1;">
                        <th style="padding: 10px;">Waktu Masuk</th>
                        <th style="padding: 10px;">Dari Dept</th>
                        <th style="padding: 10px;">Status</th>
                        <th style="padding: 10px;">MM & Nama Material</th>
                        <th style="padding: 10px;">Keterangan</th>
                        <th style="padding: 10px; text-align: right;">Qty Masuk</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatMasuk as $masuk)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 10px;">{{ date('d M Y, H:i', strtotime($masuk->tanggal)) }}</td>
                        <td style="padding: 10px; font-weight: bold; color: #475569;">{{ $masuk->dari_dept }}</td>
                        <td style="padding: 10px;">
                            <span style="padding: 3px 6px; border-radius: 4px; font-size: 11px; font-weight: bold; background: {{ $masuk->status == 'Hold' ? '#fef3c7; color: #d97706;' : '#fee2e2; color: #b91c1c;' }}">
                                {{ $masuk->status }}
                            </span>
                        </td>
                        <td style="padding: 10px;">
                            <b>{{ $masuk->mm }}</b><br>
                            <span style="color: #64748b; font-size: 11px;">{{ $masuk->nama_material ?? '-' }}</span>
                        </td>
                        <td style="padding: 10px; color: #dc2626; font-size: 12px;">{{ $masuk->keterangan ?? '-' }}</td>
                        <td style="padding: 10px; text-align: right; font-weight: bold; color: #b91c1c;">+ {{ number_format($masuk->qty, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px; color: #64748b;">Belum ada riwayat material masuk ke Karantina.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- 3. RIWAYAT MUTASI KELUAR (Dari Karantina ke Dept Lain)    -->
    <!-- ========================================================= -->
    <div style="margin-top: 40px;">
        <h4 style="color: #0f172a; margin-bottom: 15px; font-size: 16px;">
            <i class="fas fa-sign-out-alt"></i> 3. Riwayat Mutasi Keluar dari Karantina
        </h4>
        <div style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 6px;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead>
                    <tr style="background: #64748b; color: white;">
                        <!-- Tambahan info Jam pada Header -->
                        <th style="padding: 12px 10px;">Tanggal & Jam</th>
                        <th style="padding: 12px 10px;">Tujuan Dept</th>
                        <th style="padding: 12px 10px;">MM & Nama Material</th>
                        <th style="padding: 12px 10px; text-align: right;">Qty Dikirim</th>
                        <th style="padding: 12px 10px; text-align: center;">Status Mutasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatKeluar as $keluar)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <!-- Menggunakan created_at untuk mendapatkan tanggal beserta jam -->
                        <td style="padding: 10px; color: #475569;">
                            {{ date('d M Y, H:i', strtotime($keluar->created_at)) }}
                        </td>
                        <td style="padding: 10px; font-weight: bold; color: #0369a1;">
                            {{ $keluar->ke_dept }}
                        </td>
                        <td style="padding: 10px;">
                            <b>{{ $keluar->mm }}</b><br>
                            <span style="color: #64748b; font-size: 11px;">{{ $keluar->item_name ?? '-' }}</span>
                        </td>
                        <td style="padding: 10px; text-align: right; font-weight: bold; color: #16a34a;">
                            - {{ number_format($keluar->qty, 2) }} {{ $keluar->uom }}
                        </td>
                        <td style="padding: 10px; text-align: center;">
                            <!-- Logika If-Else untuk membaca status secara dinamis -->
                            @if($keluar->status_approval == 'Pending')
                                <span style="background: #fef3c7; color: #b45309; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 11px;">Pending</span>
                            @elseif($keluar->status_approval == 'Approved')
                                <span style="background: #dcfce7; color: #16a34a; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 11px;">Approved</span>
                            @elseif($keluar->status_approval == 'Rejected')
                                <span style="background: #fee2e2; color: #dc2626; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 11px;">Rejected</span>
                            @else
                                <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 11px;">{{ $keluar->status_approval }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px; color: #64748b;">Belum ada data mutasi keluar dari Karantina.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= MODAL FORM MUTASI KELUAR ================= -->
<div id="modalMutasi" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center;">
    <div style="background-color: #fff; width: 500px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); overflow: hidden;">
        <div style="background-color: #0ea5e9; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px;"><i class="fas fa-exchange-alt"></i> Form Mutasi Keluar Karantina</h3>
            <span onclick="tutupModalMutasi()" style="cursor: pointer; font-size: 20px; font-weight: bold;">&times;</span>
        </div>
        
        <form action="{{ url('/purchasing/karantina/mutasi-keluar') }}" method="POST" style="padding: 20px;">
            @csrf
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold; color: #334155;">Tanggal</label>
                    <input type="date" name="tanggal" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold; color: #334155;">Tujuan Dept</label>
                    <select name="ke_dept" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                        <option value="">-- Pilih Tujuan --</option>
                        <option value="Warehouse">Warehouse (OK)</option>
                        <option value="Produksi">Produksi</option>
                        <option value="Supplier">Return to Supplier</option>
                        <option value="Scrap">Scrap / Buang</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold; color: #334155;">No. MM</label>
                    <input type="text" id="input_mm" name="mm" readonly style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px; background: #f1f5f9; color: #64748b; font-weight: bold;">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold; color: #334155;">Status Asal</label>
                    <input type="text" id="input_status" name="status_karantina" readonly style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px; background: #f1f5f9; color: #64748b; font-weight: bold;">
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-size: 12px; font-weight: bold; color: #334155;">Qty Mutasi Keluar</label>
                <input type="number" step="0.01" name="qty" required placeholder="Masukkan jumlah qty..." style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-size: 12px; font-weight: bold; color: #334155;">Catatan Tambahan (Opsional)</label>
                <input type="text" name="catatan" placeholder="Keterangan pengiriman..." style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
            </div>

            <div style="text-align: right; margin-top: 20px;">
                <button type="button" onclick="tutupModalMutasi()" style="background: #94a3b8; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; margin-right: 5px; font-weight: bold;">Batal</button>
                <button type="submit" style="background: #0ea5e9; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">Kirim Mutasi</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= JAVASCRIPT ================= -->
<script>
    // Membuka modal dan mengisi No MM dan Status secara otomatis
    function bukaModalMutasi(mm, status) {
        document.getElementById('input_mm').value = mm;
        document.getElementById('input_status').value = status;
        document.getElementById('modalMutasi').style.display = 'flex';
    }

    // Menutup modal
    function tutupModalMutasi() {
        document.getElementById('modalMutasi').style.display = 'none';
    }
</script>
@endsection