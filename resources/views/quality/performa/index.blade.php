@extends('layouts.staff-layout')

@section('title', 'Daftar Performa Supplier')

@section('konten')
<div style="padding: 20px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="color: #1e293b; margin: 0;"><i class="fas fa-chart-line" style="color: #0ea5e9;"></i> Data Performa Supplier ({{ $tahun }})</h2>
            <p style="color: #64748b; font-size: 14px;">Rekapitulasi penilaian kualitatif dan kuantitatif supplier.</p>
        </div>
        <a href="{{ route('quality.supplier_performance.create') }}" style="background: #10b981; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: bold;">
            <i class="fas fa-plus"></i> Input Penilaian Baru
        </a>
    </div>

    @if(session('error'))
        <div style="background: #fee2e2; color: #dc2626; padding: 10px; border-radius: 6px; margin-bottom: 15px;">
            {{ session('error') }}
        </div>
    @endif

    <!-- KOTAK FILTER CETAK -->
    <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 25px; border-left: 4px solid #f59e0b;">
        <h4 style="margin-top: 0;"><i class="fas fa-print"></i> Filter & Cetak Laporan</h4>
        <form action="{{ route('quality.supplier_performance.print') }}" method="POST" target="_blank">
            @csrf
            
            <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="font-weight: bold; font-size: 13px;">Tahun</label>
                    <input type="number" name="tahun" value="{{ $tahun }}" class="form-control" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div style="flex: 2;">
                    <label style="font-weight: bold; font-size: 13px;">Filter Supplier (Kosongkan untuk cetak semua)</label>
                    <select name="supplier_id" class="form-control" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        <option value="">-- Semua Supplier --</option>
                        @foreach($vendors as $v)
                            <option value="{{ $v->id }}">{{ $v->vendor_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <label style="font-weight: bold; font-size: 13px; display: block; margin-bottom: 5px;">Pilih Bulan (Bisa lebih dari satu untuk Semester/Kuartal):</label>
            <div style="display: flex; flex-wrap: wrap; gap: 15px; background: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0;">
                @php $listBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']; @endphp
                @foreach($listBulan as $bln)
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 5px;">
                        <input type="checkbox" name="bulan[]" value="{{ $bln }}" style="transform: scale(1.2);"> {{ $bln }}
                    </label>
                @endforeach
            </div>

            <button type="submit" style="margin-top: 15px; background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
                <i class="fas fa-print"></i> Cetak Laporan (PDF / Print)
            </button>
        </form>
    </div>

    <!-- TABEL DATA -->
    <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
            <thead>
                <tr style="background: #f1f5f9; border-bottom: 2px solid #cbd5e1;">
                    <th style="padding: 10px;">Periode</th>
                    <th style="padding: 10px;">Supplier</th>
                    <th style="padding: 10px;">Skor Kuantitatif</th>
                    <th style="padding: 10px;">Status Kuantitatif</th>
                    <th style="padding: 10px;">Skor Kualitatif</th>
                    <th style="padding: 10px;">Status Kualitatif</th>
                </tr>
            </thead>
            <tbody>
                @forelse($performances as $p)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 10px; font-weight: bold;">{{ $p->periode_bulan }} {{ $p->periode_tahun }}</td>
                    <td style="padding: 10px;">{{ $p->vendor_name }}</td>
                    <td style="padding: 10px;">{{ number_format($p->skor_kuantitatif, 2) }}</td>
                    <td style="padding: 10px;">
                        <span style="padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;
                            {{ $p->status_kuantitatif == 'EXCELLENCE' ? 'background: #dcfce7; color: #16a34a;' : ($p->status_kuantitatif == 'POOR' ? 'background: #fee2e2; color: #dc2626;' : 'background: #fef3c7; color: #d97706;') }}">
                            {{ $p->status_kuantitatif }}
                        </span>
                    </td>
                    <td style="padding: 10px;">{{ number_format($p->skor_kualitatif, 2) }}</td>
                    <td style="padding: 10px;">
                        <span style="padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;
                            {{ $p->status_kualitatif == 'EXCELLENCE' ? 'background: #dcfce7; color: #16a34a;' : ($p->status_kualitatif == 'POOR' ? 'background: #fee2e2; color: #dc2626;' : 'background: #fef3c7; color: #d97706;') }}">
                            {{ $p->status_kualitatif }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align: center; padding: 20px;">Belum ada data di tahun ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection