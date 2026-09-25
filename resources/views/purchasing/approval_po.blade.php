@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 1200px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">

    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 20px;">
        <div>
            <h3 style="margin: 0; color: #1e293b; font-size: 20px;">
                <i class="fas fa-stamp" style="color: #10b981;"></i> Antrean Approval Purchase Order (PO)
            </h3>
            <span style="font-size: 13px; color: #64748b;">Daftar PO yang menunggu tanda tangan digital dari Anda.</span>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #15803d; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 10px; font-weight: 500;">
            <i class="fas fa-check-circle" style="font-size: 18px;"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: #fee2e2; color: #b91c1c; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #fecaca; display: flex; align-items: center; gap: 10px; font-weight: 500;">
            <i class="fas fa-times-circle" style="font-size: 18px;"></i> {{ session('error') }}
        </div>
    @endif

    <div style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 6px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #1e293b; color: white;">
                    <th style="padding: 12px 10px;">Tanggal & No. PO</th>
                    <th style="padding: 12px 10px;">Vendor / Supplier</th>
                    <th style="padding: 12px 10px;">Detail Pesanan</th>
                    <th style="padding: 12px 10px; text-align: right;">Total Nilai (Rp)</th>
                    <th style="padding: 12px 10px; text-align: center;">Status Approval</th>
                    <th style="padding: 12px 10px; text-align: center;">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pos as $po)
                <tr style="border-bottom: 1px solid #e2e8f0; background: #ffffff;">
                    <td style="padding: 10px; color: #475569;">
                        <span style="font-weight: bold; color: #1e293b;">{{ date('d M Y', strtotime($po->tanggal_po)) }}</span><br>
                        <span style="font-size: 11px; font-weight: bold; color: #0284c7;">PO-{{ date('Ymd', strtotime($po->tanggal_po)) }}-{{ $po->id }}</span>
                    </td>
                    <td style="padding: 10px;">
                        <strong>{{ $po->vendor_name }}</strong>
                    </td>
                    <td style="padding: 10px;">
                        <b>{{ $po->no_mm ?? '-' }}</b><br>
                        <small>{{ $po->qty_pesanan ?? 0 }} {{ $po->satuan ?? 'Pcs' }}</small>
                    </td>
                    <td style="padding: 10px; font-weight: bold; text-align: right; font-size: 14px; color: #b91c1c;">
                        Rp {{ number_format($po->total_nilai, 0, ',', '.') }}
                    </td>
                    
                    <!-- Indikator Approval Bertingkat -->
                    <td style="padding: 10px; font-size: 11px;">
                        <div style="display: flex; flex-direction: column; gap: 4px; background: #f8fafc; padding: 8px; border-radius: 4px; border: 1px solid #e2e8f0;">
                            
                            <!-- Status Manager -->
                            <div style="display: flex; justify-content: space-between;">
                                <span>Manager:</span>
                                @if($po->approval_manager_plan == 'Approved')
                                    <span style="color: #166534; font-weight: bold;"><i class="fas fa-check"></i> ACC</span>
                                @else
                                    <span style="color: #b45309; font-weight: bold;"><i class="fas fa-clock"></i> Wait</span>
                                @endif
                            </div>
                            
                            <!-- Status Direktur -->
                            <div style="display: flex; justify-content: space-between;">
                                <span>Direktur:</span>
                                @if($po->approval_direktur == 'Approved')
                                    <span style="color: #166534; font-weight: bold;"><i class="fas fa-check"></i> ACC</span>
                                @else
                                    <span style="color: #b45309; font-weight: bold;"><i class="fas fa-clock"></i> Wait</span>
                                @endif
                            </div>
                            
                            <!-- Status Presdir (Khusus >50 Juta) -->
                            <div style="display: flex; justify-content: space-between; border-top: 1px dashed #cbd5e1; padding-top: 4px; margin-top: 2px;">
                                <span>Presdir:</span>
                                @if($po->approval_presdir == 'Approved')
                                    <span style="color: #166534; font-weight: bold;"><i class="fas fa-check"></i> ACC</span>
                                @elseif($po->approval_presdir == 'Tidak Perlu')
                                    <span style="color: #64748b; font-weight: bold;"><i class="fas fa-minus"></i> Skip</span>
                                @else
                                    <span style="color: #b45309; font-weight: bold;"><i class="fas fa-clock"></i> Wait</span>
                                @endif
                            </div>

                        </div>
                    </td>

                    <!-- Tombol Eksekusi -->
                    <td style="padding: 10px; text-align: center;">
                        <form action="{{ route('purchasing.approval_po.approve', $po->id) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Anda yakin ingin memberikan Approval pada PO ini?')" style="background: #10b981; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: bold; width: 100%; box-shadow: 0 2px 4px rgba(16,185,129,0.3);">
                                <i class="fas fa-signature"></i> Approve PO
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #64748b;">
                        <i class="fas fa-inbox" style="font-size: 30px; margin-bottom: 10px; color: #cbd5e1;"></i><br>
                        Hore! Belum ada dokumen PO yang masuk ke antrean persetujuan Anda.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection