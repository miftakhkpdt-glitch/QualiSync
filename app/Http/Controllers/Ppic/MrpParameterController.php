<?php

namespace App\Http\Controllers\Ppic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MrpParameterController extends Controller
{
    public function index(Request $request)
    {
        // 1. Cari pelanggan yang khusus menggunakan tipe Min-Max
        $minMaxCustomers = DB::table('master_customers')
            ->where('tipe_kalkulasi_mrp', 'Min-Max')
            ->pluck('nama_customer');

        // 2. Ambil MM Finish Good (FG) milik pelanggan tersebut dari Forecast Aktif
        $fgMms = DB::table('sales_forecasts')
            ->whereIn('nama_customer', $minMaxCustomers)
            ->where('status', 'Aktif')
            ->pluck('no_mm');

        // 3. Ambil MM Komponen (Pweb, Cap, dll) pembentuk FG tersebut dari tabel BOM
        $componentMms = DB::table('product_boms')
            ->whereIn('fg_mm', $fgMms)
            ->pluck('component_mm');

        // 4. Gabungkan MM FG dan MM Komponen menjadi satu daftar target
        $targetMms = $fgMms->merge($componentMms)->unique()->toArray();

        // 5. Tampilkan master material HANYA yang ada di dalam daftar target Min-Max
        $query = DB::table('master_materials')
            ->whereIn('no_mm', $targetMms)
            ->orderBy('no_mm', 'asc');

        // Fitur Pencarian tetap dipertahankan
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('nama_material', 'like', '%' . $request->search . '%')
                  ->orWhere('no_mm', 'like', '%' . $request->search . '%');
            });
        }

        // Potong per 10 baris agar rapi
        $items = $query->paginate(10); 
        
        return view('ppic.mrp-parameter', compact('items'));
    }

    public function update(Request $request, $id)
    {
        // 100% Menyimpan ke master_materials
        DB::table('master_materials')->where('id', $id)->update([
            'rop' => $request->rop ?? 0,
            'max_stock' => $request->max_stock ?? 0,
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Parameter stok berhasil diperbarui!');
    }
}