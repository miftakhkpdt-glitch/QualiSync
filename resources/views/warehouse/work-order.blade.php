@extends('layouts.staff-layout')

@section('title', 'Daftar Work Order - Gudang')

@section('konten')
<div style="padding: 20px;">
    <h2 style="font-size: 22px; color: #1e293b; margin-bottom: 5px;"><i class="fas fa-boxes" style="color: #0ea5e9;"></i> Daftar Work Order (Panduan Material)</h2>
    <p style="color: #64748b; font-size: 13px; margin-bottom: 20px;">Hitung estimasi kebutuhan material (Gross) sebelum melakukan mutasi stok.</p>

    <div style="background: #fff; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 12px;">Tanggal WO</th>
                    <th style="padding: 12px;">Material / Produk (MM)</th>
                    <th style="padding: 12px; text-align: center;">Target WO (Pcs)</th>
                    <th style="padding: 12px; text-align: center; background: #fef3c7;">Reject Rate (%)</th>
                    <th style="padding: 12px; text-align: center; background: #e0f2fe;">Estimasi Butuh (Gross)</th>
                    <th style="padding: 12px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workOrders as $wo)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 12px;">
                        <span style="font-weight: bold; color: #334155;">{{ \Carbon\Carbon::parse($wo->start_date)->format('d M Y') }}</span>
                    </td>
                    <td style="padding: 12px;">
                        <div style="font-weight: bold; color: #0284c7;">{{ $wo->nama_material ?? 'Material Tidak Ditemukan' }}</div>
                        <div style="color: #64748b; font-size: 11px;">MM: {{ $wo->no_mm }}</div>
                    </td>
                    
                    <!-- TARGET QTY (Disimpan ke atribut HTML agar bisa dibaca Javascript) -->
                    <td style="padding: 12px; text-align: center; font-weight: bold;" id="target-{{ $wo->id }}" data-qty="{{ $wo->qty_target }}">
                        {{ number_format($wo->qty_target) }}
                    </td>

                    <!-- INPUT REJECT RATE (Interaktif) -->
                    <td style="padding: 12px; text-align: center; background: #fffbeb;">
                        <input type="number" step="0.1" min="0" 
                               placeholder="Misal: 5"
                               style="width: 60px; padding: 6px; text-align: center; border: 1px solid #fcd34d; border-radius: 4px;"
                               onkeyup="hitungEstimasi({{ $wo->id }}, this.value)"
                               onchange="hitungEstimasi({{ $wo->id }}, this.value)">
                    </td>

                    <!-- HASIL KALKULASI OTOMATIS -->
                    <td style="padding: 12px; text-align: center; background: #f0f9ff;">
                        <span id="hasil-{{ $wo->id }}" style="font-size: 15px; font-weight: bold; color: #0369a1;">
                            {{ number_format($wo->qty_target) }} <!-- Default awal = Target -->
                        </span>
                    </td>

                    <td style="padding: 12px; text-align: center;">
                        <a href="{{ route('warehouse.stok.raw_material', ['fg_mm' => $wo->no_mm]) }}" 
   style="background: #10b981; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 11px; font-weight: bold; display: inline-block;">
    <i class="fas fa-truck-loading"></i> Mutasi
</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: #94a3b8;">Belum ada Work Order.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- SCRIPT KALKULATOR CERDAS -->
<script>
    function hitungEstimasi(woId, rate) {
        // Ambil Qty Target asli dari data tabel
        let targetQty = parseFloat(document.getElementById('target-' + woId).getAttribute('data-qty'));
        let rejectRate = parseFloat(rate);

        let hasilElement = document.getElementById('hasil-' + woId);

        // Jika input kosong atau bukan angka, kembalikan ke nilai awal
        if(isNaN(rejectRate) || rejectRate < 0) {
            hasilElement.innerText = targetQty.toLocaleString('id-ID');
            return;
        }

        // Rumus: Target + (Target * Reject Rate / 100)
        let totalEstimasi = targetQty + (targetQty * (rejectRate / 100));
        
        // Tampilkan hasil dengan format ribuan
        hasilElement.innerText = totalEstimasi.toLocaleString('id-ID');
    }
</script>
@endsection