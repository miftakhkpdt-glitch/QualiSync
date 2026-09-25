@extends('layouts.staff-layout')

@section('konten')
<div style="padding: 30px; background-color: #f8fafc; min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-size: 24px; font-weight: bold; color: #1e293b; margin: 0 0 5px 0;">
                <i class="fas fa-list-ol" style="color: #6366f1; margin-right: 8px;"></i> Master COA
            </h2>
            <p style="color: #64748b; font-size: 14px; margin: 0;">Kelola daftar bagan akun (Chart of Accounts) perusahaan.</p>
        </div>
        <button onclick="bukaModalTambah()" style="background-color: #10b981; color: white; padding: 10px 18px; border: none; border-radius: 6px; font-size: 14px; font-weight: bold; cursor: pointer; transition: 0.3s; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);" onmouseover="this.style.backgroundColor='#059669'" onmouseout="this.style.backgroundColor='#10b981'">
            <i class="fas fa-plus" style="margin-right: 5px;"></i> Tambah Akun
        </button>
    </div>

    <!-- Tabel Data -->
    <div style="background: white; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden; border: 1px solid #e2e8f0;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background-color: #1e293b; color: white;">
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: bold; width: 15%;">Kode Akun</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: bold;">Nama Akun</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: bold; width: 15%;">Kategori</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: bold; width: 15%;">Saldo Normal</th>
                    <th style="padding: 15px 20px; font-size: 13px; font-weight: bold; width: 15%; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coas as $coa)
                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='transparent'">
                    <td style="padding: 15px 20px; font-size: 14px; font-weight: bold; color: #3b82f6;">{{ $coa->kode_akun }}</td>
                    <td style="padding: 15px 20px; font-size: 14px; color: #0f172a; font-weight: bold;">{{ $coa->nama_akun }}</td>
                    <td style="padding: 15px 20px;">
                        <span style="background: #e2e8f0; color: #475569; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold;">{{ $coa->kategori }}</span>
                    </td>
                    <td style="padding: 15px 20px;">
                        @if($coa->saldo_normal == 'Debit')
                            <span style="color: #10b981; font-weight: bold; font-size: 13px;"><i class="fas fa-arrow-up" style="margin-right: 4px;"></i> DEBIT</span>
                        @else
                            <span style="color: #ef4444; font-weight: bold; font-size: 13px;"><i class="fas fa-arrow-down" style="margin-right: 4px;"></i> KREDIT</span>
                        @endif
                    </td>
                    <td style="padding: 15px 20px; text-align: center;">
                        <button onclick="bukaModalEdit({{ $coa->id }}, '{{ $coa->kode_akun }}', '{{ $coa->nama_akun }}', '{{ $coa->kategori }}', '{{ $coa->saldo_normal }}')" style="background: #fef3c7; color: #d97706; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; margin-right: 5px;" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="hapusCOA({{ $coa->id }})" style="background: #fee2e2; color: #dc2626; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer;" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                        
                        <!-- Form Hapus Tersembunyi -->
                        <form id="delete-form-{{ $coa->id }}" action="{{ route('fat.coas.destroy', $coa->id) }}" method="POST" style="display: none;">
                            @csrf @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="padding: 20px; text-align: center; color: #94a3b8;">Belum ada master COA terdaftar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODAL TAMBAH COA ================= -->
<div id="modalTambah" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center;">
    <div style="background-color: #fff; width: 500px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); overflow: hidden;">
        <div style="background-color: #1e293b; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px;"><i class="fas fa-plus-circle"></i> Tambah Akun COA Baru</h3>
            <span onclick="tutupModalTambah()" style="cursor: pointer; font-size: 20px; font-weight: bold;">&times;</span>
        </div>
        
        <form action="{{ route('fat.coas.store') }}" method="POST" style="padding: 20px;">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="font-size: 12px; font-weight: bold; color: #64748b;">Kode Akun *</label>
                <input type="text" name="kode_akun" placeholder="Contoh: 1001" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="font-size: 12px; font-weight: bold; color: #64748b;">Nama Akun *</label>
                <input type="text" name="nama_akun" placeholder="Contoh: Kas di Tangan" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 5px;">
            </div>
            <div style="display: flex; gap: 15px; margin-bottom: 25px;">
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold; color: #64748b;">Kategori *</label>
                    <select name="kategori" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 5px;">
                        <option value="Harta">Harta (Asset)</option>
                        <option value="Kewajiban">Kewajiban (Liability)</option>
                        <option value="Modal">Modal (Equity)</option>
                        <option value="Pendapatan">Pendapatan (Revenue)</option>
                        <option value="Beban">Beban (Expense)</option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold; color: #64748b;">Saldo Normal *</label>
                    <select name="saldo_normal" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 5px;">
                        <option value="Debit">Debit (+)</option>
                        <option value="Kredit">Kredit (+)</option>
                    </select>
                </div>
            </div>
            <div style="text-align: right;">
                <button type="button" onclick="tutupModalTambah()" style="background: #94a3b8; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; margin-right: 10px;">Batal</button>
                <button type="submit" style="background: #10b981; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: bold;">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT COA ================= -->
<div id="modalEdit" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center;">
    <div style="background-color: #fff; width: 500px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); overflow: hidden;">
        <div style="background-color: #f59e0b; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px;"><i class="fas fa-edit"></i> Edit Akun COA</h3>
            <span onclick="tutupModalEdit()" style="cursor: pointer; font-size: 20px; font-weight: bold;">&times;</span>
        </div>
        
        <form id="formEditCoa" method="POST" style="padding: 20px;">
            @csrf @method('PUT')
            <div style="margin-bottom: 15px;">
                <label style="font-size: 12px; font-weight: bold; color: #64748b;">Kode Akun *</label>
                <input type="text" id="edit_kode" name="kode_akun" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="font-size: 12px; font-weight: bold; color: #64748b;">Nama Akun *</label>
                <input type="text" id="edit_nama" name="nama_akun" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 5px;">
            </div>
            <div style="display: flex; gap: 15px; margin-bottom: 25px;">
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold; color: #64748b;">Kategori *</label>
                    <select id="edit_kategori" name="kategori" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 5px;">
                        <option value="Harta">Harta (Asset)</option>
                        <option value="Kewajiban">Kewajiban (Liability)</option>
                        <option value="Modal">Modal (Equity)</option>
                        <option value="Pendapatan">Pendapatan (Revenue)</option>
                        <option value="Beban">Beban (Expense)</option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold; color: #64748b;">Saldo Normal *</label>
                    <select id="edit_saldo" name="saldo_normal" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 5px;">
                        <option value="Debit">Debit (+)</option>
                        <option value="Kredit">Kredit (+)</option>
                    </select>
                </div>
            </div>
            <div style="text-align: right;">
                <button type="button" onclick="tutupModalEdit()" style="background: #94a3b8; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; margin-right: 10px;">Batal</button>
                <button type="submit" style="background: #f59e0b; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: bold;">Update Akun</button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalTambah() { document.getElementById('modalTambah').style.display = 'flex'; }
    function tutupModalTambah() { document.getElementById('modalTambah').style.display = 'none'; }
    function tutupModalEdit() { document.getElementById('modalEdit').style.display = 'none'; }

    function bukaModalEdit(id, kode, nama, kategori, saldo) {
        document.getElementById('edit_kode').value = kode;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_kategori').value = kategori;
        document.getElementById('edit_saldo').value = saldo;
        
        // Ubah action URL form sesuai ID
        document.getElementById('formEditCoa').action = '/fat/coas/update/' + id;
        
        document.getElementById('modalEdit').style.display = 'flex';
    }

    function hapusCOA(id) {
        Swal.fire({
            title: 'Hapus Master COA?',
            text: "Pastikan akun ini belum pernah dipakai di Jurnal Umum!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection