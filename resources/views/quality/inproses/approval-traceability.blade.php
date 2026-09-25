@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 1400px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px;">
        <h3 style="margin: 0; color: #334155;"><i class="fas fa-check-double"></i> Approval Traceability Material</h3>
        <span style="background: #fef08a; color: #854d0e; padding: 5px 10px; border-radius: 4px; font-weight: bold; font-size: 12px;">
            Menunggu Pengecekan
        </span>
    </div>

    <div style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
        <p style="margin: 0; color: #1e3a8a; font-size: 13px;">
            <i class="fas fa-info-circle"></i> Halaman ini digunakan oleh Quality Dept untuk memverifikasi kesesuaian material (Traceability) yang digunakan pada suatu Work Order dari Produksi.
        </p>
    </div>

    <!-- TABEL DATA APPROVAL -->
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #1e293b; color: white;">
                    <th style="padding: 12px 10px;">Tanggal Proses</th>
                    <th style="padding: 12px 10px;">No. Work Order</th>
                    <th style="padding: 12px 10px;">Item / Produk</th>
                    <th style="padding: 12px 10px;">Operator / Shift</th>
                    <th style="padding: 12px 10px; text-align: center;">Aksi Approval</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data_traceability as $data)
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    
                    <!-- Kolom 1: Tanggal & Shift -->
                    <td style="padding: 10px;">
                        <div style="font-weight: bold; color: #1e293b;">
                            {{ \Carbon\Carbon::parse($data->tanggal)->format('d M Y') }}
                        </div>
                        <span style="background: #e2e8f0; color: #475569; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: bold;">
                            {{ $data->shift ?? 'Shift 1' }}
                        </span>
                    </td>

                    <!-- Kolom 2: Work Order -->
                    <td style="padding: 10px; font-weight: bold; color: #0369a1;">
                        {{ $data->workOrder->no_wo ?? 'WO Tidak Diketahui' }}
                    </td>

                    <!-- Kolom 3: Produk / Item -->
                    <td style="padding: 10px;">
                        <!-- Menampilkan No MM -->
                        <b style="color: #1e293b;">MM: {{ $data->workOrder->no_mm ?? '-' }}</b><br>
                        
                        <!-- Menampilkan Nama Item dari relasi Sales Order -->
                        <small style="color: #64748b;">
                            {{ $data->workOrder->salesOrder->item_name ?? $data->workOrder->salesOrder->nama_produk ?? 'Nama item ada di SO' }}
                        </small><br>
                        
                        <span style="color: #0ea5e9; font-size: 11px; font-weight: bold;">Batch: {{ $data->batch_num ?? '-' }}</span>
                    </td>

                    <!-- Kolom 4: Operator & Grup -->
                    <td style="padding: 10px;">
                        {{ $data->operator ?? '-' }}<br>
                        <span style="color: #64748b; font-size: 11px;">Grup: {{ $data->grup ?? '-' }}</span>
                    </td>

                    <!-- Kolom 5: Aksi (Tombol Approve & Reject) -->
                    <td style="padding: 10px; text-align: center;">
                        <div style="display: flex; justify-content: center; gap: 5px;">
                            <form action="{{ route('quality.approval_traceability.approve', $data->id) }}" method="POST">
                                @csrf
                                <button type="submit" onclick="return confirm('Setujui Traceability ini?')" style="background: #10b981; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-size: 11px; font-weight: bold;" title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            
                            <form action="{{ route('quality.approval_traceability.reject', $data->id) }}" method="POST">
                                @csrf
                                <button type="submit" onclick="return confirm('Tolak Traceability ini?')" style="background: #ef4444; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-size: 11px; font-weight: bold;" title="Reject">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #64748b;">
                            <i class="fas fa-clipboard-list" style="font-size: 30px; margin-bottom: 10px; color: #cbd5e1;"></i><br>
                            Belum ada data Traceability yang memerlukan approval saat ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection