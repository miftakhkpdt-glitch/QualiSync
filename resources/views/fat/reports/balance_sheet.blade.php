@extends('layouts.staff-layout')

@section('konten')
<div style="padding: 30px; background-color: #f8fafc; min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h2 style="font-size: 24px; font-weight: bold; color: #1e293b; margin: 0 0 5px 0;">
                <i class="fas fa-balance-scale" style="color: #6366f1; margin-right: 8px;"></i> Laporan Neraca (Balance Sheet)
            </h2>
            <p style="color: #64748b; font-size: 14px; margin: 0;">Posisi keuangan perusahaan mencakup Harta, Hutang, dan Modal.</p>
        </div>
        
        <!-- Indikator Balance -->
        @if(round($totalHarta) == round($totalPasiva))
            <div style="background: #dcfce7; color: #166534; padding: 10px 20px; border-radius: 8px; border: 1px solid #bbf7d0; font-weight: bold; font-size: 14px;">
                <i class="fas fa-check-circle"></i> BALANCE
            </div>
        @else
            <div style="background: #fee2e2; color: #991b1b; padding: 10px 20px; border-radius: 8px; border: 1px solid #fecaca; font-weight: bold; font-size: 14px;">
                <i class="fas fa-times-circle"></i> TIDAK BALANCE (Selisih: Rp {{ number_format(abs($totalHarta - $totalPasiva), 0, ',', '.') }})
            </div>
        @endif
    </div>

    <!-- FILTER SECTION -->
    <div style="background: white; border-radius: 10px; padding: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); margin-bottom: 25px; border: 1px solid #e2e8f0;">
        <form action="{{ route('fat.reports.balance_sheet') }}" method="GET" style="display: flex; gap: 20px; align-items: flex-end;">
            <div style="flex: 1; max-width: 300px;">
                <label style="font-size: 12px; font-weight: bold; color: #64748b; display: block; margin-bottom: 8px;">Posisi Per Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
            </div>
            <div>
                <button type="submit" style="background: #1e293b; color: white; border: none; padding: 11px 25px; border-radius: 6px; font-size: 14px; font-weight: bold; cursor: pointer;">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- FORMAT DUA KOLOM NERACA -->
    <div style="display: flex; gap: 25px; align-items: flex-start; flex-wrap: wrap;">
        
        <!-- KOLOM KIRI: AKTIVA (HARTA) -->
        <div style="flex: 1; min-width: 350px; background: white; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; border-top: 5px solid #3b82f6;">
            <div style="padding: 20px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
                <h3 style="margin: 0; font-size: 16px; color: #1e293b;"><i class="fas fa-wallet" style="color: #3b82f6;"></i> AKTIVA (Aset/Harta)</h3>
            </div>
            
            <table style="width: 100%; border-collapse: collapse;">
                <tbody>
                    @forelse($harta as $item)
                        <tr style="border-bottom: 1px dashed #f1f5f9;">
                            <td style="padding: 12px 20px; font-size: 13px; color: #475569;">{{ $item->kode }} - {{ $item->nama }}</td>
                            <td style="padding: 12px 20px; font-size: 14px; color: #0f172a; text-align: right; font-weight: bold;">Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" style="padding: 20px; text-align: center; color: #94a3b8; font-size: 13px;">Belum ada data Harta.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr style="background: #eff6ff;">
                        <td style="padding: 15px 20px; font-size: 14px; font-weight: bold; color: #1e293b;">TOTAL AKTIVA</td>
                        <td style="padding: 15px 20px; font-size: 18px; font-weight: 900; color: #2563eb; text-align: right;">Rp {{ number_format($totalHarta, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- KOLOM KANAN: PASIVA (KEWAJIBAN & MODAL) -->
        <div style="flex: 1; min-width: 350px; display: flex; flex-direction: column; gap: 25px;">
            
            <!-- Kewajiban -->
            <div style="background: white; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; border-top: 5px solid #ef4444;">
                <div style="padding: 20px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
                    <h3 style="margin: 0; font-size: 16px; color: #1e293b;"><i class="fas fa-hand-holding-usd" style="color: #ef4444;"></i> KEWAJIBAN (Hutang)</h3>
                </div>
                <table style="width: 100%; border-collapse: collapse;">
                    <tbody>
                        @forelse($kewajiban as $item)
                            <tr style="border-bottom: 1px dashed #f1f5f9;">
                                <td style="padding: 12px 20px; font-size: 13px; color: #475569;">{{ $item->kode }} - {{ $item->nama }}</td>
                                <td style="padding: 12px 20px; font-size: 14px; color: #0f172a; text-align: right; font-weight: bold;">Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" style="padding: 15px 20px; color: #94a3b8; font-size: 13px;">Tidak ada data Kewajiban.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr style="background: #f1f5f9;">
                            <td style="padding: 12px 20px; font-size: 13px; font-weight: bold; color: #475569;">Total Kewajiban</td>
                            <td style="padding: 12px 20px; font-size: 14px; font-weight: bold; color: #0f172a; text-align: right;">Rp {{ number_format($totalKewajiban, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Modal -->
            <div style="background: white; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; border-top: 5px solid #10b981;">
                <div style="padding: 20px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
                    <h3 style="margin: 0; font-size: 16px; color: #1e293b;"><i class="fas fa-coins" style="color: #10b981;"></i> MODAL (Ekuitas)</h3>
                </div>
                <table style="width: 100%; border-collapse: collapse;">
                    <tbody>
                        @forelse($modal as $item)
                            <tr style="border-bottom: 1px dashed #f1f5f9;">
                                <td style="padding: 12px 20px; font-size: 13px; color: #475569;">{{ $item->kode }} - {{ $item->nama }}</td>
                                <td style="padding: 12px 20px; font-size: 14px; color: #0f172a; text-align: right; font-weight: bold;">Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" style="padding: 15px 20px; color: #94a3b8; font-size: 13px;">Tidak ada data Modal.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr style="background: #f1f5f9;">
                            <td style="padding: 12px 20px; font-size: 13px; font-weight: bold; color: #475569;">Total Modal</td>
                            <td style="padding: 12px 20px; font-size: 14px; font-weight: bold; color: #0f172a; text-align: right;">Rp {{ number_format($totalModal, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Total Pasiva -->
            <div style="background: #fef2f2; border-radius: 10px; padding: 15px 20px; border: 1px solid #fecaca; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 14px; font-weight: bold; color: #991b1b;">TOTAL PASIVA (Kewajiban + Modal)</span>
                <span style="font-size: 18px; font-weight: 900; color: #dc2626;">Rp {{ number_format($totalPasiva, 0, ',', '.') }}</span>
            </div>

        </div>
    </div>
</div>
@endsection