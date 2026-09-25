@extends('layouts.staff-layout')

@section('konten')
<div style="max-width: 1200px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h3 style="margin-bottom: 5px; color: #334155; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
        <i class="fas fa-building"></i> Kelola Daftar Supplier / Vendor
    </h3>
    <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">Tambah dan pantau daftar vendor/supplier yang terintegrasi dengan sistem Purchasing & Warehouse.</p>

    <!-- FORM TAMBAH VENDOR -->
    <form action="{{ route('purchasing.vendors.store') }}" method="POST" style="background: #f8fafc; padding: 20px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 30px;">
        @csrf
        <!-- Grid disesuaikan untuk menampung No Telp -->
        <div style="display: grid; grid-template-columns: 1fr 2fr 1.5fr 2.5fr auto; gap: 15px; align-items: flex-end;">
            <div>
                <label style="font-size: 13px; font-weight: bold;">Vendor Code</label>
                <input type="text" name="vendor_code" placeholder="Cth: VEND-001" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>
            <div>
                <label style="font-size: 13px; font-weight: bold;">Vendor Name</label>
                <input type="text" name="vendor_name" placeholder="Nama Perusahaan" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>
            <div>
                <label style="font-size: 13px; font-weight: bold;">No. Telp</label>
                <input type="text" name="no_telp" placeholder="Cth: 0812..." required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>
            <div>
                <label style="font-size: 13px; font-weight: bold;">Address</label>
                <input type="text" name="address" placeholder="Alamat lengkap" required style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>
            <div>
                <button type="submit" style="background: #0ea5e9; color: white; padding: 9px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; height: 38px;">
                    <i class="fas fa-plus"></i> Simpan
                </button>
            </div>
        </div>
    </form>

    <!-- TABEL DAFTAR VENDOR -->
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #f1f5f9; color: #334155; border-bottom: 2px solid #cbd5e1;">
                    <th style="padding: 12px 10px;">No</th>
                    <th style="padding: 12px 10px;">Vendor Code</th>
                    <th style="padding: 12px 10px;">Vendor Name</th>
                    <th style="padding: 12px 10px;">No. Telp</th>
                    <th style="padding: 12px 10px;">Address</th>
                    <th style="padding: 12px 10px; text-align: center;">Aksi</th> 
                </tr>
            </thead>
            <tbody>
                @forelse($vendors as $index => $v)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 10px;">{{ $index + 1 }}</td>
                        <td style="padding: 10px; font-weight: bold; color: #2563eb;">{{ $v->vendor_code }}</td>
                        <td style="padding: 10px;">{{ $v->vendor_name }}</td>
                        <td style="padding: 10px;">{{ $v->no_telp ?? '-' }}</td>
                        <td style="padding: 10px;">{{ $v->address }}</td>
                        <td style="padding: 10px; text-align: center;">
                            <div style="display: flex; gap: 5px; justify-content: center;">
                                <a href="{{ url('/purchasing/vendors/' . $v->id . '/edit') }}" style="background-color: #f59e0b; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ url('/purchasing/vendors/' . $v->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus vendor ini?');" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background-color: #ef4444; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px; color: #64748b;">Belum ada data vendor/supplier yang ditambahkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection