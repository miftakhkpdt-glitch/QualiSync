<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan Internal - Mutasi Raw Material</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #000; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .ttd-box { width: 100%; margin-top: 30px; }
        .ttd-box td { border: none; text-align: center; padding-top: 50px; }
    </style>
</head>
<body onload="window.print()">
    
    <div class="header">
        <h2>PT KIMPAI DYNA TUBE</h2>
        <h3>SURAT JALAN INTERNAL - MUTASI RAW MATERIAL</h3>
        <p>
            Filter Tanggal: {{ $request->start_date ? date('d-M-Y', strtotime($request->start_date)) : 'Semua' }} 
            s/d {{ $request->end_date ? date('d-M-Y', strtotime($request->end_date)) : 'Semua' }} | 
            Pencarian: {{ $request->search ?? '-' }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Shift</th>
                <th>Tujuan Dept</th>
                <th>No. MM</th>
                <th>Nama Item</th>
                <th>Batch</th>
                <th>Qty</th>
                <th>PIC Pengirim</th>
                <th>Approved By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data_print as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ date('d-M-Y', strtotime($item->tanggal)) }}</td>
                <td>{{ $item->shift }}</td>
                <td><b>{{ $item->ke_dept }}</b></td>
                <td>{{ $item->mm }}</td>
                <td>{{ $item->item_name ?? '-' }}</td>
                <td>{{ $item->batch ?? '-' }}</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($item->qty, 2) }} {{ $item->uom }}</td>
                <td>{{ $item->nama_pembuat ?? '-' }}</td>
                <td>{{ $item->nama_penyetuju ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align: center;">Tidak ada data mutasi (Approved) yang sesuai dengan filter.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="ttd-box">
        <tr>
            <td>Disiapkan & Dikirim Oleh,<br><br><br><br><b>( Warehouse Dept )</b></td>
            <td>Diterima & Disetujui Oleh,<br><br><br><br><b>( Departemen Penerima )</b></td>
        </tr>
    </table>

</body>
</html>