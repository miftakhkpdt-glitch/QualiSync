@extends('layouts.staff-layout')

@section('title', 'Revisi Forecast Customer - PT KIMPAI DYNA TUBE')

@section('konten')
<div style="padding: 20px; max-width: 900px; margin: 0 auto;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 style="font-size: 24px; color: #f59e0b; margin: 0;">
                <i class="fas fa-edit" style="color: #f59e0b;"></i> Revisi Forecast (v{{ $baseForecast->versi }})
            </h2>
            <p style="color: #64748b; font-size: 14px; margin-top: 5px;">Versi sebelumnya akan diarsipkan, dan data baru ini akan menjadi Versi {{ $baseForecast->versi + 1 }}.</p>
        </div>
        <a href="{{ route('sales.forecast.index') }}" style="background: #cbd5e1; color: #334155; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 13px;">
            <i class="fas fa-arrow-left"></i> Batal
        </a>
    </div>

    <!-- FORM INPUT -->
    <div style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-top: 4px solid #f59e0b;">
        <form action="{{ route('sales.forecast.storeRevisi', $baseForecast->id) }}" method="POST">
            @csrf
            
            <h3 style="font-size: 16px; color: #1e293b; margin-top: 0; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">Identitas Pelanggan (Tidak dapat diubah pada mode revisi)</h3>
            
            <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 13px; font-weight: bold; color: #475569; margin-bottom: 5px;">Nama Customer</label>
                    <input type="text" value="{{ $baseForecast->nama_customer }}" readonly style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; background: #f1f5f9; color: #64748b;">
                </div>
            </div>

            <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 13px; font-weight: bold; color: #475569; margin-bottom: 5px;">No. MM</label>
                    <input type="text" value="{{ $baseForecast->no_mm }}" readonly style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; background: #f1f5f9; color: #64748b;">
                </div>
                <div style="flex: 2;">
                    <label style="display: block; font-size: 13px; font-weight: bold; color: #475569; margin-bottom: 5px;">Nama Produk</label>
                    <input type="text" value="{{ $baseForecast->nama_produk }}" readonly style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; background: #f1f5f9; color: #64748b;">
                </div>
            </div>

            <h3 style="font-size: 16px; color: #1e293b; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">Catatan Perubahan <span style="color: red;">*</span></h3>
            <div style="margin-bottom: 30px;">
                <input type="text" name="alasan_revisi" required placeholder="Contoh: Customer minta revisi penurunan qty di bulan 3 karena demand turun" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px; border-left: 4px solid #f59e0b;">
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; margin-bottom: 15px; padding-bottom: 10px;">
                <h3 style="font-size: 16px; color: #1e293b; margin: 0;">Update Target (Ubah angka, tambah/hapus bulan)</h3>
                <button type="button" id="btn_tambah_baris" style="background: #3b82f6; color: white; border: none; padding: 6px 12px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 12px;">
                    <i class="fas fa-plus"></i> Tambah Bulan
                </button>
            </div>
            
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;" id="tabel_forecast">
                <thead>
                    <tr style="background: #f8fafc;">
                        <th style="padding: 10px; text-align: left; font-size: 13px; color: #475569;">Bulan <span style="color: red;">*</span></th>
                        <th style="padding: 10px; text-align: left; font-size: 13px; color: #475569;">Tahun <span style="color: red;">*</span></th>
                        <th style="padding: 10px; text-align: left; font-size: 13px; color: #475569;">Qty Target <span style="color: red;">*</span></th>
                        <th style="padding: 10px; text-align: center; font-size: 13px; color: #475569;">Hapus</th>
                    </tr>
                </thead>
                <tbody id="body_forecast">
                    <!-- Me-loop data versi sebelumnya agar user tinggal mengedit -->
                    @foreach($forecasts as $fc)
                    <tr>
                        <td style="padding: 8px;">
                            <select name="bulan[]" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px;">
                                @for($i=1; $i<=12; $i++)
                                    <option value="{{ $i }}" {{ $fc->bulan == $i ? 'selected' : '' }}>{{ $i }} - {{ date("F", mktime(0, 0, 0, $i, 1)) }}</option>
                                @endfor
                            </select>
                        </td>
                        <td style="padding: 8px;">
                            <input type="number" name="tahun[]" value="{{ $fc->tahun }}" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px;">
                        </td>
                        <td style="padding: 8px;">
                            <input type="number" name="qty_forecast[]" value="{{ floatval($fc->qty_forecast) }}" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px;">
                        </td>
                        <td style="padding: 8px; text-align: center;">
                            <button type="button" class="btn_hapus" style="background: #ef4444; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer;"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="text-align: right; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                <button type="submit" style="background: #f59e0b; color: white; border: none; padding: 12px 25px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 14px;">
                    <i class="fas fa-save"></i> Simpan Sebagai v{{ $baseForecast->versi + 1 }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnTambah = document.getElementById('btn_tambah_baris');
        const tbody = document.getElementById('body_forecast');

        btnTambah.addEventListener('click', function() {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="padding: 8px;">
                    <select name="bulan[]" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px;">
                        <option value="">-- Pilih Bulan --</option>
                        @for($i=1; $i<=12; $i++)
                            <option value="{{ $i }}">{{ $i }} - {{ date("F", mktime(0, 0, 0, $i, 1)) }}</option>
                        @endfor
                    </select>
                </td>
                <td style="padding: 8px;">
                    <input type="number" name="tahun[]" value="{{ date('Y') }}" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px;">
                </td>
                <td style="padding: 8px;">
                    <input type="number" name="qty_forecast[]" required placeholder="Contoh: 150000" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px;">
                </td>
                <td style="padding: 8px; text-align: center;">
                    <button type="button" class="btn_hapus" style="background: #ef4444; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer;"><i class="fas fa-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        tbody.addEventListener('click', function(e) {
            if(e.target.closest('.btn_hapus')) {
                if(tbody.children.length > 1) {
                    e.target.closest('tr').remove();
                } else {
                    alert('Minimal harus ada 1 bulan target.');
                }
            }
        });
    });
</script>
@endsection