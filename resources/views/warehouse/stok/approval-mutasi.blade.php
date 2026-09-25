@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 1400px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px;">
        <h3 style="margin: 0; color: #334155;">
            <i class="fas fa-check-circle"></i> Approval Mutasi Masuk (Warehouse)
        </h3>
        <a href="{{ route('warehouse.mutasi.approval.print', request()->query()) }}" target="_blank" style="background: #3b82f6; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 13px; text-decoration: none;">
            <i class="fas fa-print"></i> Cetak Dokumen (Hasil Filter)
        </a>
    </div>

    <!-- WADAH NOTIFIKASI SUCCESS / ERROR -->
    @if(session('success'))
        <div style="background: #dcfce7; color: #16a34a; padding: 10px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #bbf7d0;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: #fee2e2; color: #dc2626; padding: 10px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #fecaca;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- FORM FILTER -->
    <form action="{{ route('warehouse.mutasi.approval.index') }}" method="GET" style="background: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 20px; display: flex; gap: 15px; align-items: flex-end;">
        <div>
            <label style="font-size: 12px; font-weight: bold; color: #475569;">Bulan</label>
            <select name="bulan" style="width: 120px; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                <option value="">-- Semua --</option>
                @for($i=1; $i<=12; $i++)
                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ request('bulan') == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 10)) }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label style="font-size: 12px; font-weight: bold; color: #475569;">Tahun</label>
            <select name="tahun" style="width: 100px; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                <option value="">-- Semua --</option>
                @for($y=date('Y'); $y>=2024; $y--)
                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label style="font-size: 12px; font-weight: bold; color: #475569;">Pencarian</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari MM / Nama / Batch..." style="width: 200px; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="submit" style="background: #0ea5e9; color: white; border: none; padding: 9px 15px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold;">Filter</button>
            <a href="{{ route('warehouse.mutasi.approval.index') }}" style="background: #94a3b8; color: white; padding: 9px 15px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold;">Reset</a>
        </div>
    </form>

    <!-- TABEL DATA -->
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #1e293b; color: white;">
                    <th style="padding: 12px 10px;">Tanggal & Jam</th>
                    <th style="padding: 12px 10px;">Dari Dept</th>
                    <th style="padding: 12px 10px;">No. MM, Item & Batch</th>
                    <th style="padding: 12px 10px;">Qty Mutasi</th>
                    <th style="padding: 12px 10px;">PIC Pengirim</th>
                    <th style="padding: 12px 10px; text-align: center;">Status Approval</th>
                    <th style="padding: 12px 10px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat_mutasi as $mutasi)
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <!-- KOLOM 1: Tanggal & Jam -->
                    <td style="padding: 10px; color: #475569;">
                        <span style="font-weight: bold; color: #1e293b;">{{ date('d M Y', strtotime($mutasi->created_at)) }}</span><br>
                        <span style="font-size: 11px; font-weight: bold;">
                            <i class="far fa-clock"></i> {{ date('H:i', strtotime($mutasi->created_at)) }}
                        </span>
                    </td>
                    <td style="padding: 10px;"><span style="background: #e0f2fe; color: #0369a1; padding: 3px 8px; border-radius: 4px;">{{ $mutasi->dari_dept }}</span></td>
                    <td style="padding: 10px;">
                        <b>{{ $mutasi->mm }}</b><br>
                        <small>{{ $mutasi->item_name }}</small><br>
                        <span style="color: #0ea5e9; font-size: 11px; font-weight: bold;">Batch: {{ $mutasi->batch ?? '-' }}</span>
                    </td>
                    <td style="padding: 10px; font-weight: bold; color: #16a34a;">+ {{ number_format($mutasi->qty, 2) }} {{ $mutasi->uom }}</td>
                    <td style="padding: 10px;">{{ $mutasi->pembuat->name ?? '-' }}</td>
                    <td style="padding: 10px; text-align: center;">
                        @if($mutasi->status_approval == 'Pending')
                            <span style="background: #fef08a; color: #854d0e; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;">Pending</span>
                        @elseif($mutasi->status_approval == 'Approved')
                            <span style="background: #dcfce7; color: #15803d; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;">Approved</span>
                        @else
                            <span style="background: #fee2e2; color: #b91c1c; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;">Rejected</span>
                        @endif
                    </td>
                    <td style="padding: 10px; text-align: center; white-space: nowrap;">
                        @if($mutasi->status_approval == 'Pending')
                            <!-- Pastikan nama route ini sudah sesuai dengan di web.php (warehouse.mutasi.approve) -->
                            <form action="{{ route('warehouse.mutasi.approve', $mutasi->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button type="submit" onclick="return confirm('Terima material retur/masuk ini ke Warehouse?')" style="background: #10b981; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;"><i class="fas fa-check"></i></button>
                            </form>
                            
                            <!-- Tombol Reject -->
                            <form action="{{ route('warehouse.mutasi.reject', $mutasi->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button type="submit" onclick="return confirm('Tolak mutasi ini? Stok akan dikembalikan utuh ke departemen asal.')" style="background: #ef4444; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;"><i class="fas fa-times"></i></button>
                            </form>
                        @elseif($mutasi->status_approval == 'Approved')
                            <i class="fas fa-check-double" style="color: #10b981;"></i> Diterima
                        @else
                            <i class="fas fa-ban" style="color: #ef4444;"></i> Ditolak
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align: center; padding: 30px;">Tidak ada pengiriman barang menuju Warehouse.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection