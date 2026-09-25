<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $sj->id }}</title>
    <style>
        /* Pengaturan Kertas & Font bergaya Dot-Matrix / Pabrik */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
        }
        .print-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }
        .title-sj {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 5px 0;
            margin-bottom: 5px;
        }
        /* Garis putus-putus khas tabel di gambar */
        .dashed-table {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .dashed-table th {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 8px 0;
            text-align: left;
            font-weight: normal;
        }
        .dashed-table td {
            padding: 8px 0;
            vertical-align: top;
        }
        .dashed-table .bottom-line td {
            border-bottom: 1px dashed #000;
            padding-bottom: 20px;
        }
        .footer-info td {
            vertical-align: top;
            padding: 2px 0;
        }
        .signature-table {
            margin-top: 30px;
            text-align: center;
        }
        .signature-table td {
            width: 25%;
            padding-top: 60px;
        }
        .notes-bottom {
            font-size: 10px;
            margin-top: 20px;
        }
        
        /* Hilangkan elemen yang tidak perlu saat diprint */
        @media print {
            @page { size: A4 portrait; margin: 1cm; }
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()"> <!-- Otomatis muncul dialog print -->

    <div class="print-container">
        
        <!-- Tombol Kembali (Tidak ikut terprint) -->
        <div class="no-print" style="margin-bottom: 20px;">
            <a href="{{ route('warehouse.outgoing.approvalPage') }}" style="text-decoration: none; background: #64748b; color: white; padding: 8px 15px; border-radius: 4px;">&larr; Kembali</a>
            <button onclick="window.print()" style="background: #0ea5e9; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; margin-left: 10px;">Cetak Ulang</button>
        </div>

        <table class="header-table">
            <tr>
                <td style="width: 50%;">
                    <h1 style="font-size: 28px; font-weight: normal; margin: 0; color: #444; letter-spacing: 1px;">KIM PAI DYNA</h1>
                </td>
                <td style="width: 50%; font-size: 11px; text-align: right;">
                    No. Dokumen : FRM/18-WH/<br>
                    No. Revisi &nbsp;&nbsp;&nbsp;: 00 (20.09.21)
                </td>
            </tr>
        </table>

        <table class="header-table" style="margin-top: 15px;">
            <tr>
                <td style="width: 50%; padding-right: 20px;">
                    <b>Pabrik :</b><br>
                    Kawasan Industri Jababeka 1<br>
                    Jl. Jababeka IX Blok E 9-17<br>
                    Bekasi 17520<br>
                    Telp : +6231 89840346<br>
                    Fax &nbsp;&nbsp;: +6231 89840347
                </td>
                <td style="width: 50%;">
                    <div class="title-sj">SURAT JALAN</div>
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 40%;">Date</td>
                            <td style="width: 5%;">:</td>
                            <td>{{ \Carbon\Carbon::parse($sj->tanggal_pengiriman)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td>No.</td>
                            <td>:</td>
                            <td>SJ/{{ date('Y/m') }}/{{ str_pad($sj->id, 4, '0', STR_PAD_LEFT) }}</td>
                        </tr>
                        <tr>
                            <td>Customer PO</td>
                            <td>:</td>
                            <td>{{ $sj->no_po }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div style="margin-top: 20px;">
            <b>To :</b><br>
            {{ $sj->nama_customer ?? '-' }}<br>
            {!! nl2br(e($sj->alamat ?? '-')) !!}
        </div>

        <table class="dashed-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 35%;">Kode Barang<br>Nama Barang</th>
                    <th style="width: 15%;">Cust. Code</th> <!-- Ini Header-nya -->
                    <th style="width: 15%;">Batch</th>
                    <th style="width: 10%; text-align: right;">Qty</th>
                    <th style="width: 10%; text-align: center;">Unit</th>
                    <th style="width: 10%; text-align: center;">Qty Unit</th>
                </tr>
            </thead>
            <tbody>
                <tr class="bottom-line">
                    <td>1</td>
                    <td>
                        <b>{{ $sj->no_mm }}</b><br>
                        {{ $sj->nama_produk ?? '-' }}
                    </td>
                    
                    <!-- ========================================== -->
                    <!-- INI YANG KITA UBAH AGAR MENAMPILKAN DATA -->
                    <!-- ========================================== -->
                    <td>{{ $sj->kode_item_customer ?? '-' }}</td>
                    
                    <td>{{ $sj->batch_number }}</td>
                    <td style="text-align: right; font-weight: bold;">
                        {{ number_format($sj->qty_kirim, 0, ',', '.') }}
                    </td>
                    <td style="text-align: center;">Pcs</td>
                    <td style="text-align: center;">-</td>
                </tr>
            </tbody>
        </table>

        <table class="footer-info">
            <tr>
                <td style="width: 15%;">No. Shipment</td>
                <td style="width: 35%;">: {{ $sj->no_shipment ?? '-' }}</td>
                <td rowspan="4" style="width: 50%;">
                    Keterangan :<br>
                    {{ $sj->keterangan ?? '-' }}
                </td>
            </tr>
            <tr>
                <td>Fwd Agent</td>
                <td>: {{ $sj->fwd_agent ?? '-' }}</td>
            </tr>
            <tr>
                <td>No. Polisi</td>
                <td>: {{ $sj->no_polisi ?? '-' }}</td>
            </tr>
            <tr>
                <td>Nama Supir</td>
                <td>: {{ $sj->nama_supir ?? '-' }}</td>
            </tr>
        </table>

        <table class="signature-table">
            <tr>
                <td>Keamanan</td>
                <td>Yang Menerima</td>
                <td>Mengetahui</td>
                <td>Yang Membuat</td>
            </tr>
            <tr>
                <td>(----------------)</td>
                <td>(----------------)</td>
                <td>(----------------)</td>
                <td>(----------------)</td>
            </tr>
        </table>

        <div class="notes-bottom">
            * Barang-barang telah diterima dalam keadaan baik dan cukup<br>
            <b><i>Setelah barang keluar dari PT Kim Pai Dyna Tube sepenuhnya adalah tanggung jawab pihak transpoter</i></b><br>
            Putih:Accounting; Merah:Accounting; Putih:Ekspedisi; Kuning:Customer; Hijau:Customer; Biru:Warehouse
        </div>

    </div>
</body>
</html>