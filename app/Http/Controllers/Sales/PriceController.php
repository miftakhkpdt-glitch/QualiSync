<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PriceController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil data harga AKTIF (yang belum dihapus / deleted_at kosong)
        $queryAktif = DB::table('material_price_tiers')->whereNull('deleted_at');

        if ($request->filled('search')) {
            $queryAktif->where('no_mm', 'like', '%' . $request->search . '%');
        }

        $prices = $queryAktif->orderBy('id', 'DESC')->get();

        // 2. Ambil data harga yang SUDAH DIHAPUS (Riwayat)
        $deletedPrices = DB::table('material_price_tiers')
                            ->whereNotNull('deleted_at')
                            ->orderBy('deleted_at', 'DESC')
                            ->get();

        // Ambil SEMUA data master material HANYA untuk pemetaan nama di tabel (jangan difilter agar riwayat lama tetap punya nama)
        $materialsMap = DB::table('master_materials')->get()->keyBy('no_mm');

        // Petakan nama material ke tabel aktif
        foreach ($prices as $p) {
            $p->nama_material = $materialsMap[$p->no_mm]->nama_material ?? '-';
        }

        // Petakan nama material ke tabel riwayat (dihapus)
        foreach ($deletedPrices as $dp) {
            $dp->nama_material = $materialsMap[$dp->no_mm]->nama_material ?? '-';
        }

        // ====================================================================
        // [PERBAIKAN DI SINI]: Filter agar dropdown hanya memunculkan Finish Good
        // ====================================================================
        $materials = DB::table('master_materials')
                        ->where('kategori', 'Finish Good')
                        ->get();
                        
        $search = $request->search;

        return view('sales.price-index', compact('prices', 'deletedPrices', 'materials', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_mm' => 'required',
            'ranges' => 'required|array',
            'ranges.*.min_qty' => 'required|numeric',
            'ranges.*.max_qty' => 'required|numeric',
            'ranges.*.price' => 'required|numeric',
        ]);

        foreach ($request->ranges as $range) {
            DB::table('material_price_tiers')->insert([
                'no_mm'      => $request->no_mm,
                'min_qty'    => $range['min_qty'],
                'max_qty'    => $range['max_qty'],
                'price'      => $range['price'],
                'created_by' => Auth::user()->name ?? 'System', 
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Aturan range harga berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        // Soft Delete: Hanya update tanggal hapus dan siapa yang menghapus
        DB::table('material_price_tiers')->where('id', $id)->update([
            'deleted_at' => now(),
            'deleted_by' => Auth::user()->name ?? 'System' 
        ]);
        
        return redirect()->back()->with('success', 'Aturan harga berhasil dihapus dan dipindahkan ke riwayat!');
    }
    
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids; 

        if ($ids && count($ids) > 0) {
            // Soft Delete Massal
            DB::table('material_price_tiers')->whereIn('id', $ids)->update([
                'deleted_at' => now(),
                'deleted_by' => Auth::user()->name ?? 'System' 
            ]);
            
            return redirect()->back()->with('success', count($ids) . ' aturan harga berhasil dihapus sekaligus!');
        }

        return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk dihapus.');
    }
}