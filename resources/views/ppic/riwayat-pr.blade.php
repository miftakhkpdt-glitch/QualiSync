@extends('layouts.staff-layout')

@section('title', 'Riwayat Purchase Request (PR) - PPIC')

@section('konten')
<div style="max-width: 1400px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 25px;">
        <h3 style="margin: 0; color: #1e293b; font-size: 20px;">
            <i class="fas fa-history"></i> Riwayat Pengajuan Purchase Request (PR)
        </h3>
    </div>
<!-- ================= BLOK FILTER PENCARIAN ================= -->
    <div style="background: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 20px;">
        <form action="{{ url()->current() }}" method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin: 0;">
            
            <!-- 1. Input Teks Pencarian -->
            <div style="flex: 1; min-width: 250px;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No PR / No MM / Nama Material..." 
                       style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px; outline: none;">
            </div>

            <!-- 2. Dropdown Status PR -->
            <div>
                <select name="status" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px; outline: none; background: white; cursor: pointer;">
                    <option value="">Semua Status</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <!-- 3. Tombol Aksi -->
            <div style="display: flex; gap: 5px;">
                <button type="submit" style="background: #3b82f6; color: white; padding: 8px 15px; border-radius: 4px; border: none; font-weight: bold; cursor: pointer; font-size: 13px; box-shadow: 0 2px 4px rgba(59,130,246,0.2);">
                    <i class="fas fa-search"></i> Filter
                </button>
                
                <!-- Tombol Reset Muncul Jika Filter Aktif -->
                @if(request('search') || request('status'))
                    <a href="{{ url()->current() }}" style="background: #ef4444; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 13px; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(239,68,68,0.2);">
                        <i class="fas fa-times"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
    <!-- ================= END BLOK FILTER ================= -->
    <!-- TABEL DATA -->
    <div style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 6px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #0ea5e9; color: white;">
                    <th style="padding: 12px 10px;">Tanggal & No PR</th>
                    <th style="padding: 12px 10px; text-align: center;">Kategori</th>
                    <th style="padding: 12px 10px;">Material</th>
                    <th style="padding: 12px 10px;">Qty Request</th>
                    <th style="padding: 12px 10px; text-align: center;">Approval Manager</th>
                    <th style="padding: 12px 10px; text-align: center;">Status Purchasing</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayatPr as $pr)
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    
                    <!-- 1. TANGGAL & EST TIBA -->
                    <td style="padding: 10px; color: #475569;">
                        <span style="font-weight: bold; color: #1e293b;">{{ date('d M Y', strtotime($pr->tanggal)) }}</span><br>
                        <span style="font-size: 11px; font-weight: bold; color: #0284c7;">{{ $pr->no_pr }}</span><br>
                        <div style="margin-top: 5px; font-size: 11px; color: #b91c1c; font-weight: 500;">
                            Est. Tiba: {{ $pr->estimasi_tiba ? date('d M Y', strtotime($pr->estimasi_tiba)) : '-' }}
                        </div>
                    </td>

                    <!-- 2. KATEGORI -->
                    <td style="padding: 10px; text-align: center;">
                        <span style="background: #f1f5f9; color: #334155; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; border: 1px solid #cbd5e1; display: inline-block;">
                            {{ $pr->kategori ?? '-' }}
                        </span>
                    </td>

                    <!-- 3. MATERIAL -->
                    <td style="padding: 10px;">
                        <b>{{ $pr->no_mm }}</b><br>
                        <small>{{ $pr->nama_material ?? 'Material Tidak Ditemukan' }}</small>
                    </td>

                    <!-- 4. QTY -->
                    <td style="padding: 10px; font-weight: bold; color: #b91c1c;">
                        {{ number_format($pr->qty, 2) }} {{ $pr->satuan ?? 'Unit' }}
                    </td>

                    <!-- 5. STATUS APPROVAL MANAGER -->
                    <td style="padding: 10px; text-align: center;">
                        @if(($pr->status_approval ?? 'Pending') == 'Approved')
                            <span style="background: #dcfce7; color: #166534; padding: 5px 10px; border-radius: 4px; font-weight: bold; font-size: 11px;">
                                <i class="fas fa-check"></i> Approved
                            </span>
                        @else
                            <span style="background: #fef3c7; color: #b45309; padding: 5px 10px; border-radius: 4px; font-weight: bold; font-size: 11px;">
                                <i class="fas fa-clock"></i> Menunggu
                            </span>
                        @endif
                    </td>

                    <!-- 6. STATUS PURCHASING -->
                    <td style="padding: 10px; text-align: center;">
                        @if($pr->status == 'Pending')
                            <span style="background: #fef08a; color: #854d0e; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;">
                                <i class="fas fa-hourglass-half"></i> Pending
                            </span>
                        @elseif($pr->status == 'Diproses')
                            <span style="background: #dbeafe; color: #1e40af; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;">
                                <i class="fas fa-cog fa-spin"></i> Diproses
                            </span>
                        @else
                            <span style="background: #dcfce7; color: #15803d; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;">
                                <i class="fas fa-check-double"></i> PO Terbit
                            </span>
                        @endif
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: #64748b;">Belum ada riwayat pembuatan PR dari tim Anda.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection