<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokController extends Controller
{
    public function rekap(Request $request)
    {
        // 1. Tangkap parameter filter untuk Tabel Stok (Dipisah antara MM/Nama dan Batch)
        $stok_search   = $request->stok_search;
        $batch_search  = $request->batch_search; // Parameter khusus batch baru
        $stok_kategori = $request->stok_kategori;

        // 2. Tangkap parameter filter untuk Tabel Kedatangan (Incoming)
        $in_search   = $request->in_search;
        $in_kategori = $request->in_kategori;
        $startDate   = $request->start_date;
        $endDate     = $request->end_date;

        // =======================================================
        // QUERY 1: STOK AKTUAL (Rincian Per Batch)
        // =======================================================
        $query = DB::table('warehouse_stocks')->where('qty', '>', 0);

        // Filter berdasarkan MM atau Nama Material
        if ($stok_search) {
            $query->where('no_mm', 'like', '%' . $stok_search . '%');
        }

        // Filter khusus berdasarkan No. Batch (Terpisah)
        if ($batch_search) {
            $query->where('batch', 'like', '%' . $batch_search . '%');
        }

        $stokList = $query->orderBy('updated_at', 'DESC')->get();

        foreach ($stokList as $item) {
            $master = DB::table('master_materials')->where('no_mm', $item->no_mm)->first();
            
            if ($master) {
                $item->nama_material = $master->nama_material ?? '-';
                $item->kategori = $master->kategori ?? 'Uncategorized';
            } else {
                $incoming = DB::table('incoming_materials')->where('mm', $item->no_mm)->first();
                $item->nama_material = $incoming ? $incoming->item_name : '-';
                $item->kategori = '-';
            }
        }

        // Filter kategori manual untuk Stok List
        if ($stok_kategori) {
            $stokList = $stokList->filter(function ($item) use ($stok_kategori) {
                return $item->kategori == $stok_kategori;
            });
        }

        $total_kuantitas = 0;
        foreach ($stokList as $item) {
            $total_kuantitas += $item->qty;
        }

        // =======================================================
        // QUERY 2: RIWAYAT KEDATANGAN BARANG (INCOMING WAREHOUSE)
        // =======================================================
        $incomingQuery = DB::table('incoming_materials')
            // PERBAIKAN: Join menggunakan master_materials
            ->leftJoin('master_materials', 'incoming_materials.mm', '=', 'master_materials.no_mm')
            ->select('incoming_materials.*', 'master_materials.kategori')
            ->where('incoming_materials.status_qc', 'Pass')
            ->where('incoming_materials.lokasi_stok', 'Warehouse');

        if ($startDate) {
            $incomingQuery->whereDate('incoming_materials.updated_at', '>=', $startDate);
        }
        if ($endDate) {
            $incomingQuery->whereDate('incoming_materials.updated_at', '<=', $endDate);
        }
        
        if ($in_search) {
            $incomingQuery->where(function($q) use ($in_search) {
                $q->where('incoming_materials.mm', 'like', "%{$in_search}%")
                  ->orWhere('incoming_materials.item_name', 'like', "%{$in_search}%");
            });
        }
        
        if ($in_kategori) {
            $incomingQuery->where('master_materials.kategori', $in_kategori);
        }

        $riwayatKedatangan = $incomingQuery->orderBy('incoming_materials.updated_at', 'DESC')->get();

        foreach($riwayatKedatangan as $riwayat) {
            $riwayat->kategori = $riwayat->kategori ?? '-';
        }

        return view('warehouse.stok.rekap', compact('stokList', 'total_kuantitas', 'riwayatKedatangan', 'request'));
    }
}