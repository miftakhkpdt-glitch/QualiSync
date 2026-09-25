@extends('layouts.staff-layout')

@section('title', 'Data Master Customer - PT KIMPAI DYNA TUBE')

@section('konten')
<div style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    
    <!-- Header Halaman -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 style="font-size: 24px; color: #334155; margin: 0;">
                <i class="fas fa-building" style="color: #0ea5e9;"></i> Data Master Customer
            </h2>
            <p style="color: #64748b; font-size: 14px; margin-top: 5px;">Kelola daftar pelanggan beserta pengaturan rumus MRP-nya.</p>
        </div>
        <a href="{{ route('sales.master-customer.create') }}" style="background: #0ea5e9; color: white; padding: 10px 15px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fas fa-plus"></i> Tambah Customer
        </a>
    </div>

    <!-- Alert Notifikasi Sukses -->
    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 4px; margin-bottom: 20px; font-weight: bold; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Form Pencarian Sederhana -->
    <div style="background: #fff; padding: 15px 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 20px;">
        <form action="{{ route('sales.master-customer.index') }}" method="GET" style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan kode atau nama customer..." style="flex: 1; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px; outline: none;">
            <button type="submit" style="background: #475569; color: white; border: none; padding: 8px 15px; border-radius: 4px; font-weight: bold; font-size: 13px; cursor: pointer;">
                <i class="fas fa-search"></i> Cari
            </button>
            @if(request('search'))
                <a href="{{ route('sales.master-customer.index') }}" style="background: #e2e8f0; color: #334155; padding: 8px 12px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold;">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Tabel Data Customer -->
    <div style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-top: 4px solid #f59e0b; overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #cbd5e1;">
                    <th style="padding: 12px; color: #475569;">Kode</th>
                    <th style="padding: 12px; color: #475569;">Nama Customer</th>
                    <th style="padding: 12px; color: #475569;">Tipe Kalkulasi MRP</th>
                    <th style="padding: 12px; color: #475569; text-align: center;">Parameter Coverage</th>
                    <th style="padding: 12px; color: #475569; text-align: center; width: 130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers ?? [] as $cust)
                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;">
                    <td style="padding: 12px; font-weight: bold; color: #64748b;">
                        {{ $cust->kode_customer ?? '-' }}
                    </td>
                    <td style="padding: 12px; font-weight: bold; color: #1e293b;">
                        {{ $cust->nama_customer }}
                    </td>
                    <td style="padding: 12px;">
                        @if($cust->tipe_kalkulasi_mrp == 'Coverage')
                            <span style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 11px;">
                                <i class="fas fa-calendar-alt"></i> Coverage
                            </span>
                        @else
                            <span style="background: #e0f2fe; color: #0284c7; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 11px;">
                                <i class="fas fa-balance-scale"></i> Min-Max
                            </span>
                        @endif
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        @if($cust->tipe_kalkulasi_mrp == 'Coverage')
                            <div style="font-size: 12px; color: #475569;">
                                Target: <b>{{ $cust->batas_coverage_bulan }} Bulan</b><br>
                                Yield: <b>{{ floatval($cust->yield_pweb) }}%</b>
                            </div>
                        @else
                            <span style="color: #cbd5e1;">-</span>
                        @endif
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        <div style="display: flex; justify-content: center; gap: 6px;">
                            <!-- Tombol Edit -->
                            <a href="{{ route('sales.master-customer.edit', $cust->id) }}" style="background: #e0f2fe; color: #0284c7; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 12px;" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <!-- Tombol Hapus -->
                            <form action="{{ route('sales.master-customer.destroy', $cust->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus customer ini?');" style="margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: #fee2e2; color: #dc2626; border: none; padding: 6px 10px; border-radius: 4px; font-size: 12px; cursor: pointer;" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px; color: #94a3b8; font-style: italic;">
                        Belum ada data customer.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection