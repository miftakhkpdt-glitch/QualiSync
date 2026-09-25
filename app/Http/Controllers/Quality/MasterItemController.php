<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Imports\MasterItemsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\MasterItem;

class MasterItemController extends Controller
{
    // 1. FUNGSI MENAMPILKAN HALAMAN & MENANGANI PENCARIAN (FILTER)
    public function index(Request $request)
    {
        $query = MasterItem::query();

        // Jika ada pencarian dari kotak pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            
            // Filter berdasarkan No. MM atau Nama Item
            // Sesuaikan 'item_name' jika di database Anda namanya berbeda (misal 'nama_item')
            $query->where('no_mm', 'like', '%' . $search . '%')
                  ->orWhere('item_name', 'like', '%' . $search . '%');
        }

        // Ambil data hasil pencarian (jika tidak mencari, maka tampil semua)
        $masterItems = $query->get(); 
        
        return view('quality.master-items', compact('masterItems'));
    }

    // 2. FUNGSI IMPORT EXCEL
    public function importExcel(Request $request)
    {
        // Validasi file harus berformat excel/csv
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        // Jalankan proses import
        Excel::import(new MasterItemsImport, $request->file('file'));

        return redirect()->back()->with('success', 'Data Master Item berhasil di-import dan diperbarui!');
    }

    // 3. FUNGSI HAPUS DATA (Dibutuhkan oleh tombol Hapus di Blade)
    public function destroy($id)
    {
        $item = MasterItem::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('success', 'Data Master Item berhasil dihapus!');
    }
}