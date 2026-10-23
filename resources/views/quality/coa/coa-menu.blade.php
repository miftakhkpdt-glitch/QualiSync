@extends('layouts.staff-layout')

@section('konten')
<!-- Custom CSS Tambahan -->
<style>
    .coa-list-page { min-height: 100%; padding: 25px 15px; border-radius: 24px; background: linear-gradient(145deg, #f5f8f2, #fbf5ed 55%, #f4f1fa); }
    .coa-list-page .coa-card { overflow: hidden; border: 1px solid #efe8dc; border-radius: 22px; background: #fffdf9; box-shadow: 0 12px 32px rgba(107, 91, 72, .10), 0 3px 8px rgba(107, 91, 72, .05); }
    .coa-list-page .coa-header { display: flex; justify-content: space-between; align-items: center; gap: 18px; flex-wrap: wrap; padding: 25px 28px; background: linear-gradient(115deg, #fbe3da, #fff0df 62%, #f8e8d5); border-bottom: 1px solid #f1e5d9; }
    .coa-list-page .coa-title h4 { margin: 0; color: #51443d; font-size: 1.3rem; font-weight: 700; }
    .coa-list-page .coa-title h4 i { color: #df8e76 !important; }
    .coa-list-page .coa-title p { margin: 5px 0 0; color: #887b73; font-size: .88rem; }
    .coa-list-page .btn-create { display: inline-block; padding: 11px 18px; border: 0; border-radius: 14px; background: #e9947c; color: #fff !important; box-shadow: 0 5px 12px rgba(205, 123, 99, .22); text-decoration: none; font-size: .9rem; font-weight: 700; transition: .2s ease; }
    .coa-list-page .btn-create:hover { transform: translateY(-2px); background: #dc8069; box-shadow: 0 8px 16px rgba(205, 123, 99, .28); }
    .coa-list-page .filter-bar { padding: 17px 25px; border-bottom: 1px solid #eee8de; background: #fffaf3; }
    .coa-list-page .filter-bar .form-control { min-height: 38px; border: 1px solid #e8dfd2; border-radius: 12px !important; background: #fff; color: #5d5149; }
    .coa-list-page .filter-bar .form-control:focus { border-color: #dfaa91; box-shadow: 0 0 0 3px rgba(223, 170, 145, .18); }
    .coa-list-page .filter-bar .btn { min-height: 36px; padding: 6px 15px !important; border: 0; border-radius: 11px; }
    .coa-list-page .filter-bar .btn-primary { background: #8db9aa; }
    .coa-list-page .filter-bar .btn-primary:hover { background: #76a795; }
    .coa-list-page .filter-bar .btn-secondary { background: #e8e2d8; color: #625951; }
    .coa-list-page .coa-table { width: 100%; min-width: 780px; margin: 0; border-collapse: separate; border-spacing: 0; font-size: .9rem; }
    .coa-list-page .coa-table th { padding: 15px; border-bottom: 1px solid #eee7dc; background: #faf6ee; color: #877568; font-size: .73rem; font-weight: 700; letter-spacing: .55px; text-transform: uppercase; }
    .coa-list-page .coa-table td { padding: 15px; border-bottom: 1px solid #f0ebe3; background: #fffdf9; color: #625a53; vertical-align: middle; transition: background .18s ease; }
    .coa-list-page .coa-table tbody tr:nth-child(even) td { background: #f7faf6; }
    .coa-list-page .coa-table tbody tr:hover td { background: #fff1e9; }
    .coa-list-page .coa-table td strong { color: #51443d !important; }
    .coa-list-page .coa-table td small { color: #988b80 !important; }
    .coa-list-page .coa-table td > span[style*="background"] { display: inline-block; margin-top: 4px; border-radius: 8px !important; background: #e9f0eb !important; color: #60796c !important; }
    .coa-list-page .badge-modern { display: inline-block; padding: 6px 11px; border-radius: 999px; font-size: .72rem; font-weight: 700; letter-spacing: .25px; }
    .coa-list-page .bg-green { background: #d9eee3; color: #39775a; }
    .coa-list-page .bg-blue { background: #dcecf3; color: #47788b; }
    .coa-list-page .bg-red { background: #f6deda; color: #a85b50; }
    .coa-list-page .bg-orange { background: #f8e6bd; color: #94703a; }
    .coa-list-page .bg-gray { background: #e9e5ec; color: #716879; }
    .coa-list-page .coa-actions { display: flex; justify-content: center; align-items: center; flex-wrap: wrap; gap: 6px; }
    .coa-list-page .btn-print, .coa-list-page .btn-edit, .coa-list-page .btn-delete { display: inline-block; padding: 8px 10px; border: 1px solid transparent; border-radius: 11px; font-size: .78rem; font-weight: 700; text-decoration: none; transition: .18s ease; }
    .coa-list-page .btn-print { border-color: #cbded4; background: #e8f2ec; color: #527968 !important; }
    .coa-list-page .btn-print:hover { background: #d6e9dd; }
    .coa-list-page .btn-edit { border-color: #f0d8a8; background: #fff2d7; color: #94703a !important; }
    .coa-list-page .btn-edit:hover { background: #f8e5b9; }
    .coa-list-page .btn-delete { border-color: #f0d2cd; background: #fbe9e5; color: #ab6258 !important; cursor: pointer; }
    .coa-list-page .btn-delete:hover { background: #f4d6d0; }
    .coa-list-page .coa-empty { padding: 50px 20px; text-align: center; }
    .coa-list-page .coa-empty i { display: block; margin-bottom: 15px; color: #dfc8b7; font-size: 40px; }
    .coa-list-page .coa-empty h6 { margin: 0; color: #63574f; }
    .coa-list-page .coa-empty p { margin-top: 5px; color: #95877d; font-size: .85rem; }
    .coa-list-page .coa-footer-info { padding: 14px 25px; border-top: 1px solid #eee8de; background: #faf6ee; color: #82766c; font-size: .8rem; }
    .coa-list-page .coa-footer-info i { margin-right: 5px; color: #8db9aa; }
    .coa-list-page .coa-success { margin-bottom: 18px; padding: 13px 18px; border: 1px solid #cfe3d6; border-left: 5px solid #82b79a; border-radius: 13px; background: #eaf4ed; color: #426d54; }
    @media (max-width: 767px) {
        .coa-list-page { padding: 14px 8px; border-radius: 16px; }
        .coa-list-page .coa-header { padding: 20px; }
        .coa-list-page .filter-bar { padding: 15px; }
        .coa-list-page .coa-table th, .coa-list-page .coa-table td { padding: 12px 10px; }
    }
</style>

<div class="container-fluid coa-list-page">
    
    <!-- Alert Notifikasi Sukses -->
    @if(session('success'))
        <div class="coa-success">
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
                            <div class="coa-actions">
                                <a href="{{ url('/print-coa/' . $item->id) }}" target="_blank" class="btn-print" title="Cetak Dokumen">
                                    <i class="fas fa-print"></i> Cetak
                                </a>

                                @if(in_array(strtolower(auth()->user()->role ?? ''), ['admin', 'staff_quality'], true))
                                <a href="{{ route('coa.edit', $item->id) }}" class="btn-edit" title="Edit Dokumen">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @endif

                                @if(strtolower(auth()->user()->role ?? '') === 'admin')
                                <form action="{{ url('/coa/' . $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen {{ $item->no_coa }} ini?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" title="Hapus Dokumen">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="coa-empty">
                            <i class="fas fa-folder-open"></i>
                            <h6>Belum ada dokumen COA</h6>
                            <p>Data tidak ditemukan atau klik "Buat COA Baru" untuk membuat dokumen baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Footer Info -->
        <div class="coa-footer-info">
            <i class="fas fa-info-circle"></i> Dokumen COA yang dicetak bersifat statis (snapshot) sesuai hasil QIR pada waktu pembuatan. Dokumen &gt; 2 tahun akan dihapus otomatis oleh sistem.
        </div>
    </div>
</div>
@endsection