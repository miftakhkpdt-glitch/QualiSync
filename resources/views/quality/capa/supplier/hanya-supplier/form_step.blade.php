<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengisian CAPA 8D Supplier - PT KIMPAI DYNA TUBE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 850px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .header { text-align: center; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px; margin-bottom: 25px; }
        .header h2 { color: #3d71e1; margin: 0; }
        
        /* Step Wizard Nav */
        .wizard-steps { display: flex; justify-content: space-between; margin-bottom: 30px; border-bottom: 1px solid #e0e0e0; padding-bottom: 15px; }
        .step-node { font-size: 12px; font-weight: bold; color: #aaa; text-align: center; flex: 1; }
        .step-node.active { color: #d4a32a; }
        .step-node.completed { color: #28a745; }
        .step-node .circle { width: 28px; height: 28px; border-radius: 50%; background: #e0e0e0; color: #fff; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px auto; }
        .step-node.active .circle { background: #d4a32a; }
        .step-node.completed .circle { background: #28a745; }

        /* Step Card */
        .step-content { display: none; }
        .step-content.active { display: block; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 5px; font-size: 13px; color: #333; }
        .form-control { width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-size: 13px; }
        .form-control:focus { border-color: #d4a32a; outline: none; }
        
        /* Buttons */
        .btn-group { display: flex; justify-content: space-between; margin-top: 25px; }
        .btn { padding: 9px 20px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; font-size: 13px; }
        .btn-prev { background: #6c757d; color: #fff; }
        .btn-next { background: #d4a32a; color: #fff; }
        .btn-submit { background: #28a745; color: #fff; display: none; }
        
        .info-box { background: #fff8e7; border-left: 4px solid #d4a32a; padding: 12px; margin-bottom: 15px; font-size: 13px; }
        .alert-success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>PORTAL SUPPLIER - FORM CAPA 8D</h2>
        <small>PT KIMPAI DYNA TUBE - Quality Assurance</small>
    </div>

    @if(session('success'))
        <div class="alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    <!-- Indicator Steps -->
    <div class="wizard-steps">
        <div class="step-node active" id="node-1"><div class="circle">1</div> Info Defect</div>
        <div class="step-node" id="node-2"><div class="circle">2</div> Containment</div>
        <div class="step-node" id="node-3"><div class="circle">3</div> Root Cause</div>
        <div class="step-node" id="node-4"><div class="circle">4</div> CAPA & Validasi</div>
        <div class="step-node" id="node-5"><div class="circle">5</div> Dokumen & Final</div>
    </div>

    <form action="/capa-8d/supplier/submit/{{ $capa->id }}" method="POST">
        @csrf

        <!-- STEP 1: Info Masalah (Read Only) -->
        <div class="step-content active" id="step-1">
            <h3>Langkah 1: Informasi Temuan Masalah</h3>
            <div class="info-box">
                <strong>No. CAPA:</strong> {{ $capa->no_capa }}<br>
                <strong>Supplier:</strong> {{ $capa->nama_supplier }}<br>
                <strong>Tanggal Temuan:</strong> {{ $capa->tanggal_temuan }}
            </div>
            <div class="form-group">
                <label>Tema Masalah (Problem Theme)</label>
                <input type="text" class="form-control" value="{{ $capa->tema_masalah }}" readonly style="background:#f0f0f0;">
            </div>
            <div class="form-group">
                <label>Deskripsi & Fenomena Masalah</label>
                <textarea class="form-control" rows="3" readonly style="background:#f0f0f0;">{{ $capa->deskripsi_masalah }}</textarea>
            </div>
            @if($capa->foto_masalah)
            <div class="form-group">
                <label>Foto Barang NG Dari QC</label><br>
                <img src="{{ asset('uploads/' . $capa->foto_masalah) }}" style="max-height: 180px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            @endif
        </div>

        <!-- STEP 2: Penelusuran Lot & Tindakan Penahanan -->
        <div class="step-content" id="step-2">
            <h3>Langkah 2: Penelusuran Lot & Tindakan Penahanan</h3>

            <div style="background: #fafafa; border: 1px solid #dcdcdc; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                <label style="font-weight: bold; font-size: 13px; color: #735d25; display: block; margin-bottom: 10px;">
                    2. Penelusuran dan History Lot Produksi Produk Bermasalah (Tracing & Production Lot History)
                </label>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div class="form-group">
                        <label>Nama Produk / Komponen</label>
                        <input type="text" name="nama_produk" class="form-control" placeholder="Masukkan nama produk..." value="{{ $capa->nama_produk ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label>No. Seri / Lot Produksi</label>
                        <input type="text" name="no_lot" class="form-control" placeholder="Contoh: LOT-20260728-A" value="{{ $capa->no_lot ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Produksi / Shift</label>
                        <input type="text" name="tanggal_produksi_shift" class="form-control" placeholder="Contoh: 28/07/2026 - Shift 1" value="{{ $capa->tanggal_produksi_shift ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label>No. Mesin / Line Produksi</label>
                        <input type="text" name="no_mesin_line" class="form-control" placeholder="Contoh: Line 2 / Mesin Seaming A" value="{{ $capa->no_mesin_line ?? '' }}">
                    </div>
                </div>

                <label style="font-weight: bold; font-size: 12.5px; color: #444; display: block; margin-top: 15px; margin-bottom: 8px;">
                    Sejarah Perubahan 5M + 1E Pada Tanggal Produksi:
                </label>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px;">
                    <input type="text" name="history_man" class="form-control" placeholder="Orang / Operator (Std vs Act)" value="{{ $capa->history_man ?? '' }}">
                    <input type="text" name="history_machine" class="form-control" placeholder="Mesin / Machine (Std vs Act)" value="{{ $capa->history_machine ?? '' }}">
                    <input type="text" name="history_method" class="form-control" placeholder="Metode / Method (Std vs Act)" value="{{ $capa->history_method ?? '' }}">
                    <input type="text" name="history_mold" class="form-control" placeholder="Cetakan / Mold (Std vs Act)" value="{{ $capa->history_mold ?? '' }}">
                    <input type="text" name="history_material" class="form-control" placeholder="Material (Std vs Act)" value="{{ $capa->history_material ?? '' }}">
                    <input type="text" name="history_env" class="form-control" placeholder="Lingkungan / Environment" value="{{ $capa->history_env ?? '' }}">
                </div>
            </div>

            <div style="background: #fafafa; border: 1px solid #dcdcdc; border-radius: 8px; padding: 15px;">
                <label style="font-weight: bold; font-size: 13px; color: #735d25; display: block; margin-bottom: 5px;">
                    3. Tindakan Penahanan / Containment Actions
                </label>
                <p style="font-size:11.5px; color:#666; margin-top:0; margin-bottom: 10px;">
                    Tindakan cepat/sementara untuk mengamankan stok produk agar customer terhindar dari defect.
                </p>
                <div class="form-group" style="margin-bottom:0;">
                    <textarea name="containment_action" class="form-control" rows="3" placeholder="Contoh: Melakukan sortir 100% pada stok gudang supplier dan mengkarantina lot terkontaminasi..." required>{{ $capa->containment_action ?? '' }}</textarea>
                </div>
            </div>
        </div>

        <!-- STEP 3: Root Cause, Why-Why & Comparison -->
        <div class="step-content" id="step-3">
            <h3>Langkah 3: Analisa Akar Penyebab Masalah (Root Cause) & Verifikasi Standard</h3>
            <p style="font-size: 11.5px; color: #555; margin-bottom: 15px;">
                Silakan pilih kategori faktor (5M + 1E), isi alur <i>Why-Why Analysis</i>, serta perbandingan <b>Kondisi Standard VS Actual</b> untuk faktor tersebut.
            </p>

            <div id="why-boxes-container">
                <div class="why-card-box" style="background: #fafafa; border: 1px solid #dcdcdc; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <label style="font-weight: bold; font-size: 13px; color: #735d25;">
                            <i class="fas fa-list-check"></i> Kategori Faktor Ke-1:
                        </label>
                    </div>

                    <div class="form-group">
                        <select name="why_analysis[0][kategori]" class="form-control" required style="font-weight: bold; background: #fff;">
                            <option value="">-- Pilih Kategori Faktor (5M + 1E) --</option>
                            <option value="Manusia (Man)">👨‍🔧 Manusia (Man)</option>
                            <option value="Mesin (Machine)">⚙️ Mesin (Machine)</option>
                            <option value="Metode (Method)">📋 Metode (Method)</option>
                            <option value="Material">📦 Material</option>
                            <option value="Cetakan (Mold)">🧩 Cetakan (Mold)</option>
                            <option value="Lingkungan (Environment)">🌡️ Lingkungan (Environment)</option>
                        </select>
                    </div>

                    <div style="margin-top: 10px;">
                        <label style="font-size: 12px; font-weight: 600; color: #444; display: block; margin-bottom: 5px;">5. Alur Why-Why Analysis:</label>
                        <input type="text" name="why_analysis[0][w1]" class="form-control" placeholder="Why 1: Mengapa defect terjadi pada faktor ini?" style="margin-bottom: 6px;" value="">
                        <input type="text" name="why_analysis[0][w2]" class="form-control" placeholder="Why 2: Mengapa hal di Why 1 terjadi?" style="margin-bottom: 6px;" value="">
                        <input type="text" name="why_analysis[0][w3]" class="form-control" placeholder="Why 3: Mengapa hal di Why 2 terjadi?" style="margin-bottom: 6px;" value="">
                        <input type="text" name="why_analysis[0][w4]" class="form-control" placeholder="Why 4: Mengapa hal di Why 3 terjadi?" style="margin-bottom: 6px;" value="">
                        <input type="text" name="why_analysis[0][w5]" class="form-control" placeholder="Why 5: Akar masalah mendasar untuk faktor ini" style="margin-bottom: 6px;" value="">
                    </div>

                    <div style="margin-top: 15px; background: #fff; padding: 12px; border: 1px dashed #bbb; border-radius: 6px;">
                        <label style="font-size: 12px; font-weight: bold; color: #735d25; display: block; margin-bottom: 8px;">
                            6. Apa yang seharusnya terjadi VS apa yang sebenarnya terjadi?
                        </label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 6px;">
                            <div>
                                <label style="font-size: 11px; font-weight: 600;">Kondisi Standard (Seharusnya)</label>
                                <input type="text" name="why_analysis[0][kondisi_std]" class="form-control" placeholder="Contoh: Parameter suhu 150°C" value="">
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 600;">Kondisi Actual (Sebenarnya)</label>
                                <input type="text" name="why_analysis[0][kondisi_act]" class="form-control" placeholder="Contoh: Parameter suhu drop 120°C" value="">
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 8px;">
                            <div>
                                <label style="font-size: 11px; font-weight: 600;">Verifikasi (Analisa Tools / Alat)</label>
                                <input type="text" name="why_analysis[0][verifikasi]" class="form-control" placeholder="Contoh: Visual Check / Thermo Sensor" value="">
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 600;">Kesimpulan</label>
                                <select name="why_analysis[0][kesimpulan]" class="form-control">
                                    <option value="Ya">Ya (Penyebab)</option>
                                    <option value="Tdk">Tidak</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" onclick="addWhyCategoryBox()" style="background: #e6f4ea; color: #1e7e34; border: 1px dashed #28a745; padding: 10px 15px; border-radius: 6px; font-weight: bold; cursor: pointer; width: 100%; font-size: 13px; margin-bottom: 20px;">
                <i class="fas fa-plus-circle"></i> + Tambah Kategori Faktor dari Yang Lain (Man / Machine / Method / dll)
            </button>

            <div class="form-group">
                <label for="root_cause" style="font-weight: bold; font-size: 13px; color: #333;">7. Kesimpulan Akhir Akar Penyebab Masalah (Root Cause Summary)</label>
                <textarea name="root_cause" class="form-control" rows="3" placeholder="Rangkuman kesimpulan akar penyebab masalah dari seluruh faktor yang dianalisis di atas..." required>{{ $capa->root_cause ?? '' }}</textarea>
            </div>
        </div>

        <!-- STEP 4: Tindakan Perbaikan (8a), Pencegahan (8b) & Validasi (9) -->
        <div class="step-content" id="step-4">
            <h3>Langkah 4: Tindakan Perbaikan (CA) & Pencegahan (PA)</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
                <div style="background: #fafafa; border: 1px solid #dcdcdc; border-radius: 8px; padding: 12px;">
                    <label style="font-weight: bold; font-size: 12.5px; color: #735d25; display: block; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">
                        8a. Tindakan Perbaikan (Corrective Action)
                    </label>
                    
                    @php
                        $caOptions = [
                            'Sortir 100%',
                            'Perbaikan Mesin (Repair machine)',
                            'Perbaikan Jig/alat kerja (Repair Jig)',
                            'Perubahan layout kerja (Work layout changes)',
                            'Revisi Instruksi Kerja (Revised Work Instruction)',
                            'Revisi point check / CTQ (Revised point check / CTQ)',
                            'Briefing dan training operator',
                            'Perubahan grade atau jenis material',
                            'Penambahan additive/mengubah dosis MB',
                            'Lain - lain (Others)'
                        ];
                    @endphp

                    @foreach($caOptions as $index => $option)
                    <div style="margin-bottom: 10px; font-size: 11.5px;">
                        <label style="font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                            <input type="checkbox" name="corrective_options[]" value="{{ $option }}" onchange="toggleDetailInput('ca_text_{{ $index }}', this)">
                            <span>{{ $option }}</span>
                        </label>
                        <input type="text" id="ca_text_{{ $index }}" name="corrective_details[{{ $option }}]" class="form-control" placeholder="Keterangan detail yang dilakukan..." style="margin-top: 4px; display: none; font-size: 11px; padding: 5px 8px;">
                    </div>
                    @endforeach
                </div>

                <div style="background: #fafafa; border: 1px solid #dcdcdc; border-radius: 8px; padding: 12px;">
                    <label style="font-weight: bold; font-size: 12.5px; color: #735d25; display: block; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">
                        8b. Tindakan Pencegahan (Preventive Action)
                    </label>

                    @php
                        $paOptions = [
                            'Penambahan CTQ / jenis pengukuran',
                            'Penambahan alat ukur (Adding measuring tools)',
                            'Pembuatan Instruksi kerja baru',
                            'Penambahan visual kontrol/sistem pokayoke',
                            'Penambahan point check preventive maintenance',
                            'Penambahan point check corrective maintenance',
                            'Sosialisasi dan training operator',
                            'Cek ulang kesesuaian material & MB',
                            'Cek ulang kesesuaian dosis additive dan MB',
                            'Lain - lain (Others)'
                        ];
                    @endphp

                    @foreach($paOptions as $index => $option)
                    <div style="margin-bottom: 10px; font-size: 11.5px;">
                        <label style="font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                            <input type="checkbox" name="preventive_options[]" value="{{ $option }}" onchange="toggleDetailInput('pa_text_{{ $index }}', this)">
                            <span>{{ $option }}</span>
                        </label>
                        <input type="text" id="pa_text_{{ $index }}" name="preventive_details[{{ $option }}]" class="form-control" placeholder="Keterangan detail yang dilakukan..." style="margin-top: 4px; display: none; font-size: 11px; padding: 5px 8px;">
                    </div>
                    @endforeach
                </div>
            </div>

            <div style="background: #fafafa; border: 1px solid #dcdcdc; border-radius: 8px; padding: 15px;">
                <label style="font-weight: bold; font-size: 13px; color: #735d25; display: block; margin-bottom: 10px;">
                    9. Implementasi dan Validasi Tindakan Perbaikan dan Pencegahan
                </label>

                <div style="margin-bottom: 12px; background: #fff; padding: 12px; border: 1px solid #e0e0e0; border-radius: 6px;">
                    <strong style="font-size: 12px; color: #333; display: block; margin-bottom: 6px;">
                        <i class="fas fa-check-square"></i> Validasi Tindakan Perbaikan (Corrective Action)
                    </strong>
                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 8px;">
                        <div>
                            <label style="font-size: 11px; font-weight: 600;">Hasil & Tanggal Validasi</label>
                            <input type="text" name="val_hasil_ca" class="form-control" placeholder="Contoh: Lot X telah disortir 100%">
                        </div>
                        <div>
                            <label style="font-size: 11px; font-weight: 600;">Status</label>
                            <select name="val_status_ca" class="form-control">
                                <option value="OK">OK</option>
                                <option value="Not OK">Not OK</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size: 11px; font-weight: 600;">PIC</label>
                            <input type="text" name="val_pic_ca" class="form-control" placeholder="Nama PIC">
                        </div>
                    </div>
                </div>

                <div style="background: #fff; padding: 12px; border: 1px solid #e0e0e0; border-radius: 6px;">
                    <strong style="font-size: 12px; color: #333; display: block; margin-bottom: 6px;">
                        <i class="fas fa-shield-alt"></i> Validasi Tindakan Pencegahan (Preventive Action)
                    </strong>
                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 8px;">
                        <div>
                            <label style="font-size: 11px; font-weight: 600;">Hasil & Tanggal Validasi</label>
                            <input type="text" name="val_hasil_pa" class="form-control" placeholder="Contoh: IK Baru diterapkan">
                        </div>
                        <div>
                            <label style="font-size: 11px; font-weight: 600;">Status</label>
                            <select name="val_status_pa" class="form-control">
                                <option value="OK">OK</option>
                                <option value="Not OK">Not OK</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size: 11px; font-weight: 600;">PIC</label>
                            <input type="text" name="val_pic_pa" class="form-control" placeholder="Nama PIC">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 5: Standarisasi Dokumen (10), Control Chart (11), Perayaan Tim (12) & Submit -->
        <div class="step-content" id="step-5">
            <h3>Langkah 5: Standarisasi Dokumen, Control Chart & Finalisasi</h3>

            <div style="background: #fafafa; border: 1px solid #dcdcdc; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                <label style="font-weight: bold; font-size: 13px; color: #735d25; display: block; margin-bottom: 10px;">
                    10. Ulasan Tindakan Perbaikan & Pencegahan Terkait Perubahan & Standarisasi Dokumen
                </label>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 12px;">
                    <div>
                        <label style="font-size: 11.5px; font-weight: 600; display: block; margin-bottom: 5px;">Jenis Dokumen (Pilih yang relevan):</label>
                        <div style="background: #fff; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 11.5px;">
                            <label style="display: block; margin-bottom: 4px; cursor: pointer;"><input type="checkbox" name="doc_types[]" value="Layout operator"> Layout operator</label>
                            <label style="display: block; margin-bottom: 4px; cursor: pointer;"><input type="checkbox" name="doc_types[]" value="Flow Chart Proses"> Flow Chart Proses</label>
                            <label style="display: block; margin-bottom: 4px; cursor: pointer;"><input type="checkbox" name="doc_types[]" value="Pengukuran / Gages"> Pengukuran / Gages</label>
                            <label style="display: block; margin-bottom: 4px; cursor: pointer;"><input type="checkbox" name="doc_types[]" value="FMEA (Design/Process)"> FMEA (Design/Process)</label>
                            <label style="display: block; margin-bottom: 4px; cursor: pointer;"><input type="checkbox" name="doc_types[]" value="Spesifikasi Produk"> Spesifikasi Produk</label>
                        </div>
                    </div>
                    <div>
                        <div class="form-group">
                            <label style="font-size: 11.5px;">Deskripsi Dokumen / Perubahan</label>
                            <textarea name="deskripsi_dokumen" class="form-control" rows="3" placeholder="Contoh: Revisi SOP setting parameter mesin & update FMEA..."></textarea>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                            <div class="form-group">
                                <label style="font-size: 11px;">Tanggal Plan</label>
                                <input type="date" name="doc_plan_date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="form-group">
                                <label style="font-size: 11px;">Tanggal Aktual</label>
                                <input type="date" name="doc_act_date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="background: #fafafa; border: 1px solid #dcdcdc; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                <label style="font-weight: bold; font-size: 13px; color: #735d25; display: block; margin-bottom: 8px;">
                    11. Control Chart (Pilih tools control chart yang sesuai)
                </label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 11.5px; background: #fff; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    <div>
                        <strong style="color: #444; display: block; margin-bottom: 4px;">Data Variable:</strong>
                        <label style="display: block; margin-bottom: 3px; cursor: pointer;"><input type="radio" name="control_chart" value="IMR chart"> IMR chart</label>
                        <label style="display: block; margin-bottom: 3px; cursor: pointer;"><input type="radio" name="control_chart" value="Xbar R chart"> Xbar R chart</label>
                        <label style="display: block; cursor: pointer;"><input type="radio" name="control_chart" value="Xbar S chart"> Xbar S chart</label>
                    </div>
                    <div>
                        <strong style="color: #444; display: block; margin-bottom: 4px;">Data Attribute:</strong>
                        <label style="display: block; margin-bottom: 3px; cursor: pointer;"><input type="radio" name="control_chart" value="c chart"> c chart</label>
                        <label style="display: block; margin-bottom: 3px; cursor: pointer;"><input type="radio" name="control_chart" value="u chart"> u chart</label>
                        <label style="display: block; margin-bottom: 3px; cursor: pointer;"><input type="radio" name="control_chart" value="np chart"> np chart</label>
                        <label style="display: block; cursor: pointer;"><input type="radio" name="control_chart" value="p chart"> p chart</label>
                    </div>
                </div>
            </div>

            <div style="background: #fafafa; border: 1px solid #dcdcdc; border-radius: 8px; padding: 15px; margin-bottom: 15px;">
                <label style="font-weight: bold; font-size: 13px; color: #735d25; display: block; margin-bottom: 8px;">
                    12. Perayaan Tim (Team Celebration)
                </label>
                <div class="form-group" style="margin-bottom: 12px;">
                    <input type="text" name="team_celebration" class="form-control" value="" placeholder="Catatan perayaan tim / sosialisasi tim...">
                </div>

                <hr style="border: none; border-top: 1px solid #ddd; margin: 15px 0;">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div class="form-group">
                        <label for="pic_supplier">Nama PIC / Penanggung Jawab Supplier</label>
                        <input type="text" name="pic_supplier" class="form-control" placeholder="Masukkan nama PIC..." value="{{ $capa->pic_supplier ?? '' }}" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_implementasi">Rencana / Tanggal Implementasi Perbaikan</label>
                        <input type="date" name="tanggal_implementasi" class="form-control" value="{{ $capa->tanggal_implementasi ?? date('Y-m-d') }}" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="btn-group">
            <button type="button" class="btn btn-prev" id="btnPrev" onclick="changeStep(-1)" disabled>Kembali</button>
            <button type="button" class="btn btn-next" id="btnNext" onclick="changeStep(1)">Lanjut</button>
            <button type="submit" class="btn btn-submit" id="btnSubmit" formnovalidate>Submit CAPA ke QC</button>
        </div>
    </form>
</div>

<script>
    let currentStep = 1;
    const totalSteps = 5;

    function changeStep(stepChange) {
        document.getElementById(`step-${currentStep}`).classList.remove('active');
        document.getElementById(`node-${currentStep}`).classList.remove('active');
        if(stepChange > 0) document.getElementById(`node-${currentStep}`).classList.add('completed');

        currentStep += stepChange;

        document.getElementById(`step-${currentStep}`).classList.add('active');
        document.getElementById(`node-${currentStep}`).classList.add('active');

        document.getElementById('btnPrev').disabled = (currentStep === 1);
        if (currentStep === totalSteps) {
            document.getElementById('btnNext').style.display = 'none';
            document.getElementById('btnSubmit').style.display = 'block';
        } else {
            document.getElementById('btnNext').style.display = 'block';
            document.getElementById('btnSubmit').style.display = 'none';
        }
    }

    function toggleDetailInput(inputId, checkbox) {
        const textInput = document.getElementById(inputId);
        if (checkbox.checked) {
            textInput.style.display = 'block';
            textInput.required = true;
        } else {
            textInput.style.display = 'none';
            textInput.required = false;
            textInput.value = '';
        }
    }

    let whyCategoryIndex = 1;

    function addWhyCategoryBox() {
        const container = document.getElementById('why-boxes-container');
        const boxHtml = `
            <div class="why-card-box" id="why-box-${whyCategoryIndex}" style="background: #fafafa; border: 1px solid #dcdcdc; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <label style="font-weight: bold; font-size: 13px; color: #735d25;">
                        <i class="fas fa-list-check"></i> Kategori Faktor Ke-${whyCategoryIndex + 1}:
                    </label>
                    <button type="button" onclick="removeWhyCategoryBox('why-box-${whyCategoryIndex}')" style="background: #dc3545; color: #fff; border: none; padding: 4px 10px; border-radius: 4px; font-size: 11px; cursor: pointer;">
                        <i class="fas fa-trash"></i> Hapus Faktor Ini
                    </button>
                </div>

                <div class="form-group">
                    <select name="why_analysis[${whyCategoryIndex}][kategori]" class="form-control" required style="font-weight: bold; background: #fff;">
                        <option value="">-- Pilih Kategori Faktor (5M + 1E) --</option>
                        <option value="Manusia (Man)">👨‍🔧 Manusia (Man)</option>
                        <option value="Mesin (Machine)">⚙️ Mesin (Machine)</option>
                        <option value="Metode (Method)">📋 Metode (Method)</option>
                        <option value="Material">📦 Material</option>
                        <option value="Cetakan (Mold)">🧩 Cetakan (Mold)</option>
                        <option value="Lingkungan (Environment)">🌡️ Lingkungan (Environment)</option>
                    </select>
                </div>

                <div style="margin-top: 10px;">
                    <label style="font-size: 12px; font-weight: 600; color: #444; display: block; margin-bottom: 5px;">5. Alur Why-Why Analysis:</label>
                    <input type="text" name="why_analysis[${whyCategoryIndex}][w1]" class="form-control" placeholder="Why 1: Mengapa defect terjadi pada faktor ini?" style="margin-bottom: 6px;">
                    <input type="text" name="why_analysis[${whyCategoryIndex}][w2]" class="form-control" placeholder="Why 2: Mengapa hal di Why 1 terjadi?" style="margin-bottom: 6px;">
                    <input type="text" name="why_analysis[${whyCategoryIndex}][w3]" class="form-control" placeholder="Why 3: Mengapa hal di Why 2 terjadi?" style="margin-bottom: 6px;">
                    <input type="text" name="why_analysis[${whyCategoryIndex}][w4]" class="form-control" placeholder="Why 4: Mengapa hal di Why 3 terjadi?" style="margin-bottom: 6px;">
                    <input type="text" name="why_analysis[${whyCategoryIndex}][w5]" class="form-control" placeholder="Why 5: Akar masalah mendasar untuk faktor ini" style="margin-bottom: 6px;">
                </div>

                <div style="margin-top: 15px; background: #fff; padding: 12px; border: 1px dashed #bbb; border-radius: 6px;">
                    <label style="font-size: 12px; font-weight: bold; color: #735d25; display: block; margin-bottom: 8px;">
                        6. Apa yang seharusnya terjadi VS apa yang sebenarnya terjadi?
                    </label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 6px;">
                        <div>
                            <label style="font-size: 11px; font-weight: 600;">Kondisi Standard (Seharusnya)</label>
                            <input type="text" name="why_analysis[${whyCategoryIndex}][kondisi_std]" class="form-control" placeholder="Contoh: Parameter suhu 150°C" value="">
                        </div>
                        <div>
                            <label style="font-size: 11px; font-weight: 600;">Kondisi Actual (Sebenarnya)</label>
                            <input type="text" name="why_analysis[${whyCategoryIndex}][kondisi_act]" class="form-control" placeholder="Contoh: Parameter suhu drop 120°C" value="">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 8px;">
                        <div>
                            <label style="font-size: 11px; font-weight: 600;">Verifikasi (Analisa Tools / Alat)</label>
                            <input type="text" name="why_analysis[${whyCategoryIndex}][verifikasi]" class="form-control" placeholder="Contoh: Visual Check / Thermo Sensor" value="">
                        </div>
                        <div>
                            <label style="font-size: 11px; font-weight: 600;">Kesimpulan</label>
                            <select name="why_analysis[${whyCategoryIndex}][kesimpulan]" class="form-control">
                                <option value="Ya">Ya (Penyebab)</option>
                                <option value="Tdk">Tidak</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', boxHtml);
        whyCategoryIndex++;
    }

    function removeWhyCategoryBox(boxId) {
        const boxElement = document.getElementById(boxId);
        if (boxElement) {
            boxElement.remove();
        }
    }
</script>

</body>
</html>