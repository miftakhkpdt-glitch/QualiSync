<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = DB::table('master_vendors')->orderBy('id', 'DESC')->get();
        return view('purchasing.vendor.vendors', compact('vendors'));
    }

    // ====================================================================
    // PROSES SIMPAN VENDOR BARU
    // ====================================================================
    public function store(Request $request)
    {
        $request->validate([
            'vendor_code' => 'required|unique:master_vendors,vendor_code',
            'vendor_name' => 'required',
            'address' => 'required',
            // no_telp tidak wajib diisi (optional), jadi tidak perlu divalidasi 'required'
        ]);

        DB::table('master_vendors')->insert([
            'vendor_code' => $request->vendor_code,
            'vendor_name' => $request->vendor_name,
            'no_telp'     => $request->no_telp, // <-- Tambahan agar No Telp tersimpan saat buat baru
            'address'     => $request->address,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return redirect()->back()->with('success', 'Data Supplier / Vendor berhasil ditambahkan!');
    }

    // ====================================================================
    // A. MENAMPILKAN FORM EDIT VENDOR (Nama fungsi diubah menjadi edit)
    // ====================================================================
    public function edit($id)
    {
        // Ambil data vendor berdasarkan ID
        $vendor = DB::table('master_vendors')->where('id', $id)->first();
        
        if (!$vendor) {
            return redirect()->back()->with('error', 'Data vendor tidak ditemukan.');
        }

        return view('purchasing.vendor.edit-vendor', compact('vendor'));
    }

    // ====================================================================
    // B. PROSES UPDATE DATA VENDOR (Nama fungsi diubah menjadi update)
    // ====================================================================
    public function update(Request $request, $id)
    {
        DB::table('master_vendors')->where('id', $id)->update([
            'vendor_code' => $request->vendor_code,
            'vendor_name' => $request->vendor_name,
            'no_telp'     => $request->no_telp,
            'address'     => $request->address,
            'updated_at'  => now()
        ]);

        return redirect('/purchasing/vendors')->with('success', 'Data Vendor berhasil diperbarui!');
    }

    // ====================================================================
    // C. PROSES HAPUS VENDOR (Nama fungsi diubah menjadi destroy)
    // ====================================================================
    public function destroy($id)
    {
        try {
            DB::table('master_vendors')->where('id', $id)->delete();
            return redirect()->back()->with('success', 'Vendor berhasil dihapus!');
        } catch (\Exception $e) {
            // Jika gagal dihapus (misal karena ID vendor ini sudah dipakai di tabel PO)
            return redirect()->back()->with('error', 'Vendor tidak bisa dihapus karena sudah memiliki riwayat transaksi (PO).');
        }
    }
}