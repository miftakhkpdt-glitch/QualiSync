@extends('layouts.staff-layout')

@section('title', 'Penilaian Performa Supplier')

@section('konten')
<div style="max-width: 1000px; margin: 0 auto; padding: 20px;">
    
    <div style="margin-bottom: 20px;">
        <h2 style="color: #1e293b; margin: 0;"><i class="fas fa-star-half-alt" style="color: #f59e0b;"></i> Form Penilaian Kinerja Supplier</h2>
        <p style="color: #64748b; font-size: 14px;">Masukkan skor perhitungan manual (Kuantitatif) dan pilih peringkat pengamatan (Kualitatif). Sistem akan otomatis menghitung hasil akhirnya.</p>
    </div>

    <!-- Tombol Kembali ke Halaman Utama -->
    <div style="margin-bottom: 20px;">
        <a href="{{ route('quality.supplier_performance.index') }}" style="background: #64748b; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold; display: inline-block;">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar & Cetak
        </a>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-weight: bold;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ url('/quality/supplier-performance/store') }}" method="POST">
        @csrf
        
        <!-- INFORMASI DASAR -->
        <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 20px; border-top: 4px solid #0ea5e9;">
            <h4 style="margin-top: 0; color: #334155; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">Informasi Periode</h4>
            <div style="display: flex; gap: 15px;">
                <div style="flex: 2;">
                    <label style="display: block; font-weight: bold; font-size: 13px; margin-bottom: 5px;">Pilih Supplier</label>
                    <select name="supplier_id" required class="form-control" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #cbd5e1;">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($vendors as $v)
                            <option value="{{ $v->id }}">{{ $v->vendor_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-weight: bold; font-size: 13px; margin-bottom: 5px;">Bulan Penilaian</label>
                    <select name="periode_bulan" required class="form-control" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #cbd5e1;">
                        <option value="">-- Pilih Bulan --</option>
                        <option value="Januari">Januari</option>
                        <option value="Februari">Februari</option>
                        <option value="Maret">Maret</option>
                        <option value="April">April</option>
                        <option value="Mei">Mei</option>
                        <option value="Juni">Juni</option>
                        <option value="Juli">Juli</option>
                        <option value="Agustus">Agustus</option>
                        <option value="September">September</option>
                        <option value="Oktober">Oktober</option>
                        <option value="November">November</option>
                        <option value="Desember">Desember</option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-weight: bold; font-size: 13px; margin-bottom: 5px;">Tahun</label>
                    <input type="number" name="periode_tahun" value="{{ date('Y') }}" required class="form-control" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #cbd5e1;">
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 20px;">
            <!-- BAGIAN KUANTITATIF -->
            <div style="flex: 1; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-top: 4px solid #10b981;">
                <h4 style="margin-top: 0; color: #334155; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">Bagian 1: Kuantitatif (Masukkan Skor %)</h4>
                
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 13px;">Total perbandingan item diterima (Bobot 30%)</label>
                    <input type="number" step="0.01" name="q_item_diterima" required placeholder="Contoh: 97.7" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 13px;">SCAR (Sesuai target) (Bobot 20%)</label>
                    <input type="number" step="0.01" name="q_scar" required placeholder="0 - 100" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 13px;">Kesesuaian jumlah qty (Bobot 10%)</label>
                    <input type="number" step="0.01" name="q_qty" required placeholder="0 - 100" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 13px;">Kesesuaian jenis item (Bobot 10%)</label>
                    <input type="number" step="0.01" name="q_jenis" required placeholder="0 - 100" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 13px;">Kelengkapan CoA (Bobot 20%)</label>
                    <input type="number" step="0.01" name="q_coa" required placeholder="0 - 100" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 13px;">Kualitas kemasan (Bobot 10%)</label>
                    <input type="number" step="0.01" name="q_packaging" required placeholder="0 - 100" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div style="margin-bottom: 15px; background: #f8fafc; padding: 10px; border-radius: 6px;">
                    <label style="font-size: 13px; font-weight: bold; color: #0284c7;">Pengiriman tepat waktu (Bobot 100%)</label>
                    <input type="number" step="0.01" name="q_pengiriman" required placeholder="0 - 100" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                </div>
            </div>

            <!-- BAGIAN KUALITATIF -->
            <div style="flex: 1; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-top: 4px solid #f59e0b;">
                <h4 style="margin-top: 0; color: #334155; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">Bagian 2: Kualitatif (Pilih Peringkat)</h4>
                
                <div style="margin-bottom: 20px;">
                    <label style="font-size: 13px; font-weight: bold;">Manajemen & Cepat Tanggap</label>
                    <select name="k_manajemen" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                        <option value="">-- Pilih Peringkat --</option>
                        <option value="A">A - Tanggapan 1 hari</option>
                        <option value="B">B - Tanggapan 2-3 hari</option>
                        <option value="C">C - Tanggapan 3-4 hari</option>
                        <option value="D">D - Tanggapan > 4 hari</option>
                    </select>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="font-size: 13px; font-weight: bold;">Kompetensi & Kemampuan Teknis</label>
                    <select name="k_kompetensi" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                        <option value="">-- Pilih Peringkat --</option>
                        <option value="A">A - Sangat baik (1-2 hari perbaikan)</option>
                        <option value="B">B - Baik (2-3 hari perbaikan)</option>
                        <option value="C">C - Cukup (3-5 hari perbaikan)</option>
                        <option value="D">D - Kurang (> 6 hari perbaikan)</option>
                    </select>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="font-size: 13px; font-weight: bold;">Efek Terhadap Internal Proses</label>
                    <select name="k_efek_internal" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                        <option value="">-- Pilih Peringkat --</option>
                        <option value="A">A - (0 - 1 Kasus terjadi)</option>
                        <option value="B">B - (2 - 3 Kasus terjadi)</option>
                        <option value="C">C - (4 - 5 Kasus terjadi)</option>
                        <option value="D">D - (>= 6 Kasus terjadi)</option>
                    </select>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="font-size: 13px; font-weight: bold;">Tingkat Kritis Masalah Incoming</label>
                    <select name="k_masalah_kritis" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; margin-top: 5px;">
                        <option value="">-- Pilih Peringkat --</option>
                        <option value="A">A - (0 - 1 Kasus Kritis)</option>
                        <option value="B">B - (2 - 3 Kasus Kritis)</option>
                        <option value="C">C - (4 - 5 Kasus Kritis)</option>
                        <option value="D">D - (>= 6 Kasus Kritis)</option>
                    </select>
                </div>

                <div style="margin-top: 40px;">
                    <button type="submit" style="width: 100%; background: #3b82f6; color: white; padding: 12px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 15px;">
                        <i class="fas fa-save"></i> Simpan & Hitung Otomatis
                    </button>
                </div>
            </div>
        </div>
    </form>

    <div style="margin-top: 30px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-top: 4px solid #6366f1;">
        <h3 style="margin: 0 0 8px; color: #1e293b;"><i class="fas fa-calculator" style="color: #6366f1;"></i> Rumus Perhitungan Penilaian Supplier</h3>
        <p style="margin: 0 0 20px; color: #64748b; font-size: 13px;">Gunakan rumus berikut untuk mendapatkan skor persentase yang dimasukkan pada bagian kuantitatif. Sistem kemudian mengonversi skor tersebut ke peringkat dan menghitung bobot secara otomatis.</p>

        <h4 style="margin: 0 0 10px; color: #334155;">Kuantitatif</h4>
        <div style="overflow-x: auto; margin-bottom: 20px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
                <thead>
                    <tr style="background: #f8fafc;">
                        <th style="border: 1px solid #cbd5e1; padding: 10px;">Kriteria</th>
                        <th style="border: 1px solid #cbd5e1; padding: 10px; text-align: center;">Bobot Kualitas</th>
                        <th style="border: 1px solid #cbd5e1; padding: 10px;">Rumus Skor (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 10px;">Total perbandingan item diterima</td>
                        <td style="border: 1px solid #cbd5e1; padding: 10px; text-align: center;">30%</td>
                        <td style="border: 1px solid #cbd5e1; padding: 10px;">(Jumlah pengiriman per item berstatus OK/accept ÷ jumlah total pengiriman) × 100%</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 10px;">SCAR sesuai target</td>
                        <td style="border: 1px solid #cbd5e1; padding: 10px; text-align: center;">20%</td>
                        <td style="border: 1px solid #cbd5e1; padding: 10px;">(Jumlah SCAR yang dibalas sesuai target ÷ jumlah SCAR yang dikirim ke supplier) × 100%. Target: tanggapan pertama maksimal 1 × 24 jam dan SCAR ditutup maksimal 5 hari; selebihnya dianggap missed.</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 10px;">Kesesuaian jumlah qty</td>
                        <td style="border: 1px solid #cbd5e1; padding: 10px; text-align: center;">10%</td>
                        <td style="border: 1px solid #cbd5e1; padding: 10px;">(Jumlah pengiriman dengan qty sesuai ÷ jumlah total pengiriman) × 100%</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 10px;">Kesesuaian jenis item</td>
                        <td style="border: 1px solid #cbd5e1; padding: 10px; text-align: center;">10%</td>
                        <td style="border: 1px solid #cbd5e1; padding: 10px;">(Jumlah pengiriman dengan jenis item sesuai ÷ jumlah total pengiriman) × 100%</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 10px;">Kelengkapan CoA</td>
                        <td style="border: 1px solid #cbd5e1; padding: 10px; text-align: center;">20%</td>
                        <td style="border: 1px solid #cbd5e1; padding: 10px;">(Jumlah pengiriman dengan CoA lengkap ÷ jumlah total pengiriman) × 100%</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 10px;">Kualitas kemasan/packaging</td>
                        <td style="border: 1px solid #cbd5e1; padding: 10px; text-align: center;">10%</td>
                        <td style="border: 1px solid #cbd5e1; padding: 10px;">(Jumlah pengiriman dengan kondisi kemasan baik ÷ jumlah total pengiriman) × 100%</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 10px;">Pengiriman tepat waktu</td>
                        <td style="border: 1px solid #cbd5e1; padding: 10px; text-align: center;">100% (aspek pengiriman)</td>
                        <td style="border: 1px solid #cbd5e1; padding: 10px;">(Jumlah pengiriman tepat waktu sesuai PO ÷ jumlah total pengiriman) × 100%. Sumber data: WH/PPIC.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="background: #f8fafc; padding: 12px 15px; border-radius: 6px; margin-bottom: 24px; color: #334155; font-size: 13px; line-height: 1.7;">
            <strong>Konversi bobot:</strong> peringkat A = 100% bobot, B = 75% bobot, C = 50% bobot, dan D = 25% bobot (lihat Tabel 1).<br>
            <strong>Total Kualitas</strong> = jumlah skor berbobot dari 6 kriteria kualitas di atas.<br>
            <strong>Skor Kuantitatif</strong> = (70% × Total Kualitas) + (30% × skor Pengiriman tepat waktu).
        </div>

        <h4 style="margin: 0 0 10px; color: #334155;">Kualitatif</h4>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
                <thead>
                    <tr style="background: #f8fafc;">
                        <th style="border: 1px solid #cbd5e1; padding: 10px;">Kriteria</th>
                        <th style="border: 1px solid #cbd5e1; padding: 10px; text-align: center;">Bobot</th>
                        <th style="border: 1px solid #cbd5e1; padding: 10px;">Dasar Penilaian</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 10px;">Manajemen & cepat tanggap</td>
                        <td style="border: 1px solid #cbd5e1; padding: 10px; text-align: center;">25%</td>
                        <td rowspan="4" style="border: 1px solid #cbd5e1; padding: 10px;">Pilih peringkat berdasarkan pengalaman, komunikasi, dan praktik sehari-hari terkait kualitas dan pengiriman supplier. Gunakan rincian kriteria pada Tabel 2.</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 10px;">Kompetensi & kemampuan teknis</td>
                        <td style="border: 1px solid #cbd5e1; padding: 10px; text-align: center;">25%</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 10px;">Efek terhadap internal proses</td>
                        <td style="border: 1px solid #cbd5e1; padding: 10px; text-align: center;">25%</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 10px;">Tingkat kritis masalah incoming</td>
                        <td style="border: 1px solid #cbd5e1; padding: 10px; text-align: center;">25%</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="background: #fffbeb; padding: 12px 15px; border-radius: 6px; margin-top: 12px; color: #78350f; font-size: 13px; line-height: 1.7;">
            <strong>Skor Kualitatif</strong> = jumlah skor berbobot 4 kriteria. Untuk setiap kriteria, bobot 25 dikalikan nilai peringkat: A = 100% (25 poin), B = 75% (18,75 poin), C = 50% (12,5 poin), atau D = 25% (6,25 poin). Status akhir mengikuti konversi pada Tabel 1.
        </div>
    </div>

    <!-- ============================================== -->
    <!-- TABEL PANDUAN REFERENSI (Sesuai Gambar Excel) -->
    <!-- ============================================== -->
    
    <div style="display: flex; gap: 20px; margin-top: 30px;">
        
        <!-- [BARU] TABEL 1: KONVERSI NILAI -->
        <div style="flex: 1; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <h4 style="margin: 0 0 15px 0; color: #1e293b;"><i class="fas fa-percentage" style="color: #0ea5e9;"></i> Tabel 1. Konversi Peringkat</h4>
            <table style="width: 100%; border-collapse: collapse; font-size: 12px; text-align: center;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #cbd5e1; padding: 10px; background: #f8fafc;">Rentang Nilai</th>
                        <th style="border: 1px solid #cbd5e1; padding: 10px; background: #f8fafc;">Peringkat</th>
                        <th style="border: 1px solid #cbd5e1; padding: 10px; background: #f8fafc;">Status Supplier</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 8px;">91-100%</td>
                        <td style="border: 1px solid #cbd5e1; padding: 8px; font-weight: bold;">A</td>
                        <td style="border: 1px solid #cbd5e1; padding: 8px; background: #16a34a; color: white; font-weight: bold;">EXCELLENCE</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 8px;">80-90%</td>
                        <td style="border: 1px solid #cbd5e1; padding: 8px; font-weight: bold;">B</td>
                        <td style="border: 1px solid #cbd5e1; padding: 8px; background: #0ea5e9; color: white; font-weight: bold;">AVERAGE</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 8px;">61-79%</td>
                        <td style="border: 1px solid #cbd5e1; padding: 8px; font-weight: bold;">C</td>
                        <td style="border: 1px solid #cbd5e1; padding: 8px; background: #eab308; color: white; font-weight: bold;">NEED IMPROVEMENT</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 8px;"><= 60%</td>
                        <td style="border: 1px solid #cbd5e1; padding: 8px; font-weight: bold;">D</td>
                        <td style="border: 1px solid #cbd5e1; padding: 8px; background: #dc2626; color: white; font-weight: bold;">POOR</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- TABEL 2: KONVERSI KUALITATIF (YANG SUDAH ADA SEBELUMNYA) -->
        <div style="flex: 2; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <h4 style="margin: 0 0 15px 0; color: #1e293b;"><i class="fas fa-info-circle" style="color: #0ea5e9;"></i> Tabel 2. Konversi Tingkat Penilaian Kualitatif</h4>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 12px; text-align: center;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #cbd5e1; padding: 10px; background: #f8fafc; width: 15%;">Kriteria</th>
                            <th style="border: 1px solid #cbd5e1; padding: 10px; background: #16a34a; color: white; width: 21%;">A (EXCELLENCE)</th>
                            <th style="border: 1px solid #cbd5e1; padding: 10px; background: #0ea5e9; color: white; width: 21%;">B (AVERAGE)</th>
                            <th style="border: 1px solid #cbd5e1; padding: 10px; background: #eab308; color: white; width: 21%;">C (NEED IMPROVEMENT)</th>
                            <th style="border: 1px solid #cbd5e1; padding: 10px; background: #dc2626; color: white; width: 21%;">D (POOR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border: 1px solid #cbd5e1; padding: 10px; font-weight: bold; text-align: left; background: #f8fafc;">Manajemen dan Cepat tanggap</td>
                            <td style="border: 1px solid #cbd5e1; padding: 10px;">Tanggapan & tindakan dlm <b>1 hari</b>.<br><br>Reject: Penarikan & ganti lot <b>1-2 hari</b>.</td>
                            <td style="border: 1px solid #cbd5e1; padding: 10px;">Tanggapan & tindakan dlm <b>2-3 hari</b>.<br><br>Reject: Penarikan & ganti lot <b>2-3 hari</b>.</td>
                            <td style="border: 1px solid #cbd5e1; padding: 10px;">Tanggapan & tindakan dlm <b>3-4 hari</b>.<br><br>Reject: Penarikan & ganti lot <b>3-4 hari</b>.</td>
                            <td style="border: 1px solid #cbd5e1; padding: 10px;">Tanggapan & tindakan dlm <b>>4 hari</b>.<br><br>Reject: Penarikan & ganti lot <b>>4 hari</b>.</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #cbd5e1; padding: 10px; font-weight: bold; text-align: left; background: #f8fafc;">Kompetensi Teknis</td>
                            <td style="border: 1px solid #cbd5e1; padding: 10px;">Kompetensi <b>Sangat Baik</b>. Akar masalah & perbaikan <b>1-2 hari</b> setelah claim.</td>
                            <td style="border: 1px solid #cbd5e1; padding: 10px;">Kompetensi <b>Baik</b>. Akar masalah & perbaikan <b>2-3 hari</b> setelah claim.</td>
                            <td style="border: 1px solid #cbd5e1; padding: 10px;">Kompetensi <b>Cukup</b>. Akar masalah & perbaikan <b>3-5 hari</b> setelah claim.</td>
                            <td style="border: 1px solid #cbd5e1; padding: 10px;">Kompetensi <b>Sangat Kurang</b>. Akar masalah & perbaikan <b>>=6 hari</b> setelah claim.</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #cbd5e1; padding: 10px; font-weight: bold; text-align: left; background: #f8fafc;">Efek terhadap internal proses</td>
                            <td style="border: 1px solid #cbd5e1; padding: 10px;">Tidak ada atau maksimum <b>1 kasus</b> terjadi (stop line, sortir, kurang barang).</td>
                            <td style="border: 1px solid #cbd5e1; padding: 10px;"><b>2 - 3 kasus</b> terjadi (stop line, sortir, kurang barang untuk produksi).</td>
                            <td style="border: 1px solid #cbd5e1; padding: 10px;"><b>4 - 5 kasus</b> terjadi (stop line, sortir, kurang barang untuk produksi).</td>
                            <td style="border: 1px solid #cbd5e1; padding: 10px;"><b>6 kasus atau lebih</b> terjadi (stop line, sortir, kurang barang untuk produksi).</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #cbd5e1; padding: 10px; font-weight: bold; text-align: left; background: #f8fafc;">Tingkat kritis masalah incoming</td>
                            <td style="border: 1px solid #cbd5e1; padding: 10px;"><b>0 - 1 kasus kritis</b></td>
                            <td style="border: 1px solid #cbd5e1; padding: 10px;"><b>2 - 3 kasus kritis</b></td>
                            <td style="border: 1px solid #cbd5e1; padding: 10px;"><b>4 - 5 kasus kritis</b></td>
                            <td style="border: 1px solid #cbd5e1; padding: 10px;"><b>6 kasus kritis atau lebih</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p style="margin: 12px 0 0; color: #475569; font-size: 12px; line-height: 1.6;"><strong>Kategori masalah kritis:</strong> barang tercampur (mixed up), kontaminasi, masalah terkait safety, karakter printing kurang (miss printed), dan salah arah gulungan label (wrong label direction).</p>
        </div>
    </div>
    
</div>
@endsection