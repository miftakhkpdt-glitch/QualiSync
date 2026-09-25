@extends('layouts.staff-layout')

@section('title', 'Master Data Reject')

@section('konten')
<div style="padding: 30px; background-color: #f8fafc; min-height: 100vh;">
    
    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 24px; font-weight: bold; color: #1e293b; margin: 0 0 5px 0;">
            <i class="fas fa-database" style="color: #ef4444; margin-right: 8px;"></i> Kamus Master Reject
        </h2>
        <p style="color: #64748b; font-size: 14px; margin: 0;">Kelola daftar kendala/cacat produk yang akan muncul sebagai pilihan di Daily Report.</p>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #bbf7d0;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
        
        <!-- FORM INPUT KIRI -->
        <div style="flex: 1; min-width: 300px; max-width: 400px;">
            <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
                <h3 style="font-size: 16px; margin: 0 0 15px 0; color: #334155; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">Tambah Reject Baru</h3>
                
                <form action="{{ route('produksi.master.reject.store') }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 15px;">
                        <label style="display:block; font-size: 12px; font-weight: bold; color: #64748b; margin-bottom: 5px;">Area Mesin</label>
                        <select name="mesin" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px; outline: none;">
                            <option value="">-- Pilih Mesin --</option>
                            <option value="AISA">Mesin AISA</option>
                            <option value="COMBITOOL">Mesin COMBITOOL</option>
                            <option value="PRINTING">Mesin PRINTING</option>
                            <option value="UMUM">UMUM / Semua Area</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display:block; font-size: 12px; font-weight: bold; color: #64748b; margin-bottom: 5px;">Kategori Cacat</label>
                        <select name="kategori" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px; outline: none;">
                            <option value="Reject Process">Reject Process</option>
                            <option value="Reject Printing">Reject Printing</option>
                            <option value="Reject Material">Reject Material</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display:block; font-size: 12px; font-weight: bold; color: #64748b; margin-bottom: 5px;">Nama Cacat / Kendala</label>
                        <input type="text" name="nama_reject" required placeholder="Contoh: Tube Bocor Bawah" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px; outline: none;">
                    </div>

                    <button type="submit" style="width: 100%; background: #ef4444; color: white; border: none; padding: 12px; border-radius: 5px; font-weight: bold; cursor: pointer; transition: 0.2s;">
                        <i class="fas fa-plus"></i> Simpan Master Reject
                    </button>
                </form>
            </div>
        </div>

        <!-- TABEL DATA KANAN -->
        <div style="flex: 2; min-width: 400px;">
            <div style="background: white; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead style="background-color: #f1f5f9; color: #475569; font-size: 13px;">
                        <tr>
                            <th style="padding: 15px 20px;">Mesin & Kategori</th>
                            <th style="padding: 15px 20px;">Nama Cacat</th>
                            <th style="padding: 15px 20px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rejects as $item)
                        <tr style="border-bottom: 1px solid #f1f5f9; font-size: 14px;">
                            <td style="padding: 12px 20px;">
                                <strong style="color: #3b82f6;">{{ $item->mesin }}</strong><br>
                                <span style="font-size: 12px; color: #64748b;">{{ $item->kategori }}</span>
                            </td>
                            <td style="padding: 12px 20px; font-weight: bold; color: #334155;">
                                {{ $item->nama_reject }}
                            </td>
                            <td style="padding: 12px 20px; text-align: center;">
                                <a href="{{ route('produksi.master.reject.delete', $item->id) }}" onclick="return confirm('Yakin ingin menghapus data ini?')" style="background: #fee2e2; color: #991b1b; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: bold;">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="padding: 20px; text-align: center; color: #94a3b8;">Data Master Reject masih kosong.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
</div>
@endsection