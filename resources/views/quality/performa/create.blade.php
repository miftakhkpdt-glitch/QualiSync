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
                            <td style="border: 1px solid #cbd5e1; padding: 10px; font-weight: bold; text-align: left; background: #f8fafc;">Efek thd internal proses / Masalah Kritis</td>
                            <td style="border: 1px solid #cbd5e1; padding: 10px;">Tidak ada atau maksimum <b>1 kasus</b> terjadi (stop line, sortir, kurang barang).</td>
                            <td style="border: 1px solid #cbd5e1; padding: 10px;"><b>2 - 3 kasus</b> terjadi (stop line, sortir, kurang barang untuk produksi).</td>
                            <td style="border: 1px solid #cbd5e1; padding: 10px;"><b>4 - 5 kasus</b> terjadi (stop line, sortir, kurang barang untuk produksi).</td>
                            <td style="border: 1px solid #cbd5e1; padding: 10px;"><b>6 kasus atau lebih</b> terjadi (stop line, sortir, kurang barang untuk produksi).</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
</div>
@endsection