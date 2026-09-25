@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 1400px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 25px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <h3 style="margin: 0; color: #1e293b; font-size: 20px;">
                <i class="fas fa-file-invoice" style="color: #3b82f6; margin-right: 8px;"></i> Daftar Purchase Request (PR) Masuk
            </h3>
            
            <!-- BADGE IDENTITAS DEPARTEMEN DINAMIS -->
            @if(isset($deptFilter) && $deptFilter != '')
                <span style="background: #e0f2fe; color: #0284c7; padding: 4px 12px; border-radius: 20px; font-size: 14px; font-weight: 700; border: 1px solid #bae6fd;">
                    @if($deptFilter == 'hrd_ga')
                        HRD & GA
                    @elseif($deptFilter == 'product_development')
                        PRODUCT DEVELOPMENT
                    @else
                        {{ strtoupper($deptFilter) }}
                    @endif
                </span>
            @else
                <span style="background: #f1f5f9; color: #64748b; padding: 4px 12px; border-radius: 20px; font-size: 14px; font-weight: 700; border: 1px solid #e2e8f0;">
                    SEMUA DEPARTEMEN
                </span>
            @endif
        </div>
    </div>
    <!-- ================= BLOK FILTER PENCARIAN ================= -->
    <div style="background: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 20px;">
        <form action="{{ url()->current() }}" method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin: 0;">
            
            <!-- SIMPAN PARAMETER DEPT DARI SIDEBAR SECARA TERSEMBUNYI -->
            @if(request('dept'))
                <input type="hidden" name="dept" value="{{ request('dept') }}">
            @endif

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
                
                <!-- Tombol Reset Muncul Jika Filter Pencarian/Status Aktif -->
                @if(request('search') || request('status'))
                    <a href="{{ url()->current() }}?dept={{ request('dept') }}" style="background: #ef4444; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 13px; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(239,68,68,0.2);">
                        <i class="fas fa-times"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
    <!-- ================= END BLOK FILTER ================= -->
    @if(session('success'))
        <div style="background: #dcfce7; color: #15803d; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 10px; font-weight: 500;">
            <i class="fas fa-check-circle" style="font-size: 18px;"></i> {{ session('success') }}
        </div>
    @endif

    <!-- TABEL DATA -->
    <div style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 6px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #1e293b; color: white;">
                    <th style="padding: 12px 10px;">Tanggal & No PR</th>
                    <th style="padding: 12px 10px; text-align: center;">Kategori</th>
                    <th style="padding: 12px 10px;">Material Yang Diminta</th>
                    <th style="padding: 12px 10px;">Qty Dibutuhkan</th>
                    <th style="padding: 12px 10px;">Pemohon & Ket</th>
                    <th style="padding: 12px 10px; text-align: center;">Status PR</th>
                    <th style="padding: 12px 10px; text-align: center;">Approval Manager</th>
                    
                    <!-- SEMBUNYIKAN HEADER TINDAKAN UNTUK MANAGER -->
                    @if(auth()->check() && auth()->user()->role != 'manager_plan')
                        <th style="padding: 12px 10px; text-align: center;">Tindakan</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($prs as $pr)
                <tr style="border-bottom: 1px solid #e2e8f0; background: {{ $pr->status == 'Pending' ? '#fffbeb' : '#ffffff' }};">
                    
                    <!-- 1. KOLOM TANGGAL & ESTIMASI TIBA -->
                    <td style="padding: 10px; color: #475569;">
                        <span style="font-weight: bold; color: #1e293b;">{{ date('d M Y', strtotime($pr->tanggal)) }}</span><br>
                        <span style="font-size: 11px; font-weight: bold; color: #0284c7;">{{ $pr->no_pr }}</span><br>
                        <div style="margin-top: 5px; font-size: 11px; color: #b91c1c; font-weight: 500;">
                            <i class="fas fa-calendar-check"></i> Est. Tiba: {{ $pr->estimasi_tiba ? date('d M Y', strtotime($pr->estimasi_tiba)) : '-' }}
                        </div>
                    </td>

                    <!-- 2. KOLOM KATEGORI -->
                    <td style="padding: 10px; text-align: center;">
                        <span style="background: #f1f5f9; color: #334155; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; border: 1px solid #cbd5e1; display: inline-block;">
                            {{ $pr->kategori ?? '-' }}
                        </span>
                    </td>

                    <!-- 3. KOLOM MATERIAL -->
                    <td style="padding: 10px;">
                        <b>{{ $pr->no_mm }}</b><br>
                        <small>{{ $pr->nama_material ?? 'Material Tidak Ditemukan' }}</small>
                    </td>

                    <!-- 4. KOLOM QTY -->
                    <td style="padding: 10px; font-weight: bold; color: #b91c1c;">
                        {{ number_format($pr->qty, 2) }} {{ $pr->satuan ?? 'Pcs' }}
                    </td>

                    <!-- 5. KOLOM PEMOHON & KETERANGAN -->
                    <td style="padding: 10px;">
                        <span style="font-weight: 600;">{{ $pr->nama_pemohon ?? 'Sistem PPIC' }}</span><br>
                        <div style="font-size: 11px; color: #64748b; margin-top: 4px; padding: 4px 6px; background: #f8fafc; border-left: 2px solid #cbd5e1; border-radius: 0 4px 4px 0;">
                            <b>Ket:</b> {{ $pr->catatan ?? '-' }}
                        </div>
                    </td>

                    <!-- 6. KOLOM STATUS PR -->
                    <td style="padding: 10px; text-align: center;">
                        @if($pr->status == 'Pending')
                            <span style="background: #fef08a; color: #854d0e; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;">
                                <i class="fas fa-hourglass-half"></i> Pending
                            </span>
                        @elseif($pr->status == 'Diproses')
                            <span style="background: #dbeafe; color: #1e40af; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;">
                                <i class="fas fa-cog fa-spin"></i> Sedang Diproses
                            </span>
                        @else
                            <span style="background: #dcfce7; color: #15803d; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;">
                                <i class="fas fa-check-double"></i> Selesai (PO Terbit)
                            </span>
                        @endif
                    </td>

                    <!-- 7. KOLOM APPROVAL PLAN MANAGER -->
                    <td style="padding: 10px; text-align: center;">
                        @if(($pr->status_approval ?? 'Pending') == 'Approved')
                            <span style="background: #dcfce7; color: #166534; padding: 5px 10px; border-radius: 4px; font-weight: bold; font-size: 11px;">
                                <i class="fas fa-check"></i> Approved
                            </span>
                        @else
                            <span style="background: #fef3c7; color: #b45309; padding: 5px 10px; border-radius: 4px; font-weight: bold; font-size: 11px;">
                                <i class="fas fa-clock"></i> Menunggu
                            </span>
                            
                            <!-- TOMBOL APPROVE (Hanya muncul jika yang login adalah Manager Plan) -->
                            @if(auth()->check() && auth()->user()->role == 'manager_plan') 
                                <form action="{{ url('/purchasing/approve-pr/'.$pr->id) }}" method="POST" style="margin-top: 5px;">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Anda yakin menyetujui Purchase Request ini?')" style="background: #10b981; color: white; border: none; padding: 4px 8px; border-radius: 3px; cursor: pointer; font-size: 11px; width: 100%; font-weight: bold; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                                        Approve PR
                                    </button>
                                </form>
                            @endif
                        @endif
                    </td>

                    <!-- 8. KOLOM TINDAKAN (DISEMBUNYIKAN DARI MANAGER) -->
                    @if(auth()->check() && auth()->user()->role != 'manager_plan')
                    <td style="padding: 10px; text-align: center;">
                        @if(($pr->status_approval ?? 'Pending') != 'Approved')
                            <!-- TOMBOL DIGEMBOK -->
                            <button disabled style="background: #cbd5e1; color: white; border: none; padding: 6px 12px; border-radius: 4px; font-size: 11px; font-weight: bold; cursor: not-allowed;" title="Menunggu Approval Manager">
                                <i class="fas fa-lock"></i> Terkunci
                            </button>
                        @else
                            <!-- TOMBOL TERBUKA -->
                            @if($pr->status == 'Pending')
                                <form action="{{ route('purchasing.pr.proses', $pr->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Kunci dan Proses PR ini untuk dibuatkan PO?')" style="background: #0ea5e9; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 11px; font-weight: bold; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                                        <i class="fas fa-play"></i> Proses ke PO
                                    </button>
                                </form>
                            @elseif($pr->status == 'Diproses')
                                <a href="{{ url('/purchasing/create-po/' . $pr->id) }}" style="background: #10b981; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 11px; font-weight: bold; display: inline-block; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                                    <i class="fas fa-file-invoice"></i> Buat PO
                                </a>
                            @else
                                <span style="color: #94a3b8; font-size: 12px; font-weight: bold;"><i class="fas fa-check-circle"></i> Selesai</span>
                            @endif
                        @endif
                    </td>
                    @endif

                </tr>
                @empty
                <tr>
                    <!-- Menyesuaikan jumlah colspan jika Manager yang melihat -->
                    <td colspan="{{ (auth()->check() && auth()->user()->role == 'manager_plan') ? '7' : '8' }}" style="text-align: center; padding: 30px; color: #64748b;">
                        <!-- PESAN KOSONG DINAMIS -->
                        Belum ada dokumen Purchase Request (PR) yang masuk 
                        @if(isset($deptFilter) && $deptFilter != '')
                            dari <b>{{ $deptFilter == 'hrd_ga' ? 'HRD & GA' : strtoupper($deptFilter) }}</b>.
                        @else
                            saat ini.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection