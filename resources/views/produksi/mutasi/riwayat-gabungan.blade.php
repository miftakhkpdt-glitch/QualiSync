@extends('layouts.admin') {{-- Sesuaikan layout utama Anda --}}

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Mutasi Masuk & Keluar (Produksi Dept)</h6>
        </div>
        <div class="card-body">
            <!-- Form Filter -->
            <form method="GET" action="" class="form-inline mb-4">
                <div class="row w-100">
                    <div class="col-md-2 mb-2">
                        <select name="jenis" class="form-control w-100">
                            <option value="">-- Semua Jenis --</option>
                            <option value="masuk" {{ request('jenis') == 'masuk' ? 'selected' : '' }}>Mutasi Masuk</option>
                            <option value="keluar" {{ request('jenis') == 'keluar' ? 'selected' : '' }}>Mutasi Keluar</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <select name="bulan" class="form-control w-100">
                            <option value="">-- Semua Bulan --</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <input type="number" name="tahun" class="form-control w-100" placeholder="Tahun (Cth: 2026)" value="{{ request('tahun') }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <input type="text" name="search" class="form-control w-100" placeholder="Cari No. MM / Nama / Batch..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                        <a href="{{ url()->current() }}" class="btn btn-secondary"><i class="fas fa-sync"></i> Reset</a>
                    </div>
                </div>
            </form>

            <!-- Tabel Riwayat -->
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Status Mutasi</th>
                            <th>Dari Dept</th>
                            <th>Ke Dept</th>
                            <th>No. MM & Nama Item</th>
                            <th>Qty</th>
                            <th>Batch</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $row)
                        <tr>
                            <td>{{ $row->tanggal }}</td>
                            <td>
                                @if($row->ke_dept == 'Produksi')
                                    <span class="badge badge-success">Masuk (Terima)</span>
                                @else
                                    <span class="badge badge-warning">Keluar (Kirim)</span>
                                @endif
                            </td>
                            <td>{{ $row->dari_dept }}</td>
                            <td>{{ $row->ke_dept }}</td>
                            <td>
                                <strong>{{ $row->mm }}</strong><br>
                                <small>{{ $row->item_name }}</small>
                            </td>
                            <td>{{ number_format($row->qty, 2) }} {{ $row->uom ?? 'Pcs' }}</td>
                            <td>{{ $row->batch ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data riwayat mutasi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection