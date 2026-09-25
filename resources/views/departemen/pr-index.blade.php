@extends('layouts.staff-layout') <!-- Sesuaikan dengan nama layout Anda -->

@section('konten')
<div style="max-width: 1200px; margin: 0 auto;">
    
    <!-- HEADER -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
        <h3 style="margin: 0; color: #1e293b; font-weight: bold;">
            <i class="fas fa-edit" style="color: #3b82f6;"></i> Form Pengajuan Purchase Request
        </h3>
        <span style="background: #e0f2fe; color: #0284c7; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 14px;">
            DEPARTEMEN: {{ $departemen == 'hrd_ga' ? 'HRD & GA' : strtoupper($departemen) }}
        </span>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #15803d; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- KOTAK FORM INPUT -->
    <div style="background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <form action="{{ url('/departemen/purchase-request') }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                
                <div>
                    <label style="font-weight: bold; font-size: 13px; color: #475569;">Pilih Material / Barang *</label>
                    <select name="no_mm" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px; margin-top: 5px;">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($materials as $m)
                            <option value="{{ $m->no_mm }}">{{ $m->no_mm }} - {{ $m->nama_material }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-weight: bold; font-size: 13px; color: #475569;">Jumlah (Qty) *</label>
                    <input type="number" name="qty" required min="1" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px; margin-top: 5px;" placeholder="Contoh: 10">
                </div>

                <div>
                    <label style="font-weight: bold; font-size: 13px; color: #475569;">Target Tanggal Tiba *</label>
                    <input type="date" name="estimasi_tiba" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px; margin-top: 5px;">
                </div>

                <div>
                    <label style="font-weight: bold; font-size: 13px; color: #475569;">Kategori Barang *</label>
                    <select name="kategori" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px; margin-top: 5px;">
                        <option value="M">Material (M)</option>
                        <option value="S">Sparepart Mesin (S)</option>
                        <option value="A">ATK / Umum (A)</option>
                    </select>
                </div>

                <div style="grid-column: span 2;">
                    <label style="font-weight: bold; font-size: 13px; color: #475569;">Alasan Pembelian / Keterangan *</label>
                    <textarea name="catatan" required rows="2" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px; margin-top: 5px;" placeholder="Tuliskan untuk keperluan apa barang ini diminta..."></textarea>
                </div>

            </div>

            <div style="margin-top: 20px; text-align: right;">
                <button type="submit" style="background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">
                    <i class="fas fa-paper-plane"></i> Ajukan PR ke Purchasing
                </button>
            </div>
        </form>
    </div>

    <!-- TABEL RIWAYAT PENGAJUAN -->
    <h4 style="color: #1e293b; font-weight: bold; margin-bottom: 15px;"><i class="fas fa-history"></i> Riwayat Pengajuan PR Departemen Anda</h4>
    <div style="background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #f1f5f9; color: #334155;">
                    <th style="padding: 12px; border-bottom: 2px solid #cbd5e1;">No PR & Tanggal</th>
                    <th style="padding: 12px; border-bottom: 2px solid #cbd5e1;">Barang</th>
                    <th style="padding: 12px; border-bottom: 2px solid #cbd5e1;">Qty</th>
                    <th style="padding: 12px; border-bottom: 2px solid #cbd5e1;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayatPr as $pr)
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 12px;"><b>{{ $pr->no_pr }}</b><br><small>{{ date('d M Y', strtotime($pr->tanggal)) }}</small></td>
                    <td style="padding: 12px;">{{ $pr->nama_material }}</td>
                    <td style="padding: 12px;">{{ $pr->qty }}</td>
                    <td style="padding: 12px;">
                        @if($pr->status == 'Pending') <span style="background: #fef08a; padding: 4px 8px; border-radius: 4px; font-size: 11px;">Pending</span>
                        @else <span style="background: #dcfce7; padding: 4px 8px; border-radius: 4px; font-size: 11px;">Selesai</span> @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align: center; padding: 20px;">Belum ada riwayat pengajuan PR.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection