@extends('layouts.staff-layout')

@section('konten')
<!-- Custom CSS Tambahan -->
<style>
    .coa-card { background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eee; overflow: hidden; }
    .coa-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 25px; border-bottom: 1px solid #f5f5f5; flex-wrap: wrap; gap: 15px; }
    .coa-title h4 { margin: 0; font-weight: 600; color: #2c3e50; font-size: 1.25rem; }
    .coa-title p { margin: 4px 0 0; font-size: 0.85rem; color: #7f8c8d; }
    .btn-create { background: #4e73df; color: white !important; padding: 8px 18px; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 0.9rem; transition: 0.3s; box-shadow: 0 2px 6px rgba(78, 115, 223, 0.4); border: none; }
    .btn-create:hover { background: #2e59d9; transform: translateY(-1px); }
    
    /* Filter Bar Style */
    .filter-bar { background-color: #f8f9fc; padding: 15px 25px; border-bottom: 1px solid #eaecf4; }
    
    .coa-table { width: 100%; border-collapse: collapse; margin: 0; font-size: 0.9rem; }
    .coa-table th { background-color: #f8f9fc; padding: 15px; color: #4e73df; font-weight: 600; border-bottom: 2px solid #eaecf4; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
    .coa-table td { padding: 15px; border-bottom: 1px solid #eaecf4; vertical-align: middle; color: #5a5c69; }
    .coa-table tr:hover { background-color: #f8f9fc; }
    .badge-modern { padding: 5px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.3px; }
    .bg-green { background-color: #1cc88a; color: white; }
    .bg-blue { background-color: #36b9cc; color: white; }
    .bg-red { background-color: #e74a3b; color: white; }
    .bg-orange { background-color: #f6c23e; color: white; }
    .bg-gray { background-color: #858796; color: white; }
    
    /* Tombol Aksi */
    .btn-print { background-color: #f8f9fc; color: #4e73df !important; border: 1px solid #4e73df; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 0.8rem; font-weight: 500; transition: 0.2s; display: inline-block; }
    .btn-print:hover { background-color: #4e73df; color: white !important; }
    
    .btn-delete { background-color: #fff0f0; color: #e74a3b !important; border: 1px solid #e74a3b; padding: 5px 10px; border-radius: 4px; font-size: 0.8rem; font-weight: 500; transition: 0.2s; cursor: pointer; }
    .btn-delete:hover { background-color: #e74a3b; color: white !important; }
</style>

<div class="container-fluid" style="padding: 25px 15px;">
    
    <!-- Alert Notifikasi Sukses -->
    @if(session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px; border-left: 5px solid #28a745;">
            <i class="fas fa-check-circle" style="margin-right: 8px;"></i> {{ session('success') }}
        </div>
    @endif

    <div class="coa-card">
        <!-- Bagian Header & Tombol -->
        <div class="coa-header">
            <div class="coa-title">
                <h4><i class="fas fa-file-signature" style="margin-right: 8px; color: #4e73df;"></i> Riwayat Dokumen COA</h4>
                <p>Manajemen & Riwayat Certificate of Analysis (COA) yang diterbitkan.</p>
            </div>
            <div>
                <a href="{{ url('/coa/create') }}" class="btn-create">
                    <i class="fas fa-plus-circle" style="margin-right: 5px;"></i> Buat COA Baru
                </a>
            </div>
        </div>

        <!-- BAGIAN FILTER (TAHUN & NO MM) -->
        <div class="filter-bar">
            <form method="GET" action="{{ url()->current() }}" class="row g-2 align-items-center" style="margin: 0;">
                <div class="col-md-3 col-12 mb-2 mb-md-0">
                    <select name="tahun" class="form-control form-control-sm" style="border-radius: 5px;">
                        <option value="">-- Filter Semua Tahun --</option>
                        @php $tahunSekarang = date('Y'); @endphp
                        @for($y = $tahunSekarang; $y >= $tahunSekarang - 5; $y--)
                            <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4 col-12 mb-2 mb-md-0">
                    <input type="text" name="mm" class="form-control form-control-sm" placeholder="Cari No. MM / Material..." value="{{ request('mm') }}" style="border-radius: 5px;">
                </div>
                <div class="col-md-4 col-12">
                    <button type="submit" class="btn btn-sm btn-primary" style="padding: 5px 15px;"><i class="fas fa-search" style="margin-right: 4px;"></i> Cari</button>
                    @if(request('tahun') || request('mm'))
                        <a href="{{ url()->current() }}" class="btn btn-sm btn-secondary" style="padding: 5px 15px;"><i class="fas fa-undo" style="margin-right: 4px;"></i> Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Bagian Tabel Data -->
        <div style="overflow-x: auto;">
            <table class="coa-table">
                <thead>
                    <tr>
                        <th style="text-align: center; width: 5%;">No</th>
                        <th style="text-align: left;">No. Dokumen COA</th>
                        <th style="text-align: left;">Tipe Template</th>
                        <th style="text-align: left;">Material & Batch</th>
                        <th style="text-align: left;">Customer</th>
                        <th style="text-align: center;">Keputusan</th>
                        <th style="text-align: center; width: 16%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $key => $item)
                    <tr>
                        <td style="text-align: center; font-weight: 600;">{{ $key + 1 }}</td>
                        <td>
                            <span style="font-weight: 700; color: #2c3e50; font-size: 0.95rem;">{{ $item->no_coa }}</span><br>
                            <small style="color: #999;"><i class="far fa-clock"></i> {{ $item->created_at ? $item->created_at->format('d M Y, H:i') : '-' }}</small>
                        </td>
                        <td>
                            @if(($item->template_type ?? '') === 'YASULOR')
                                <span class="badge-modern bg-orange"><i class="fas fa-star" style="font-size: 10px;"></i> YASULOR</span>
                            @else
                                <span class="badge-modern bg-gray">GENERAL</span>
                            @endif
                        </td>
                        <td>
                            <strong style="color: #2c3e50;">{{ $item->no_mm }}</strong><br>
                            <span style="font-size: 0.8rem; background: #e3e6f0; padding: 2px 6px; border-radius: 4px; color: #444;">Batch: {{ $item->no_batch }}</span>
                        </td>
                        <td>
                            <span style="font-weight: 500;">{{ $item->customer_name ?? '-' }}</span><br>
                            <small style="color: #999;">PO: {{ $item->po_number ?? '-' }}</small>
                        </td>
                        <td style="text-align: center;">
                            @switch($item->status_decision)
                                @case('PASSED')
                                    <span class="badge-modern bg-green">PASSED</span>
                                    @break
                                @case('PASSED WITH NOTE')
                                    <span class="badge-modern bg-blue">PASSED W/ NOTE</span>
                                    @break
                                @case('BLOCKED')
                                    <span class="badge-modern bg-red">BLOCKED</span>
                                    @break
                                @default
                                    <span class="badge-modern bg-gray">{{ $item->status_decision }}</span>
                            @endswitch
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 6px;">
                                <a href="{{ url('/print-coa/' . $item->id) }}" target="_blank" class="btn-print" title="Cetak Dokumen">
                                    <i class="fas fa-print"></i> Cetak
                                </a>

                                <form action="{{ url('/coa/' . $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen {{ $item->no_coa }} ini?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" title="Hapus Dokumen">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 50px 20px;">
                            <i class="fas fa-folder-open" style="font-size: 40px; color: #ddd; margin-bottom: 15px; display: block;"></i>
                            <h6 style="color: #666; margin: 0;">Belum ada dokumen COA</h6>
                            <p style="color: #999; font-size: 0.85rem; margin-top: 5px;">Data tidak ditemukan atau klik "Buat COA Baru" untuk membuat dokumen baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Footer Info -->
        <div style="background-color: #f8f9fc; padding: 12px 25px; border-top: 1px solid #eaecf4; font-size: 0.8rem; color: #858796;">
            <i class="fas fa-info-circle" style="color: #4e73df; margin-right: 5px;"></i> Dokumen COA yang dicetak bersifat statis (snapshot) sesuai hasil QIR pada waktu pembuatan. Dokumen > 2 tahun akan dihapus otomatis oleh sistem.
        </div>
    </div>
</div>
@endsection