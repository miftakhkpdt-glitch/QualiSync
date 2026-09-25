@extends('layouts.staff-layout')

@section('konten')
<div style="padding: 30px; background-color: #f8fafc; min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-size: 24px; font-weight: bold; color: #1e293b; margin: 0 0 5px 0;">
                <i class="fas fa-file-invoice-dollar" style="color: #10b981; margin-right: 8px;"></i> Manajemen Penggajian (Payroll)
            </h2>
            <p style="color: #64748b; font-size: 14px; margin: 0;">Buat dan kelola slip gaji karyawan setiap bulan.</p>
        </div>
        
        <!-- DUA TOMBOL AKSI -->
        <div style="display: flex; gap: 10px;">
            <button onclick="bukaModalRekap()" style="background-color: #3b82f6; color: white; padding: 10px 18px; border: none; border-radius: 6px; font-size: 14px; font-weight: bold; cursor: pointer; transition: 0.3s; box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);">
                <i class="fas fa-paper-plane" style="margin-right: 5px;"></i> Posting Total ke FAT
            </button>
            <button onclick="bukaModalGaji()" style="background-color: #10b981; color: white; padding: 10px 18px; border: none; border-radius: 6px; font-size: 14px; font-weight: bold; cursor: pointer; transition: 0.3s; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);" onmouseover="this.style.backgroundColor='#059669'" onmouseout="this.style.backgroundColor='#10b981'">
                <i class="fas fa-plus" style="margin-right: 5px;"></i> Buat Slip Gaji Baru
            </button>
        </div>
    </div>
    <!-- ================= PENGINGAT (REMINDER) ================= -->
    <div style="background-color: #fffbeb; border-left: 5px solid #f59e0b; padding: 15px 20px; border-radius: 8px; margin-bottom: 25px; display: flex; align-items: flex-start; gap: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border-right: 1px solid #fde68a; border-top: 1px solid #fde68a; border-bottom: 1px solid #fde68a;">
        <i class="fas fa-exclamation-triangle" style="color: #d97706; font-size: 24px; margin-top: 2px;"></i>
        <div>
            <h4 style="margin: 0 0 5px 0; color: #b45309; font-size: 14px; font-weight: bold;">PENTING: Jangan Lupa Posting ke Keuangan (FAT)!</h4>
            <p style="margin: 0; color: #92400e; font-size: 13px; line-height: 1.5;">
                Setelah Anda menekan tombol <b>"Bayar"</b> untuk semua karyawan bulan ini, Anda <b>WAJIB</b> menekan tombol biru <b style="color: #1d4ed8;">"Posting Total ke FAT"</b> di atas. Jika tidak di-posting, pengeluaran gaji tidak akan tercatat di Laporan Keuangan perusahaan.
            </p>
        </div>
    </div>

    <!-- Tabel Data -->
    <div style="background: white; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden; border: 1px solid #e2e8f0;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background-color: #1e293b; color: white;">
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: bold;">Karyawan</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: bold;">Periode</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: bold;">Gaji Bersih</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: bold; text-align: center;">Status</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: bold; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payrolls as $pay)
                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='transparent'">
                    <td style="padding: 15px 20px;">
                        <div style="font-weight: bold; color: #1e293b; font-size: 14px;">{{ $pay->nama_karyawan }}</div>
                        <div style="font-size: 12px; color: #64748b; text-transform: uppercase;">{{ $pay->role }}</div>
                    </td>
                    <td style="padding: 15px 20px; font-size: 14px; font-weight: bold; color: #0f172a;">{{ $pay->periode }}</td>
                    <td style="padding: 15px 20px; font-size: 14px; font-weight: bold; color: #10b981;">Rp {{ number_format($pay->gaji_bersih, 0, ',', '.') }}</td>
                    <td style="padding: 15px 20px; text-align: center;">
                        @if($pay->status == 'Pending')
                            <span style="background: #fef3c7; color: #d97706; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold;">Pending</span>
                        @else
                            <span style="background: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold;">Dibayar ({{ \Carbon\Carbon::parse($pay->tanggal_dibayar)->format('d/m/Y') }})</span>
                        @endif
                    </td>
                    <td style="padding: 15px 20px; text-align: center;">
                        @if($pay->status == 'Pending')
                            <button onclick="bayarGaji({{ $pay->id }})" style="background: #3b82f6; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: bold;" title="Proses Pembayaran" onmouseover="this.style.backgroundColor='#2563eb'" onmouseout="this.style.backgroundColor='#3b82f6'">
                                <i class="fas fa-money-bill-wave"></i> Bayar
                            </button>
                            <form id="pay-form-{{ $pay->id }}" action="{{ route('hrd.payrolls.pay', $pay->id) }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @else
                            <button style="background: #94a3b8; color: white; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: not-allowed;" title="Sudah Lunas">
                                <i class="fas fa-check-double"></i> Selesai
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="padding: 20px; text-align: center; color: #94a3b8;">Belum ada data slip gaji.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODAL POSTING REKAP ================= -->
<div id="modalRekap" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center;">
    <div style="background-color: #fff; width: 450px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); overflow: hidden;">
        <div style="background-color: #3b82f6; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px;"><i class="fas fa-paper-plane"></i> Posting Rekap Gaji ke FAT</h3>
            <span onclick="tutupModalRekap()" style="cursor: pointer; font-size: 20px; font-weight: bold;">&times;</span>
        </div>
        
        <form action="{{ route('hrd.payrolls.post_rekap') }}" method="POST" style="padding: 20px;">
            @csrf
            <div style="margin-bottom: 20px;">
                <label style="font-size: 12px; font-weight: bold; color: #64748b;">Periode yang akan di-posting *</label>
                <input type="text" name="periode" placeholder="Cth: Agustus 2026" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 5px;">
                <p style="font-size: 12px; color: #64748b; margin-top: 10px; line-height: 1.5;">
                    <i class="fas fa-info-circle" style="color: #3b82f6;"></i> Sistem akan menjumlahkan seluruh gaji berstatus "Dibayar" pada periode ini dan mengirimkannya sebagai <b>1 baris Jurnal</b> secara anonim ke FAT.
                </p>
            </div>
            <div style="text-align: right;">
                <button type="button" onclick="tutupModalRekap()" style="background: #94a3b8; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; margin-right: 10px;">Batal</button>
                <button type="submit" style="background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: bold;">Kirim ke FAT</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL BUAT SLIP GAJI ================= -->
<div id="modalGaji" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center;">
    <div style="background-color: #fff; width: 550px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); overflow: hidden;">
        <div style="background-color: #1e293b; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px;"><i class="fas fa-file-invoice-dollar"></i> Buat Slip Gaji Baru</h3>
            <span onclick="tutupModalGaji()" style="cursor: pointer; font-size: 20px; font-weight: bold;">&times;</span>
        </div>
        
        <form action="{{ route('hrd.payrolls.store') }}" method="POST" style="padding: 20px; max-height: 80vh; overflow-y: auto;">
            @csrf
            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                <div style="flex: 2;">
                    <label style="font-size: 12px; font-weight: bold; color: #64748b;">Pilih Karyawan *</label>
                    <select name="user_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 5px;">
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($karyawan as $k)
                            <option value="{{ $k->id }}">{{ $k->name }} ({{ strtoupper($k->role) }})</option>
                        @endforeach
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold; color: #64748b;">Periode *</label>
                    <input type="text" name="periode" placeholder="Cth: Agustus 2026" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 5px;">
                </div>
            </div>

            <div style="border-top: 1px dashed #e2e8f0; margin: 20px 0;"></div>

            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold; color: #64748b;">Gaji Pokok (Rp) *</label>
                    <input type="number" name="gaji_pokok" required placeholder="0" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 5px;">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold; color: #64748b;">Tunjangan (Rp)</label>
                    <input type="number" name="tunjangan" placeholder="0" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 5px;">
                </div>
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 25px;">
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold; color: #64748b;">Uang Lembur (Rp)</label>
                    <input type="number" name="uang_lembur" placeholder="0" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 5px;">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold; color: #ef4444;">Potongan (Rp)</label>
                    <input type="number" name="potongan" placeholder="0" style="width: 100%; padding: 10px; border: 1px solid #fca5a5; border-radius: 6px; margin-top: 5px;">
                </div>
            </div>

            <div style="text-align: right; background: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0;">
                <button type="button" onclick="tutupModalGaji()" style="background: #94a3b8; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; margin-right: 10px;">Batal</button>
                <button type="submit" style="background: #10b981; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: bold;">Simpan Draft Gaji</button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalGaji() { document.getElementById('modalGaji').style.display = 'flex'; }
    function tutupModalGaji() { document.getElementById('modalGaji').style.display = 'none'; }
    
    function bukaModalRekap() { document.getElementById('modalRekap').style.display = 'flex'; }
    function tutupModalRekap() { document.getElementById('modalRekap').style.display = 'none'; }

    function bayarGaji(id) {
        Swal.fire({
            title: 'Bayar Gaji Ini?',
            text: "Status slip gaji akan menjadi Dibayar (Data tersimpan aman di HRD).",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Setujui Pembayaran',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('pay-form-' + id).submit();
            }
        });
    }
</script>
@endsection