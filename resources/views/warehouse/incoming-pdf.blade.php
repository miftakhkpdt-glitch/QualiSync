<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kedatangan Barang (Incoming)</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; padding: 0; font-size: 18px; color: #333; }
        .header p { margin: 5px 0 0 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background-color: #e2e8f0; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

    <div class="header">
        <h2>LAPORAN REKAPAN KEDATANGAN BARANG (INCOMING)</h2>
        <p>
            Periode: 
            {{ request('start_date') ? date('d M Y', strtotime(request('start_date'))) : 'Awal' }} 
            s/d 
            {{ request('end_date') ? date('d M Y', strtotime(request('end_date'))) : 'Sekarang' }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="10%">Tgl Masuk</th>
                <th width="10%">No. STPB</th>
                <th width="10%">No. PO</th>
                <th width="10%">No. SJ Supplier</th>
                <th width="12%">No. MM</th>
                <th width="15%">Nama Material</th>
                <th width="8%">Kategori</th>
                <th width="12%">Vendor</th>
                <th width="10%">Qty Masuk</th>
            </tr>
        </thead>
        <tbody>
            @php $totalQty = 0; @endphp
            @forelse($riwayatKedatangan ?? [] as $key => $row)
                @php $totalQty += $row->quantity; @endphp
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td class="text-center">{{ date('d/m/Y', strtotime($row->updated_at)) }}</td>
                    <td class="text-center">{{ $row->stpb_number ?? '-' }}</td>
                    <td class="text-center">{{ $row->po_kpdt_number ?? '-' }}</td>
                    <td class="text-center">{{ $row->sj_supplier_number ?? '-' }}</td>
                    <td class="text-center">{{ $row->mm }}</td>
                    <td>{{ $row->item_name }}</td>
                    <td class="text-center">{{ $row->kategori ?? '-' }}</td>
                    <td>{{ $row->vendor_name ?? '-' }}</td>
                    <td class="text-right">{{ number_format($row->quantity, 2) }} {{ $row->uom }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data untuk periode / filter ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="9" class="text-right" style="font-weight: bold;">TOTAL QTY MASUK:</td>
                <td class="text-right" style="font-weight: bold;">{{ number_format($totalQty, 2) }}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>