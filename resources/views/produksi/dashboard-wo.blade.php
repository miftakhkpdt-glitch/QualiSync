@extends('layouts.staff-layout')
@section('title', 'Tugas Produksi (WO)')
@section('konten')
<div style="padding: 20px;">
    <h2 style="font-size: 22px; color: #1e293b; margin-bottom: 20px;"><i class="fas fa-inbox" style="color: #0ea5e9;"></i> Antrean Work Order (WO Baru)</h2>
    <!-- ... (kode session success letakkan di sini) ... -->

    <div style="background: #fff; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <!-- thead sama seperti sebelumnya -->
            <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                <tr><th style="padding: 15px;">Start Date</th><th style="padding: 15px;">Produk (MM)</th><th style="padding: 15px; text-align: center;">Target WO</th><th style="padding: 15px; text-align: center;">Status</th><th style="padding: 15px; text-align: center;">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($workOrders as $wo)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 15px;">{{ \Carbon\Carbon::parse($wo->start_date)->format('d M Y') }}</td>
                    <td style="padding: 15px;">
                        <div style="font-weight: bold; color: #0284c7;">{{ $wo->nama_material ?? 'Material Tidak Diketahui' }}</div>
                        <div style="color: #64748b; font-size: 11px;">MM: {{ $wo->no_mm }}</div>
                    </td>
                    <td style="padding: 15px; text-align: center; font-weight: bold; color: #b45309;">{{ number_format($wo->qty_target) }} Pcs</td>
                    <td style="padding: 15px; text-align: center;">
                        <span style="background: #fef9c3; color: #854d0e; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold;">Menunggu Eksekusi</span>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <!-- Tombol Mulai (Memindah ke On Progress) -->
                        <form action="{{ route('produksi.wo.mulai', $wo->id) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Mulai jalankan WO ini? WO akan dipindahkan ke menu Proses Produksi.')" style="background: #3b82f6; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: bold; cursor: pointer;">
                                <i class="fas fa-play"></i> Mulai Produksi
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8;">Tidak ada antrean WO baru.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection