@extends('layouts.staff-layout') <!-- Sesuaikan dengan layout staff yang Anda gunakan -->

@section('konten')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Manajemen Master Item</h1>

    @if(session('success'))
        <div style="background: #d1fae5; color: #065f46; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- 1. KOTAK FORM UPLOAD EXCEL -->
    <div class="card shadow mb-4" style="background: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 25px;">
        <h6 class="m-0 font-weight-bold text-primary" style="margin-bottom: 15px;">Import Data Master Item dari Excel</h6>
        
        <form action="{{ route('quality.master-item.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 8px; max-width: 600px;">
                <label for="file" style="font-weight: 600; font-size: 14px; margin: 0; color: #334155;">Pilih File Excel (.xlsx, .xls, .csv):</label>
                
                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <!-- Input File -->
                    <input type="file" name="file" id="file" required style="padding: 6px; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 4px; flex: 1; min-width: 250px; background: #f8fafc;">
                    
                    <!-- Tombol Upload -->
                    <button type="submit" style="background: #10b981; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 600; white-space: nowrap;">
                        <i class="fas fa-upload"></i> Upload & Import
                    </button>
                </div>
                
                <!-- Teks Keterangan -->
                <small style="color: #64748b; font-size: 12px;">* Pastikan kolom Excel sesuai dengan parameter standar kualitas pabrik.</small>
            </div>
        </form>
    </div>

    <!-- 2. TABEL DATA MASTER ITEM LENGKAP -->
    <div class="card shadow mb-4" style="background: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0;">
        
        <!-- [BARU] KOTAK PENCARIAN (FILTER) -->
        <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Master Item</h6>
            
            <form action="{{ url('/quality/master-items') }}" method="GET" style="display: flex; gap: 8px; margin: 0;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. MM atau Nama Item..." style="padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 4px; width: 250px; font-size: 13px; outline: none;">
                
                <button type="submit" style="background: #3b82f6; color: white; border: none; padding: 6px 15px; border-radius: 4px; cursor: pointer; font-size: 13px;">
                    <i class="fas fa-search"></i> Cari
                </button>

                @if(request('search'))
                    <a href="{{ url('/quality/master-items') }}" style="background: #ef4444; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 13px; display: flex; align-items: center;" title="Reset Pencarian">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>
        <!-- [SELESAI] KOTAK PENCARIAN -->

        <div class="table-responsive" style="overflow-x: auto;">
            <table class="table table-bordered" width="100%" cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                    <tr style="background: #f8fafc; text-align: left;">
                        <th style="padding: 10px; border: 1px solid #e2e8f0;">No. MM</th>
                        <th style="padding: 10px; border: 1px solid #e2e8f0;">Nama Item</th>
                        <th style="padding: 10px; border: 1px solid #e2e8f0;">Weight (Min-Max)</th>
                        <th style="padding: 10px; border: 1px solid #e2e8f0;">Height (Min-Max)</th>
                        <th style="padding: 10px; border: 1px solid #e2e8f0;">LOF (Min-Max)</th>
                        <th style="padding: 10px; border: 1px solid #e2e8f0;">Sep Force</th>
                        <th style="padding: 10px; border: 1px solid #e2e8f0;">Air Tight</th>
                        <th style="padding: 10px; border: 1px solid #e2e8f0;">Tape Test</th>
                        <th style="padding: 10px; border: 1px solid #e2e8f0;">Unzip</th>
                        <th style="padding: 10px; border: 1px solid #e2e8f0;">Pinch</th>
                        <th style="padding: 10px; border: 1px solid #e2e8f0; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @isset($masterItems)
                        @forelse($masterItems as $item)
                        <tr>
                            <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: bold;">{{ $item->no_mm }}</td>
                            <td style="padding: 10px; border: 1px solid #e2e8f0;">{{ $item->item_name }}</td>
                            <td style="padding: 10px; border: 1px solid #e2e8f0;">{{ $item->min_weight }} - {{ $item->max_weight }}</td>
                            <td style="padding: 10px; border: 1px solid #e2e8f0;">{{ $item->min_height }} - {{ $item->max_height }}</td>
                            <td style="padding: 10px; border: 1px solid #e2e8f0;">{{ $item->min_lof ?? '-' }} - {{ $item->max_lof ?? '-' }}</td>
                            <td style="padding: 10px; border: 1px solid #e2e8f0;">{{ $item->sep_force ?? '-' }}</td>
                            <td style="padding: 10px; border: 1px solid #e2e8f0;">{{ $item->std_air_tight ?? '-' }}</td>
                            <td style="padding: 10px; border: 1px solid #e2e8f0;">{{ $item->std_tape_test ?? '-' }}</td>
                            <td style="padding: 10px; border: 1px solid #e2e8f0;">{{ $item->std_unzip ?? '-' }}</td>
                            <td style="padding: 10px; border: 1px solid #e2e8f0;">{{ $item->std_pinch ?? '-' }}</td>
                            <td style="padding: 10px; border: 1px solid #e2e8f0; text-align: center;">
                                <!-- [DIPERBAIKI] URL diarahkan ke /quality/master-items/ -->
                                <form action="{{ url('/quality/master-items/' . $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus item ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: #ef4444; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" style="text-align: center; padding: 30px; color: #64748b;">
                                @if(request('search'))
                                    Data untuk pencarian "<b>{{ request('search') }}</b>" tidak ditemukan.
                                @else
                                    Belum ada data Master Item. Silakan upload file Excel Anda di atas.
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    @endisset
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection