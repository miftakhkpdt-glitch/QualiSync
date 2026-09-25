@extends('layouts.staff-layout')

@push('styles')
<style>
    .fg-container { background-color: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); width: 100%; max-width: 1400px; margin: 0 auto; border: 1px solid #e2e8f0; }
    .judul-form { font-size: 18px; font-weight: 700; margin-bottom: 20px; text-transform: uppercase; color: #1e293b; }
    
    .tabel-fg { width: 100%; border-collapse: collapse; font-size: 11.5px; text-align: center; color: #1e293b; }
    .tabel-fg th, .tabel-fg td { border: 1px solid #cbd5e1; padding: 8px 6px; vertical-align: middle; }
    
    /* Warna header cokelat sesuai contoh gambar */
    .tabel-fg thead th { font-weight: bold; background-color: #d6af2e; color: #ffffff; font-size: 11px; text-transform: uppercase; }
    
    .badge-status { padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; display: inline-block; }
    .badge-open { background-color: #fee2e2; color: #dc2626; }
    .badge-closed { background-color: #dcfce7; color: #16a34a; }

    .btn-tambah { background-color: #d6af2e; color: white; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 20px; transition: background 0.2s; }
    .btn-tambah:hover { background-color: #d6af2e; color: white; }
    
    .btn-aksi { padding: 4px 8px; border-radius: 4px; text-decoration: none; font-size: 10.5px; font-weight: bold; color: white; background-color: #0284c7; display: inline-flex; align-items: center; gap: 4px; }
    .btn-aksi:hover { background-color: #0369a1; }
</style>
@endpush

@section('konten')
<div class="fg-container">
    <div class="judul-form">
        <i class="fas fa-clipboard-list" style="color: #d6af2e; margin-right: 8px;"></i> Corrective and Preventive Action (CAPA Customer)
    </div>

    <!-- Tombol Tambah Data CAPA Customer -->
    <a href="{{ url('/capa-8d/customer/create') }}" class="btn-tambah">
        <i class="fas fa-plus-circle"></i> Tambah CAPA Customer
    </a>

    <table class="tabel-fg">
        <thead>
            <tr>
                <th>No</th>
                <th>Complaint Month</th>
                <th>Customer</th>
                <th>No. Capa Customer</th>
                <th>MM</th>
                <th>Item</th>
                <th>Defect</th>
                <th>Source</th>
                <th>Category Defect</th>
                <th>SNCR (NC/NON NC)</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($capaCustomers ?? [] as $index => $data)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $data->complaint_month ?? '-' }}</td>
                    <td>{{ $data->customer ?? '-' }}</td>
                    <td>{{ $data->no_capa_customer ?? '-' }}</td>
                    <td>{{ $data->mm ?? '-' }}</td>
                    <td>{{ $data->item ?? '-' }}</td>
                    <td>{{ $data->defect ?? '-' }}</td>
                    <td>{{ $data->source ?? '-' }}</td>
                    <td>{{ $data->category_defect ?? '-' }}</td>
                    <td>{{ $data->sncr ?? '-' }}</td>
                    <td>
                        @if(($data->status ?? 'Open') == 'Open')
                            <span class="badge-status badge-open">Open</span>
                        @else
                            <span class="badge-status badge-closed">Closed</span>
                        @endif
                    </td>
                    <td>
                        <a href="#" class="btn-aksi">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" style="padding: 30px; color: #64748b; font-style: italic;">
                        Belum ada data CAPA Customer yang tersedia.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection