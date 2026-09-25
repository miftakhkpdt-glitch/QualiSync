@extends('layouts.staff-layout') <!-- Sesuaikan dengan layout Anda -->

@section('konten')
<div style="max-width: 1400px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px;">

    <!-- ========================================== -->
    <!-- ALERT MESSAGES (PENANGKAP PESAN SUKSES & ERROR) -->
    <!-- ========================================== -->
    @if(session('success'))
        <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- PENANGKAP ERROR DARI URL -->
    @if(request()->has('error_msg'))
        <div style="background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle"></i> {{ request('error_msg') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle"></i> {!! implode('<br>', $errors->all()) !!}
        </div>
    @endif
    <!-- ========================================== -->


    <!-- ========================================== -->
    <!-- TABEL 1: MENUNGGU APPROVAL -->
    <!-- ========================================== -->
    <div style="background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-top: 4px solid #f59e0b;">
        <h3 style="margin-top: 0; margin-bottom: 20px; color: #b45309;">
            <i class="fas fa-hourglass-half"></i> Menunggu Approval
        </h3>
        
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #fef3c7; color: #92400e;">
                    <th style="padding: 12px 10px;">Tanggal SJ</th>
                    <th style="padding: 12px 10px;">No. PO & MM</th>
                    <th style="padding: 12px 10px;">Batch</th>
                    <th style="padding: 12px 10px; text-align: center;">Qty Kirim</th>
                    <th style="padding: 12px 10px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pending as $item)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 10px;">{{ \Carbon\Carbon::parse($item->tanggal_pengiriman)->format('d M Y') }}</td>
                        
                        <!-- NAMA PRODUK -->
                        <td style="padding: 10px;">
                            <b>{{ $item->no_po }}</b><br>
                            <small style="color: #64748b;">
                                {{ $item->no_mm }} - <span style="color: #0f172a; font-weight: bold;">{{ $item->nama_produk ?? 'Nama tidak ditemukan' }}</span>
                            </small>
                        </td>
                        
                        <td style="padding: 10px;">{{ $item->batch_number }}</td>
                        <td style="padding: 10px; text-align: center; font-weight: bold; color: #0f172a;">{{ number_format($item->qty_kirim, 0, ',', '.') }}</td>
                        
                        <!-- TOMBOL AKSI APPROVE & HAPUS BERDAMPINGAN -->
                        <td style="padding: 10px; text-align: center; display: flex; gap: 8px; justify-content: center;">
                            
                            <!-- Tombol Approve -->
                            <form action="{{ route('warehouse.outgoing.approve', $item->id) }}" method="POST" onsubmit="return confirm('Approve SJ ini? OSPO akan otomatis terpotong.');">
                                @csrf
                                <button type="submit" style="background: #10b981; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </form>

                            <!-- Tombol Hapus -->
                            <form action="{{ route('warehouse.outgoing.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus draft Surat Jalan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px; color: #64748b;">Tidak ada Surat Jalan yang menunggu approval saat ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>


    <!-- ========================================== -->
    <!-- TABEL 2: RIWAYAT APPROVAL -->
    <!-- ========================================== -->
    <div style="background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-top: 4px solid #10b981;">
        <h3 style="margin-top: 0; margin-bottom: 20px; color: #047857;">
            <i class="fas fa-history"></i> Riwayat Approval (Selesai)
        </h3>
        
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #d1fae5; color: #065f46;">
                    <th style="padding: 12px 10px;">Tanggal SJ</th>
                    <th style="padding: 12px 10px;">No. PO & MM</th>
                    <th style="padding: 12px 10px;">Batch</th>
                    <th style="padding: 12px 10px; text-align: center;">Qty Kirim</th>
                    <th style="padding: 12px 10px; text-align: center;">Di-Approve Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($approved as $item)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 10px;">{{ \Carbon\Carbon::parse($item->tanggal_pengiriman)->format('d M Y') }}</td>
                        
                        <!-- NAMA PRODUK -->
                        <td style="padding: 10px;">
                            <b>{{ $item->no_po }}</b><br>
                            <small style="color: #64748b;">
                                {{ $item->no_mm }} - <span style="color: #0f172a; font-weight: bold;">{{ $item->nama_produk ?? 'Nama tidak ditemukan' }}</span>
                            </small>
                        </td>
                        
                        <td style="padding: 10px;">{{ $item->batch_number }}</td>
                        <td style="padding: 10px; text-align: center; font-weight: bold; color: #0f172a;">{{ number_format($item->qty_kirim, 0, ',', '.') }}</td>
                        
                        <td style="padding: 10px; text-align: center;">
                            <span style="background: #e2e8f0; color: #334155; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;">
                                {{ $item->approved_by ?? 'Admin' }}
                            </span>
                            <br>
                            <small style="color: #94a3b8; font-size: 10px;">{{ \Carbon\Carbon::parse($item->updated_at)->format('d M Y, H:i') }}</small>
                            <br>
                            
                            <!-- TOMBOL PRINT SJ -->
                            <a href="{{ route('warehouse.outgoing.print', $item->id) }}" target="_blank" style="display: inline-block; margin-top: 5px; background: #3b82f6; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 11px; font-weight: bold;">
                                <i class="fas fa-print"></i> Print SJ
                            </a>
                            
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px; color: #64748b;">Belum ada riwayat approval.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection