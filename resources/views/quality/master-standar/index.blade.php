@extends('layouts.staff-layout')

@section('title', 'Master Standar Parameter')

@push('styles')
<style>
    .modern-container { max-width: 1200px; margin: 30px auto; font-family: 'Inter', sans-serif; }
    .modern-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; overflow: hidden; }
    .card-header { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 20px 25px; }
    .card-title { font-size: 18px; font-weight: 700; color: #1e293b; margin: 0; }
    .card-subtitle { font-size: 13px; color: #64748b; margin-top: 5px; }
    
    .modern-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; }
    .modern-table th { background-color: #f1f5f9; color: #334155; font-weight: 600; padding: 15px; border-bottom: 2px solid #e2e8f0; }
    .modern-table td { padding: 15px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
    .modern-table tr:hover { background-color: #f8fafc; }
    
    .btn-manage { background-color: #3b82f6; color: white; padding: 8px 15px; border-radius: 6px; font-weight: bold; text-decoration: none; font-size: 13px; transition: 0.2s; display: inline-block; }
    .btn-manage:hover { background-color: #2563eb; }
</style>
@endpush

@section('konten')
<div style="padding: 20px; max-width: 1000px; margin: 0 auto;">
    
    <!-- Header Halaman -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="color: #1e293b; margin: 0;"><i class="fas fa-sliders-h" style="color: #3b82f6;"></i> Master Standar Parameter</h2>
            <p style="color: #64748b; font-size: 14px; margin: 5px 0 0 0;">Kelola batas standar parameter pengujian ukur per item produk.</p>
        </div>
        <a href="{{ url('/dashboard-qa') }}" style="background: #64748b; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-weight: bold;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="modern-card">
        <div class="card-header">
            <h2 class="card-title"><i class="fas fa-cogs" style="color: #64748b; margin-right: 8px;"></i> Daftar Item Produk QA</h2>
            <div class="card-subtitle">Pilih Item/Produk di bawah ini untuk mengatur batas standar parameter (Weight, Air Tight, dll)</div>
        </div>
        
        <div style="padding: 20px;">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 150px;">No. MM</th>
                        <th>Nama Item Produk</th>
                        <th style="text-align: center; width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $item->no_mm }}</strong></td>
                        <td>{{ $item->nama_material ?? '-' }}</td>
                        <td style="text-align: center;">
                            <a href="{{ url('/master-standar/manage/' . $item->no_mm) }}" class="btn-manage">
                                <i class="fas fa-edit"></i> Kelola Parameter
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 30px; color: #64748b; font-style: italic;">
                            Belum ada data Master Item di database.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection