@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 1400px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    
    <!-- HEADER -->
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 20px;">
        <h3 style="margin: 0; color: #1e293b; font-size: 18px;">
            <i class="fas fa-check-circle" style="color: #3b82f6;"></i> Approval Mutasi Material (Quality Dept)
        </h3>
        
        <!-- TOMBOL CETAK (Sesuai Route di web.php Anda) -->
        <a href="{{ route('quality.mutasi.print', request()->all()) }}" target="_blank" style="background: #3b82f6; color: white; padding: 9px 15px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold;">
            <i class="fas fa-print"></i> Cetak Dokumen (Hasil Filter)
        </a>
    </div>

    <!-- FORM FILTER -->
    <form action="{{ url()->current() }}" method="GET" style="background: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 20px; display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
        <div>
            <label style="font-size: 12px; font-weight: bold; color: #475569;">Bulan</label><br>
            <select name="bulan" style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px; width: 120px; margin-top: 5px;">
                <option value="">-- Semua --</option>
                @for($i=1; $i<=12; $i++)
                    <option value="{{ sprintf('%02d', $i) }}" {{ request('bulan') == sprintf('%02d', $i) ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                    </option>
                @endfor
            </select>
        </div>
        <div>
            <label style="font-size: 12px; font-weight: bold; color: #475569;">Tahun</label><br>
            <select name="tahun" style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px; width: 120px; margin-top: 5px;">
                <option value="">-- Semua --</option>
                @for($y=date('Y'); $y>=2023; $y--)
                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label style="font-size: 12px; font-weight: bold; color: #475569;">Pencarian</label><br>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari MM / Nama / Batch..." style="width: 250px; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px; margin-top: 5px;">
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="submit" style="background: #0ea5e9; color: white; border: none; padding: 9px 15px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold;">Filter</button>
            <a href="{{ url()->current() }}" style="background: #94a3b8; color: white; padding: 9px 15px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold; display: inline-block;">Reset</a>
        </div>
    </form>

    <!-- TABEL DATA APPROVAL -->
    <div style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 6px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #1e293b; color: white;">
                    <th style="padding: 12px 10px;">Tanggal</th>
                    <th style="padding: 12px 10px; text-align: center;">Dari Dept</th>
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
                    
                    <!-- KOLOM 1: TANGGAL & SHIFT -->
                    <td style="padding: 10px; color: #475569;">
                        {{ \Carbon\Carbon::parse($mutasi->created_at)->format('d M Y') }}<br>
                        <small>{{ $mutasi->shift ?? 'Shift 1' }}</small>
                    </td>

                    <!-- KOLOM 2: DARI DEPT -->
                    <td style="padding: 10px; text-align: center;">
                        <span style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold;">
                            {{ $mutasi->dari_dept ?? 'Quality' }}
                        </span>
                    </td>
                    
                    <!-- KOLOM 3: MM, ITEM, BATCH -->
                    <td style="padding: 10px;">
                        <b style="color: #1e293b;">{{ $mutasi->mm }}</b><br>
                        <small style="color: #64748b;">{{ $mutasi->item_name }}</small><br>
                        <span style="color: #0ea5e9; font-size: 11px; font-weight: bold;">Batch: {{ $mutasi->batch ?? '-' }}</span>
                    </td>
                    
                    <!-- KOLOM 4: QTY MUTASI -->
                    <td style="padding: 10px; font-weight: bold; color: #1e293b;">
                        {{ number_format($mutasi->qty, 2) }} <br>
                        <small style="color: #64748b; font-weight: normal;">{{ $mutasi->uom ?? 'Pcs' }}</small>
                    </td>

                    <!-- KOLOM 5: PIC PENGIRIM -->
                    <td style="padding: 10px; color: #475569;">
                        {{ $mutasi->pembuat->name ?? 'Super Admin Utama' }}
                    </td>
                    
                    <!-- KOLOM 6: STATUS APPROVAL -->
                    <td style="padding: 10px; text-align: center;">
                        @if($mutasi->status_approval == 'Pending')
                            <span style="color: #b45309; font-weight: bold; font-size: 12px;">Pending</span>
                        @elseif($mutasi->status_approval == 'Approved' || $mutasi->status_approval == 'Selesai')
                            <span style="color: #16a34a; font-weight: bold; font-size: 12px;">Approved</span>
                        @else
                            <span style="color: #dc2626; font-weight: bold; font-size: 12px;">{{ $mutasi->status_approval }}</span>
                        @endif
                    </td>

                    <!-- KOLOM 7: AKSI -->
                    <td style="padding: 10px; text-align: center;">
                        @if($mutasi->status_approval == 'Pending')
                            <div style="display: flex; justify-content: center; gap: 5px;">
                                <!-- Tombol Setujui -->
                                <form action="{{ route('quality.mutasi.approve', $mutasi->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Yakin ingin menyetujui mutasi ini?')" style="background: #10b981; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-size: 11px; font-weight: bold;" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <!-- Tombol Tolak -->
                                <form action="{{ route('quality.mutasi.reject', $mutasi->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Yakin ingin menolak mutasi ini?')" style="background: #ef4444; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-size: 11px; font-weight: bold;" title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        @else
                            <!-- Icon Selesai jika sudah diapprove -->
                            <span style="color: #10b981; font-size: 12px; display: inline-block;">
                                <i class="fas fa-check"></i><br>Selesai
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align: center; padding: 30px; color: #64748b;">Belum ada data mutasi yang perlu di-approve.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection