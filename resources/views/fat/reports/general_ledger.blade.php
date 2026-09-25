@extends('layouts.staff-layout')

@section('konten')
<div style="padding: 30px; background-color: #f8fafc; min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 24px; font-weight: bold; color: #1e293b; margin: 0 0 5px 0;">
            <i class="fas fa-book-open" style="color: #3b82f6; margin-right: 8px;"></i> Laporan Buku Besar
        </h2>
        <p style="color: #64748b; font-size: 14px; margin: 0;">Lacak riwayat mutasi debit, kredit, dan saldo berjalan untuk setiap akun.</p>
    </div>

    <!-- FILTER SECTION -->
    <div style="background: white; border-radius: 10px; padding: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); margin-bottom: 25px; border: 1px solid #e2e8f0;">
        <form action="{{ route('fat.reports.general_ledger') }}" method="GET" style="display: flex; gap: 20px; align-items: flex-end;">
            <div style="flex: 2;">
                <label style="font-size: 12px; font-weight: bold; color: #64748b; display: block; margin-bottom: 8px;">Pilih Akun (COA)</label>
                <select name="coa_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; background: #f8fafc;">
                    <option value="">-- Pilih Akun yang Ingin Dicek --</option>
                    @foreach($coas as $coa)
                        <option value="{{ $coa->id }}" {{ $coa_id == $coa->id ? 'selected' : '' }}>
                            {{ $coa->kode_akun }} - {{ $coa->nama_akun }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="flex: 1;">
                <label style="font-size: 12px; font-weight: bold; color: #64748b; display: block; margin-bottom: 8px;">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $start_date }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
            </div>
            <div style="flex: 1;">
                <label style="font-size: 12px; font-weight: bold; color: #64748b; display: block; margin-bottom: 8px;">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $end_date }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
            </div>
            <div>
                <button type="submit" style="background: #3b82f6; color: white; border: none; padding: 11px 25px; border-radius: 6px; font-size: 14px; font-weight: bold; cursor: pointer; transition: 0.3s;">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>

    <!-- HASIL LAPORAN -->
    @if($selectedCoa)
    <div style="background: white; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #e2e8f0;">
        <div style="background: #1e293b; padding: 20px; color: white; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <span style="background: rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 4px; font-size: 13px; font-weight: bold; margin-right: 10px;">{{ $selectedCoa->kode_akun }}</span>
                <span style="font-size: 18px; font-weight: bold;">{{ $selectedCoa->nama_akun }}</span>
                <p style="margin: 5px 0 0 0; font-size: 13px; color: #cbd5e1;">Kategori: {{ $selectedCoa->kategori }} | Saldo Normal: {{ $selectedCoa->saldo_normal }}</p>
            </div>
            <div style="text-align: right;">
                <p style="margin: 0 0 5px 0; font-size: 12px; color: #cbd5e1;">Periode Laporan</p>
                <span style="font-size: 14px; font-weight: bold;">{{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}</span>
            </div>
        </div>

        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background-color: #f1f5f9; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 12px 20px; font-size: 13px; color: #475569; width: 12%;">Tanggal</th>
                    <th style="padding: 12px 20px; font-size: 13px; color: #475569; width: 18%;">No. Jurnal</th>
                    <th style="padding: 12px 20px; font-size: 13px; color: #475569;">Keterangan</th>
                    <th style="padding: 12px 20px; font-size: 13px; color: #475569; text-align: right; width: 13%;">Debit (Rp)</th>
                    <th style="padding: 12px 20px; font-size: 13px; color: #475569; text-align: right; width: 13%;">Kredit (Rp)</th>
                    <th style="padding: 12px 20px; font-size: 13px; color: #475569; text-align: right; width: 15%;">Saldo Berjalan (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Baris Saldo Awal -->
                <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                    <td colspan="5" style="padding: 12px 20px; font-size: 14px; font-weight: bold; text-align: right; color: #64748b;">SALDO AWAL SEBELUM {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} :</td>
                    <td style="padding: 12px 20px; font-size: 14px; font-weight: bold; text-align: right; color: #0f172a;">{{ number_format($saldoAwal, 0, ',', '.') }}</td>
                </tr>

                @php 
                    $saldoBerjalan = $saldoAwal; 
                    $totalDebit = 0;
                    $totalKredit = 0;
                @endphp

                @forelse($mutasi as $row)
                    @php 
                        $totalDebit += $row->debit;
                        $totalKredit += $row->kredit;

                        // Perhitungan Saldo Berjalan (Berdasarkan Saldo Normal Akun)
                        if ($selectedCoa->saldo_normal == 'Debit') {
                            $saldoBerjalan = $saldoBerjalan + $row->debit - $row->kredit;
                        } else {
                            $saldoBerjalan = $saldoBerjalan + $row->kredit - $row->debit;
                        }
                    @endphp
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px 20px; font-size: 13px; color: #475569;">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                        <td style="padding: 12px 20px; font-size: 13px; font-weight: bold; color: #3b82f6;">{{ $row->nomor_jurnal }}</td>
                        <td style="padding: 12px 20px; font-size: 13px; color: #0f172a;">{{ $row->keterangan }}</td>
                        <td style="padding: 12px 20px; font-size: 13px; text-align: right; color: {{ $row->debit > 0 ? '#10b981' : '#cbd5e1' }}; font-weight: {{ $row->debit > 0 ? 'bold' : 'normal' }};">
                            {{ number_format($row->debit, 0, ',', '.') }}
                        </td>
                        <td style="padding: 12px 20px; font-size: 13px; text-align: right; color: {{ $row->kredit > 0 ? '#ef4444' : '#cbd5e1' }}; font-weight: {{ $row->kredit > 0 ? 'bold' : 'normal' }};">
                            {{ number_format($row->kredit, 0, ',', '.') }}
                        </td>
                        <td style="padding: 12px 20px; font-size: 14px; font-weight: bold; text-align: right; color: #0f172a;">
                            {{ number_format($saldoBerjalan, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 30px; text-align: center; color: #94a3b8; font-style: italic;">
                            Tidak ada mutasi jurnal untuk akun ini pada periode yang dipilih.
                        </td>
                    </tr>
                @endforelse

                <!-- Baris Total Mutasi Akhir -->
                <tr style="background: #f1f5f9; border-top: 2px solid #e2e8f0;">
                    <td colspan="3" style="padding: 15px 20px; font-size: 14px; font-weight: bold; text-align: right; color: #475569;">TOTAL MUTASI PERIODE INI :</td>
                    <td style="padding: 15px 20px; font-size: 14px; font-weight: bold; text-align: right; color: #10b981;">{{ number_format($totalDebit, 0, ',', '.') }}</td>
                    <td style="padding: 15px 20px; font-size: 14px; font-weight: bold; text-align: right; color: #ef4444;">{{ number_format($totalKredit, 0, ',', '.') }}</td>
                    <td style="padding: 15px 20px; font-size: 16px; font-weight: 900; text-align: right; color: #1e293b; background: #e2e8f0;">{{ number_format($saldoBerjalan, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @elseif(!empty($coa_id) && !$selectedCoa)
        <div style="background: #fee2e2; color: #991b1b; padding: 20px; border-radius: 8px; border: 1px solid #fecaca; text-align: center; font-weight: bold;">Akun COA yang Anda cari tidak ditemukan.</div>
    @endif
</div>
@endsection