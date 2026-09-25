<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Performa Supplier</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0 0 5px 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 0; font-size: 13px; }
        
        .supplier-section { margin-bottom: 40px; page-break-inside: avoid; }
        .supplier-title { background: #f0f0f0; padding: 8px; font-weight: bold; font-size: 14px; border: 1px solid #000; margin-bottom: -1px; }
        
        table { width: 100%; border-collapse: collapse; text-align: center; margin-bottom: 15px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background-color: #f9f9f9; font-size: 11px; }
        
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #3b82f6; color: white; border: none; cursor: pointer; font-weight: bold;">Cetak / Save PDF</button>
    </div>

    <div class="header">
        <h2>Laporan Penilaian Kinerja Supplier</h2>
        <p>Periode Bulan: <b>{{ implode(', ', $bulan_selected) }}</b> | Tahun: <b>{{ $tahun }}</b></p>
    </div>

    @forelse($groupedData as $vendor_name => $performances)
        <div class="supplier-section">
            <div class="supplier-title">SUPPLIER : {{ $vendor_name }}</div>
            <table>
                <thead>
                    <tr>
                        <th rowspan="2">Bulan</th>
                        <th colspan="2">Aspek Kuantitatif (Skor Sistem)</th>
                        <th colspan="2">Aspek Kualitatif (Observasi QC)</th>
                    </tr>
                    <tr>
                        <th>Total Skor</th>
                        <th>Status</th>
                        <th>Total Skor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($performances as $p)
                    <tr>
                        <td style="font-weight: bold;">{{ $p->periode_bulan }}</td>
                        <td>{{ number_format($p->skor_kuantitatif, 2) }}</td>
                        <td style="font-weight: bold;">{{ $p->status_kuantitatif }}</td>
                        <td>{{ number_format($p->skor_kualitatif, 2) }}</td>
                        <td style="font-weight: bold;">{{ $p->status_kualitatif }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <div style="text-align: center; padding: 50px; font-style: italic;">
            Tidak ada data penilaian untuk periode yang dipilih.
        </div>
    @endforelse

    <div style="margin-top: 50px; text-align: right; font-size: 12px;">
        <p>Dibuat oleh,</p>
        <br><br><br>
        <p><b>Quality Department</b></p>
    </div>

</body>
</html>