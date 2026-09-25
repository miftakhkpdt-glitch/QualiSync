<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lembar 8D - {{ $capa->no_capa }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 9px; color: #000; margin: 0; padding: 10px; background: #fff; line-height: 1.2; }
        .page { width: 100%; max-width: 800px; margin: 0 auto 20px auto; border: 1px solid #000; background: #fff; page-break-after: always; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 3px 5px; vertical-align: top; }
        .header-bg { background-color: #fffae6; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .section-header { background-color: #fffae6; font-weight: bold; padding: 3px 5px; font-size: 9.5px; }
        
        @media print {
            body { padding: 0; background: none; }
            .no-print { display: none; }
            .page { border: none; margin: 0; box-shadow: none; }
        }
    </style>
</head>
<body>

    <!-- Tombol Navigasi -->
    <div class="no-print" style="margin-bottom: 15px; text-align: right; max-width: 800px; margin-left: auto; margin-right: auto;">
        <button onclick="window.print()" style="background: #d4a32a; color: white; border: none; padding: 8px 15px; border-radius: 4px; font-weight: bold; cursor: pointer;">
            Cetak / Simpan PDF (2 Halaman)
        </button>
        <a href="{{ url('/capa-8d/supplier') }}" style="background: #6c757d; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-weight: bold; margin-left: 5px;">Kembali</a>
    </div>

    <!-- ================= HALAMAN 1 ================= -->
    <div class="page">
        <!-- Header Dokumen -->
        <table>
            <tr>
                <td style="width: 25%;">
                    <strong style="font-size: 11px; color: #735d25;">KIM PAI DYNA</strong><br>
                    <span style="font-size: 8px;">Quality Assurance Department</span>
                </td>
                <td class="text-center" style="width: 50%;">
                    <strong style="font-size: 11px;">Lembar Penyelesaian Masalah Metode 8D</strong><br>
                    <span style="font-size: 8px;">(Problem Solving Sheet Using 8D Method)</span><br>
                    <span style="font-size: 10px; color: #b88b20;">CAPA No.: {{ $capa->no_capa }}</span>
                </td>
                <td style="width: 25%; font-size: 8.5px;">
                    <strong>No. Dokumen:</strong> FRM/18-QA/17<br>
                    <strong>No. Revisi:</strong> 01 (12.02.24)
                </td>
            </tr>
        </table>

        <!-- Tema & Info Komplain -->
        <table>
            <tr>
                <td style="width: 45%;">
                    <strong>Tema Masalah (Problem Theme):</strong><br>
                    {{ $capa->tema_masalah }}
                </td>
                <td style="width: 30%;">
                    <strong>Tanggal Komplain (Complaint date):</strong><br>
                    {{ $capa->tanggal_temuan }}<br><br>
                    <strong>Tanggal Penerimaan Sample:</strong><br> -
                </td>
                <td style="width: 25%; font-size: 8.5px;">
                    <strong>Sumber Informasi Masalah:</strong><br>
                    ☐ Komplain Customer<br>
                    ☐ Masalah Inprocess<br>
                    ☑ <strong>Masalah Incoming/Supplier</strong>
                </td>
            </tr>
        </table>

        <!-- 1. Tim & Foto -->
        <table>
            <tr>
                <td class="section-header" style="width: 50%;">1. Pembentukan & Anggota Tim Penyelesaian Masalah (Problem Solving Team)</td>
                <td class="section-header" style="width: 50%;">Foto Masalah (Problem Picture)</td>
            </tr>
            <tr>
                <td style="height: 220px; font-size: 8.5px; vertical-align: top;">
                    Supplier: <strong>{{ $capa->nama_supplier }}</strong><br>
                    Anggota: Team QC & Supplier Representative ({{ $capa->pic_supplier ?? 'PIC Supplier' }})
                </td>
                <td style="height: 220px; text-align: center; vertical-align: middle; padding: 0;">
                    @if($capa->foto_masalah)
                        <img src="{{ asset('uploads/' . $capa->foto_masalah) }}" alt="Foto Barang NG" style="width: 100%; height: 218px; object-fit: contain; display: block; margin: 0 auto;">
                    @else
                        <span style="color: #777; font-size: 9px; line-height: 220px;">[Tidak ada foto]</span>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Keterangan Masalah -->
        <table>
            <tr>
                <td class="section-header">Keterangan Masalah dan Fenomena Terjadinya Masalah (Description of the problem)</td>
            </tr>
            <tr>
                <td style="height: 40px;">
                    {{ $capa->deskripsi_masalah }}
                </td>
            </tr>
        </table>

        <!-- 2. Penelusuran Lot -->
        <table>
            <tr>
                <td class="section-header" colspan="2">2. Penelusuran dan history lot produksi produk yang bermasalah (Tracing & production lot history)</td>
            </tr>
            <tr>
                <td style="width: 50%;">
                    Nama Produk: {{ $capa->nama_produk ?? '' }} <br>
                    No Seri / Lot: {{ $capa->no_lot ?? '' }} <br>
                    Tanggal Produksi / Shift: {{ $capa->tanggal_produksi_shift ?? '' }}
                </td>
                <td style="width: 50%;">
                    Sejarah Produksi / Sortir: <br>
                    No Mesin / Line: {{ $capa->no_mesin_line ?? '' }}
                </td>
            </tr>
        </table>

        <!-- Sejarah Perubahan 5M + 1E -->
        <table>
            <tr>
                <td class="section-header" colspan="6">Sejarah Perubahan 5M + 1E pada tanggal produksi (History of 5M + 1E changes)</td>
            </tr>
            <tr style="text-align: center; font-weight: bold; background: #f9f9f9;">
                <td style="width: 16%;">Orang / Operator</td>
                <td style="width: 16%;">Mesin (Machine)</td>
                <td style="width: 16%;">Metode (Method)</td>
                <td style="width: 16%;">Cetakan (Mold)</td>
                <td style="width: 16%;">Material</td>
                <td style="width: 20%;">Lingkungan (Environment)</td>
            </tr>
            <tr style="text-align: center;">
                <td>{{ $capa->history_man ?? '' }}</td>
                <td>{{ $capa->history_machine ?? '' }}</td>
                <td>{{ $capa->history_method ?? '' }}</td>
                <td>{{ $capa->history_mold ?? '' }}</td>
                <td>{{ $capa->history_material ?? '' }}</td>
                <td>{{ $capa->history_env ?? '' }}</td>
            </tr>
        </table>

        <!-- 3. Tindakan Penahanan -->
        <table>
            <tr>
                <td class="section-header">3. Tindakan penahanan / tindakan langsung untuk memastikan customer terhindar (Containment actions)</td>
            </tr>
            <tr>
                <td style="height: 35px; white-space: pre-line;">
                    {{ $capa->containment_action ?? '' }}
                </td>
            </tr>
        </table>

        <!-- 4. Analisa Akar Masalah -->
        <table>
            <tr>
                <td class="section-header" colspan="7">4. Analisa Akar Penyebab Masalah (Potential root cause analysis)</td>
            </tr>
            <tr style="text-align: center; font-weight: bold; background: #f9f9f9;">
                <td style="width: 22%;">Defect / Problem</td>
                <td style="width: 13%;">Manusia (Man)</td>
                <td style="width: 13%;">Mesin (Machine)</td>
                <td style="width: 13%;">Metode (Method)</td>
                <td style="width: 13%;">Material</td>
                <td style="width: 13%;">Cetakan (Mold)</td>
                <td style="width: 13%;">Lingkungan</td>
            </tr>
            <tr style="text-align: center; height: 30px; vertical-align: middle;">
                <td style="text-align: left; font-weight: bold;">{{ $capa->tema_masalah }}</td>
                <td>{{ str_contains($capa->root_cause_category ?? '', 'Man') ? '☑' : '☐' }}</td>
                <td>{{ (str_contains($capa->root_cause_category ?? '', 'Machine') || str_contains($capa->root_cause_category ?? '', 'Mesin')) ? '☑' : '☐' }}</td>
                <td>{{ (str_contains($capa->root_cause_category ?? '', 'Method') || str_contains($capa->root_cause_category ?? '', 'Metode')) ? '☑' : '☐' }}</td>
                <td>{{ str_contains($capa->root_cause_category ?? '', 'Material') ? '☑' : '☐' }}</td>
                <td>{{ (str_contains($capa->root_cause_category ?? '', 'Mold') || str_contains($capa->root_cause_category ?? '', 'Cetakan')) ? '☑' : '☐' }}</td>
                <td>{{ (str_contains($capa->root_cause_category ?? '', 'Environment') || str_contains($capa->root_cause_category ?? '', 'Lingkungan')) ? '☑' : '☐' }}</td>
            </tr>
        </table>

        <!-- 5. Why - Why Analisis -->
        <table>
            <tr>
                <td class="section-header" colspan="8">5. Why - Why Analisis (Why - Why Analysis)</td>
            </tr>
            <tr style="text-align: center; font-weight: bold; background: #f9f9f9;">
                <td style="width: 18%;">Defect</td>
                <td style="width: 16%;">Kategori</td>
                <td style="width: 13.2%;">W1</td>
                <td style="width: 13.2%;">W2</td>
                <td style="width: 13.2%;">W3</td>
                <td style="width: 13.2%;">W4</td>
                <td style="width: 13.2%;">W5</td>
            </tr>
            
            @php
                $w1Array = array_filter(explode("\n", $capa->why_1 ?? ''));
                $w2Array = array_filter(explode("\n", $capa->why_2 ?? ''));
                $w3Array = array_filter(explode("\n", $capa->why_3 ?? ''));
                $w4Array = array_filter(explode("\n", $capa->why_4 ?? ''));
                $w5Array = array_filter(explode("\n", $capa->why_5 ?? ''));
                
                $rawCategories = array_filter(explode(", ", $capa->root_cause_category ?? ''));
                $categories = array_values($rawCategories);
            @endphp

            @if(count($categories) > 0)
                @foreach($categories as $index => $cat)
                <tr>
                    @if($index == 0)
                        <td rowspan="{{ count($categories) }}" style="vertical-align: middle; font-weight: bold;">
                            {{ $capa->tema_masalah }}
                        </td>
                    @endif
                    <td style="font-weight: bold; background: #fcfcfc;">{{ $cat }}</td>
                    <td>{{ isset($w1Array[$index]) ? trim(preg_replace('/^\[.*?\]\s*/', '', $w1Array[$index])) : '' }}</td>
                    <td>{{ isset($w2Array[$index]) ? trim(preg_replace('/^\[.*?\]\s*/', '', $w2Array[$index])) : '' }}</td>
                    <td>{{ isset($w3Array[$index]) ? trim(preg_replace('/^\[.*?\]\s*/', '', $w3Array[$index])) : '' }}</td>
                    <td>{{ isset($w4Array[$index]) ? trim(preg_replace('/^\[.*?\]\s*/', '', $w4Array[$index])) : '' }}</td>
                    <td>{{ isset($w5Array[$index]) ? trim(preg_replace('/^\[.*?\]\s*/', '', $w5Array[$index])) : '' }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td>{{ $capa->tema_masalah }}</td>
                    <td></td><td></td><td></td><td></td><td></td><td></td>
                </tr>
            @endif
        </table>

        <!-- 6. Apa yang seharusnya terjadi VS sebenarnya (PERBAIKAN: DIBERSIHKAN DARI HARDCODED TEXT) -->
        <table>
            <tr>
                <td class="section-header" colspan="7">6. Apa yang seharusnya terjadi VS apa yang sebenarnya terjadi?</td>
            </tr>
            <tr style="text-align: center; font-weight: bold; background: #f9f9f9;">
                <td style="width: 18%;">Defect</td>
                <td style="width: 16%;">Kategori</td>
                <td style="width: 22%;">Why Terakhir (W5)</td>
                <td style="width: 13%;">Kondisi Standard</td>
                <td style="width: 12%;">Kondisi Actual</td>
                <td style="width: 11%;">Verifikasi</td>
                <td style="width: 8%;">Hasil</td>
            </tr>

            @php
                $stdArray = array_values(array_filter(explode("\n", $capa->kondisi_std ?? '')));
                $actArray = array_values(array_filter(explode("\n", $capa->kondisi_act ?? '')));
                $verArray = array_values(array_filter(explode("\n", $capa->verifikasi ?? '')));
                $kesArray = array_values(array_filter(explode("\n", $capa->kesimpulan_verifikasi ?? '')));
            @endphp

            @if(count($categories) > 0)
                @foreach($categories as $index => $cat)
                <tr>
                    @if($index == 0)
                        <td rowspan="{{ count($categories) }}" style="vertical-align: middle; font-weight: bold;">
                            {{ $capa->tema_masalah }}
                        </td>
                    @endif
                    <td style="font-weight: bold;">{{ $cat }}</td>
                    <td>{{ isset($w5Array[$index]) ? trim(preg_replace('/^\[.*?\]\s*/', '', $w5Array[$index])) : '' }}</td>
                    <td>{{ $stdArray[$index] ?? '' }}</td>
                    <td>{{ $actArray[$index] ?? '' }}</td>
                    <td>{{ $verArray[$index] ?? '' }}</td>
                    <td class="text-center">{{ $kesArray[$index] ?? '' }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td>{{ $capa->tema_masalah }}</td>
                    <td></td><td></td><td></td><td></td><td></td><td></td>
                </tr>
            @endif
        </table>

        <!-- 7. Kesimpulan Akar Masalah -->
        <table>
            <tr>
                <td class="section-header">7. Kesimpulan Akar Penyebab Masalah (Conclusion for root cause)</td>
            </tr>
            <tr>
                <td style="height: 30px; font-weight: bold; color: #333; white-space: pre-line;">
                    {{ $capa->root_cause ?? '' }}
                </td>
            </tr>
        </table>
        
        <div class="text-right" style="padding: 4px; font-size: 8px;">Hal 1 dari 2</div>
    </div>


    <!-- ================= HALAMAN 2 ================= -->
    <div class="page">
        <!-- Header Halaman 2 -->
        <table>
            <tr>
                <td style="width: 25%;">
                    <strong style="font-size: 11px; color: #735d25;">KIM PAI DYNA</strong><br>
                    <span style="font-size: 8px;">Quality Assurance Department</span>
                </td>
                <td class="text-center" style="width: 50%;">
                    <strong style="font-size: 11px;">Lembar Penyelesaian Masalah Metode 8D</strong><br>
                    <span style="font-size: 8px;">CAPA No.: {{ $capa->no_capa }}</span>
                </td>
                <td style="width: 25%; font-size: 8.5px;">
                    <strong>No. Dokumen:</strong> FRM/18-QA/17<br>
                    <strong>No. Revisi:</strong> 01 (12.02.24)
                </td>
            </tr>
        </table>

        <!-- 8. Tindakan Perbaikan (CA) & Pencegahan (PA) (PERBAIKAN: DIBERSIHKAN DARI FALLBACK '-') -->
        <table>
            <tr>
                <td class="section-header" style="width: 50%;">8. Tindakan Perbaikan (Corrective Action)</td>
                <td class="section-header" style="width: 50%;">Tindakan Pencegahan (Preventive Action)</td>
            </tr>
            <tr>
                <td style="font-size: 8.5px; height: 160px; vertical-align: top; white-space: pre-line; line-height: 1.4;">
                    {{ $capa->corrective_action ?? '' }}
                </td>
                <td style="font-size: 8.5px; height: 160px; vertical-align: top; white-space: pre-line; line-height: 1.4;">
                    {{ $capa->preventive_action ?? '' }}
                </td>
            </tr>
        </table>

        <!-- 9. Implementasi dan Validasi (PERBAIKAN: DIBERSIHKAN DARI FALLBACK 'OK') -->
        <table>
            <tr>
                <td class="section-header" colspan="4">9. Implementasi dan Validasi Tindakan Perbaikan dan Pencegahan</td>
            </tr>
            <tr style="background: #f9f9f9; font-weight: bold; text-align: center;">
                <td style="width: 45%;">Daftar Tindakan Perbaikan</td>
                <td style="width: 25%;">Hasil & Tanggal Validasi</td>
                <td style="width: 15%;">Status (OK/Not OK)</td>
                <td style="width: 15%;">PIC</td>
            </tr>
            <tr>
                <td style="height: 35px; white-space: pre-line; font-size: 8px;">{{ $capa->corrective_action ?? '' }}</td>
                <td>{{ $capa->val_hasil_ca ?? (isset($capa->tanggal_implementasi) ? 'Target Impl: ' . $capa->tanggal_implementasi : '') }}</td>
                <td class="text-center" style="font-weight: bold;">{{ $capa->val_status_ca ?? '' }}</td>
                <td>{{ $capa->val_pic_ca ?? ($capa->pic_supplier ?? '') }}</td>
            </tr>
            <tr style="background: #f9f9f9; font-weight: bold; text-align: center;">
                <td>Daftar Tindakan Pencegahan</td>
                <td>Hasil & Tanggal Validasi</td>
                <td>Status (OK/Not OK)</td>
                <td>PIC</td>
            </tr>
            <tr>
                <td style="height: 35px; white-space: pre-line; font-size: 8px;">{{ $capa->preventive_action ?? '' }}</td>
                <td>{{ $capa->val_hasil_pa ?? (isset($capa->tanggal_implementasi) ? 'Target Impl: ' . $capa->tanggal_implementasi : '') }}</td>
                <td class="text-center" style="font-weight: bold;">{{ $capa->val_status_pa ?? '' }}</td>
                <td>{{ $capa->val_pic_pa ?? ($capa->pic_supplier ?? '') }}</td>
            </tr>
        </table>

        <!-- 10. Ulasan Dokumentasi (PERBAIKAN: DIBERSIHKAN DARI TEXT HARDCODED DESKRIPSI) -->
        <table>
            <tr>
                <td class="section-header" colspan="4">10. Ulasan Tindakan Perbaikan & Pencegahan Terkait Perubahan & Standarisasi Dokumen</td>
            </tr>
            <tr style="background: #f9f9f9; font-weight: bold; text-align: center;">
                <td style="width: 40%;">Jenis Dokumen (Document Type)</td>
                <td style="width: 35%;">Deskripsi Dokumen</td>
                <td style="width: 10%;">PIC</td>
                <td style="width: 15%;">Tanggal Pelaksanaan</td>
            </tr>
            <tr>
                <td style="height: 55px; font-size: 8.5px; line-height: 1.3;">
                    {{ str_contains($capa->doc_types ?? '', 'Layout operator') ? '☑' : '☐' }} Layout operator<br>
                    {{ str_contains($capa->doc_types ?? '', 'Flow Chart Proses') ? '☑' : '☐' }} Flow Chart Proses<br>
                    {{ str_contains($capa->doc_types ?? '', 'Pengukuran') ? '☑' : '☐' }} Pengukuran / Gages<br>
                    {{ str_contains($capa->doc_types ?? '', 'FMEA') ? '☑' : '☐' }} FMEA (Design/Process)<br>
                    {{ str_contains($capa->doc_types ?? '', 'Spesifikasi Produk') ? '☑' : '☐' }} Spesifikasi Produk
                </td>
                <td>{{ $capa->deskripsi_dokumen ?? '' }}</td>
                <td>{{ $capa->pic_supplier ?? '' }}</td>
                <td>
                    Plan: {{ $capa->doc_plan_date ?? $capa->tanggal_implementasi ?? '' }}<br>
                    Aktual: {{ $capa->doc_act_date ?? $capa->tanggal_submit_supplier ?? '' }}
                </td>
            </tr>
        </table>

        <!-- 11. Control Chart -->
        <table>
            <tr>
                <td class="section-header">11. Control Chart (Pilih salah satu tools control chart yang sesuai)</td>
            </tr>
            <tr>
                <td style="height: 35px; font-size: 8.5px; color: #333;">
                    <strong>Tools Terpilih:</strong> {{ $capa->control_chart ?? '[ Belum Dipilih ]' }}<br>
                    <span style="color: #666; font-size: 8px;">
                        *Data variable: IMR chart, Xbar R chart, Xbar S chart | *Data attribute: c chart, u chart, np chart, p chart
                    </span>
                </td>
            </tr>
        </table>

        <!-- 12. Perayaan Tim (PERBAIKAN: DIBERSIHKAN DARI TEKS HARDCODED) -->
        <table>
            <tr>
                <td class="section-header">12. Perayaan Tim (Team Celebration)</td>
            </tr>
            <tr>
                <td style="height: 25px;">{{ $capa->team_celebration ?? '' }}</td>
            </tr>
        </table>

        <!-- Tanda Tangan Pengesahan -->
        <table>
            <tr>
                <td class="text-center" style="width: 33%; height: 45px; vertical-align: bottom;">
                    Disiapkan oleh:<br><br><strong>( {{ $capa->pic_supplier ?? 'Staff Supplier' }} )</strong>
                </td>
                <td class="text-center" style="width: 33%; height: 45px; vertical-align: bottom;">
                    Diperiksa oleh:<br><br><strong>( Spv / QA Head )</strong>
                </td>
                <td class="text-center" style="width: 33%; height: 45px; vertical-align: bottom;">
                    Disetujui oleh:<br><br><strong>( Manager )</strong>
                </td>
            </tr>
        </table>

        <div class="text-right" style="padding: 4px; font-size: 8px;">Hal 2 dari 2</div>
    </div>

</body>
</html>