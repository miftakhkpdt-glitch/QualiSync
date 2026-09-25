@extends('layouts.staff-layout')

@section('title', 'Verifikasi Incoming Material - PT KIMPAI DYNA TUBE')

@push('styles')
<style>
    .card-table { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .tabel-custom { width: 100%; border-collapse: collapse; }
    .tabel-custom th { background-color: #0ea5e9; color: white; padding: 12px 8px; text-align: left; font-size: 11px; border-bottom: 2px solid #0284c7; white-space: nowrap; }
    .tabel-custom td { padding: 10px 8px; font-size: 12px; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .tabel-custom tr:hover td { background-color: #f8fafc; }
    
    .badge { padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: bold; white-space: nowrap; }
    .badge-pass { background-color: #dcfce7; color: #15803d; }
    .badge-hold { background-color: #fef3c7; color: #d97706; } 
    .badge-ng { background-color: #fee2e2; color: #b91c1c; }
    .badge-pending { background-color: #e2e8f0; color: #475569; } 
    
    .btn-aksi { padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 11px; font-weight: bold; border: none; color: white; transition: 0.2s; }
    .btn-aksi:hover { opacity: 0.9; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    
    /* CSS Khusus Modal Input Qty */
    .input-qty { width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box; font-weight: bold; color: #1e293b; text-align: center; }
</style>
@endpush

@section('konten')
    <div style="margin-bottom: 20px;">
        <h1 style="font-size: 22px; color: #334155; margin-bottom: 5px;">
            <i class="fas fa-clipboard-check"></i> Verifikasi Incoming Material (Quality QC)
        </h1>
        <p style="color: #64748b; font-size: 14px; margin: 0;">Daftar material masuk dari supplier yang menunggu verifikasi dan approval QC.</p>
    </div>

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

    <div class="card-table">
        <div style="overflow-x: auto;">
            <table class="tabel-custom">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>No. PO</th>
                        <th>MM</th>
                        <th>Item Name</th>
                        <th style="text-align: right;">Quantity</th>
                        <th>Uom</th>
                        <th>Supplier</th>
                        <th>Status QC</th>
                        <th>Lokasi Stok</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($incoming ?? [] as $item)
                        <tr>
                            <td>{{ $item->date ?? '-' }}</td>
                            
                            <!-- [DIPERBAIKI] Panggil variabel yang benar: po_kpdt_number -->
                            <td>{{ $item->po_kpdt_number ?? '-' }}</td>
                            
                            <td style="font-weight: bold;">{{ $item->mm ?? '-' }}</td>
                            <td>{{ $item->item_name ?? '-' }}</td>
                            <td style="text-align: right; font-weight: bold; color: #0f172a;">{{ isset($item->quantity) ? number_format($item->quantity, 2) : '0' }}</td>
                            <td>{{ $item->uom ?? '-' }}</td>
                            <td>{{ $item->vendor_name ?? '-' }}</td>
                            <td>
                                <span class="badge {{ ($item->status_qc ?? '') == 'Pass' ? 'badge-pass' : (($item->status_qc ?? '') == 'NG' ? 'badge-ng' : (($item->status_qc ?? '') == 'Hold' ? 'badge-hold' : 'badge-pending')) }}">
                                    {{ $item->status_qc ?? 'Pending' }}
                                </span>
                            </td>
                            <td>
                                <span style="padding: 3px 8px; border-radius: 4px; font-size: 11px; background: {{ ($item->lokasi_stok ?? '') == 'Warehouse' ? '#e0f2fe; color: #0369a1;' : '#ffedd5; color: #c2410c;' }}">
                                    {{ $item->lokasi_stok ?? 'Karantina' }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <!-- Tombol Proses QC (Memicu Modal Row Splitting) -->
                                <button type="button" class="btn-aksi" style="background: #0ea5e9;" onclick="bukaModalJudgement({{ $item->id }}, {{ $item->quantity }}, '{{ $item->item_name }}', '{{ $item->uom }}')">
                                    <i class="fas fa-microscope"></i> Proses QC
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 25px; color: #64748b;">Belum ada data incoming material yang perlu diverifikasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <hr style="border: 0; border-top: 2px dashed #cbd5e1; margin: 40px 0;">

    <!-- TABEL RIWAYAT APPROVAL QC -->
    <div style="margin-bottom: 15px;">
        <h3 style="font-size: 18px; color: #334155; margin-bottom: 5px;">
            <i class="fas fa-history"></i> Riwayat Approval QC (Pass / Hold / NG)
        </h3>
        <p style="color: #64748b; font-size: 13px; margin: 0;">Daftar material yang sudah selesai diverifikasi oleh Quality.</p>
    </div>

    <div class="card-table">
        <div style="overflow-x: auto;">
            <table class="tabel-custom">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>No. PO</th> 
                        <th>MM</th>
                        <th>Item Name</th>
                        <th style="text-align: right;">Quantity</th>
                        <th>Uom</th>
                        <th>Supplier</th>
                        <th>Status QC</th>
                        <th>Lokasi Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayatApproval ?? [] as $history)
                        <tr>
                            <td>{{ $history->date ?? '-' }}</td>
                            
                            <!-- [DIPERBAIKI] Panggil variabel yang benar: po_kpdt_number -->
                            <td style="font-weight: bold; color: #2563eb;">{{ $history->po_kpdt_number ?? '-' }}</td>
                            
                            <td style="font-weight: bold;">{{ $history->mm ?? '-' }}</td>
                            <td>{{ $history->item_name ?? '-' }}</td>
                            <td style="text-align: right; font-weight: bold;">{{ isset($history->quantity) ? number_format($history->quantity, 2) : '0' }}</td>
                            <td>{{ $history->uom ?? '-' }}</td>
                            <td>{{ $history->vendor_name ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $history->status_qc == 'Pass' ? 'badge-pass' : ($history->status_qc == 'Hold' ? 'badge-hold' : 'badge-ng') }}">
                                    {{ $history->status_qc }}
                                </span>
                                @if($history->status_qc != 'Pass' && !empty($history->keterangan_qc))
                                    <div style="font-size: 10px; color: #64748b; margin-top: 4px; max-width: 150px; white-space: normal;">
                                        <b>Alasan:</b> {{ $history->keterangan_qc }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span style="padding: 3px 8px; border-radius: 4px; font-size: 11px; background: {{ $history->lokasi_stok == 'Warehouse' ? '#e0f2fe; color: #0369a1;' : '#ffedd5; color: #c2410c;' }}">
                                    {{ $history->lokasi_stok }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 25px; color: #64748b;">Belum ada riwayat approval QC.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= MODAL PROSES JUDGEMENT (ROW SPLITTING) ================= -->
    <div id="modalJudgement" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); justify-content: center; align-items: center;">
        <div style="background-color: #fff; width: 500px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); overflow: hidden;">
            <div style="background-color: #1e293b; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 16px;"><i class="fas fa-balance-scale"></i> Proses Judgement QC</h3>
                <span onclick="tutupModal()" style="cursor: pointer; font-size: 20px; font-weight: bold; padding: 0 5px;" title="Tutup">&times;</span>
            </div>
            
            <form id="formJudgement" method="POST" style="padding: 20px;" onsubmit="return validasiSplitting()">
                @csrf
                
                <div style="background: #f1f5f9; padding: 10px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #e2e8f0;">
                    <strong style="display: block; color: #334155; font-size: 14px;" id="modalItemName">Nama Material</strong>
                    <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                        <span style="font-size: 12px; color: #64748b;">Total Qty Awal:</span>
                        <span style="font-size: 14px; font-weight: bold; color: #0284c7;" id="modalTotalQtyText">0</span>
                    </div>
                </div>

                <!-- Input Qty Tersembunyi untuk Validasi JS -->
                <input type="hidden" id="hiddenTotalQty" value="0">

                <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                    <div style="flex: 1;">
                        <label style="font-size: 12px; font-weight: bold; color: #15803d; display: block; margin-bottom: 5px;">Qty Pass (OK)</label>
                        <input type="number" step="any" name="qty_pass" id="inpPass" class="input-qty" style="border-color: #bbf7d0; background: #f0fdf4;" min="0">
                    </div>
                    <div style="flex: 1;">
                        <label style="font-size: 12px; font-weight: bold; color: #d97706; display: block; margin-bottom: 5px;">Qty Hold</label>
                        <input type="number" step="any" name="qty_hold" id="inpHold" class="input-qty" style="border-color: #fde68a; background: #fffbeb;" min="0">
                    </div>
                    <div style="flex: 1;">
                        <label style="font-size: 12px; font-weight: bold; color: #b91c1c; display: block; margin-bottom: 5px;">Qty NG (Reject)</label>
                        <input type="number" step="any" name="qty_ng" id="inpNg" class="input-qty" style="border-color: #fecaca; background: #fef2f2;" min="0">
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="font-size: 12px; font-weight: bold; color: #334155; display: block; margin-bottom: 5px;">Keterangan / Alasan (Opsional, disarankan jika ada Hold/NG):</label>
                    <textarea name="keterangan_qc" rows="3" placeholder="Contoh: 100 Pcs NG karena kemasan rusak..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; resize: none; font-family: inherit; font-size: 13px; box-sizing: border-box;"></textarea>
                </div>

                <div style="text-align: right; margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 15px;">
                    <button type="button" onclick="tutupModal()" style="background: #94a3b8; color: white; border: none; padding: 9px 15px; border-radius: 4px; cursor: pointer; margin-right: 5px; font-weight: bold;">Batal</button>
                    <button type="submit" style="background: #0ea5e9; color: white; border: none; padding: 9px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;"><i class="fas fa-save"></i> Simpan & Pecah Stok</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function bukaModalJudgement(id, totalQty, itemName, uom) {
            // Arahkan URL Submit
            document.getElementById('formJudgement').action = `/quality/incoming/update-status/${id}`;
            
            // Set teks informasi di atas
            document.getElementById('modalItemName').innerText = itemName;
            document.getElementById('modalTotalQtyText').innerText = totalQty + ' ' + uom;
            document.getElementById('hiddenTotalQty').value = totalQty;
            
            // Atur default value (Anggap semua Pass secara default untuk kemudahan)
            document.getElementById('inpPass').value = totalQty;
            document.getElementById('inpHold').value = 0;
            document.getElementById('inpNg').value = 0;
            
            // Tampilkan Modal
            document.getElementById('modalJudgement').style.display = 'flex';
        }

        function tutupModal() {
            document.getElementById('modalJudgement').style.display = 'none';
        }

        function validasiSplitting() {
            // Ambil nilai dari inputan form
            let totalAwal = parseFloat(document.getElementById('hiddenTotalQty').value) || 0;
            let pass = parseFloat(document.getElementById('inpPass').value) || 0;
            let hold = parseFloat(document.getElementById('inpHold').value) || 0;
            let ng = parseFloat(document.getElementById('inpNg').value) || 0;
            
            let totalInput = pass + hold + ng;

            // Mencegah input angka minus
            if (pass < 0 || hold < 0 || ng < 0) {
                alert('Peringatan: Kuantitas tidak boleh bernilai negatif (minus)!');
                return false;
            }

            // Validasi krusial: Total pecahan harus sama dengan total awal
            // Menggunakan toFixed untuk menghindari bug perhitungan desimal javascript
            if (totalInput.toFixed(2) !== totalAwal.toFixed(2)) {
                alert(`Peringatan: Total pembagian Qty (${totalInput}) harus sama persis dengan Total Qty Awal (${totalAwal})!`);
                return false;
            }

            // Memastikan jika ada nilai di kolom Hold atau NG, meminta konfirmasi tambahan
            if (hold > 0 || ng > 0) {
                return confirm(`Anda memisahkan barang ke Karantina (Hold: ${hold}, NG: ${ng}). Lanjutkan proses ini?`);
            }
            
            return true; // Lolos verifikasi, form otomatis ter-submit
        }
    </script>
@endsection