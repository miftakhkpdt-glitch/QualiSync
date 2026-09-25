<?php

namespace App\Http\Controllers\Ppic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MrpController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // --- PENGAMBILAN DATA FORECAST DENGAN FILTER ---
        $query = DB::table('sales_forecasts')
            ->select('no_mm', 'nama_produk', 'nama_customer')
            ->where('status', 'Aktif');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nama_customer', 'like', '%' . $search . '%')
                  ->orWhere('nama_produk', 'like', '%' . $search . '%')
                  ->orWhere('no_mm', 'like', '%' . $search . '%');
            });
        }

        $activeForecasts = $query->groupBy('no_mm', 'nama_produk', 'nama_customer')->get();
        $mrpData = [];

        // STATUS PR PENDING HANYA UNTUK ITEM MIN-MAX
        $statusPending = ['Selesai', 'Batal', 'Closed', 'Ditolak']; 

        foreach ($activeForecasts as $item) {
            $fg_mm = $item->no_mm;
            
            $customer = DB::table('master_customers')->where('nama_customer', $item->nama_customer)->first();
            if (!$customer) continue;

            $masterItemFG = DB::table('master_materials')->where('no_mm', $fg_mm)->first();
            $stokFG = DB::table('warehouse_stocks')->where('no_mm', $fg_mm)->sum('qty');

            // --- PENCARIAN KOMPONEN PWEB ---
            $pweb_mm = null; $stokPweb = 0; $ropPwebTampil = 0; $maxStockPweb = 0;
            $pwebData = DB::table('product_boms')
                ->join('master_materials', 'product_boms.component_mm', '=', 'master_materials.no_mm')
                ->where('product_boms.fg_mm', $fg_mm)
                ->where(function($query) {
                    $query->where('master_materials.nama_material', 'like', '%PRINTED WEB%')
                          ->orWhere('master_materials.nama_material', 'like', '%PWEB%')
                          ->orWhere('master_materials.nama_material', 'like', '%WEB%');
                })->select('master_materials.*')->first();

            if ($pwebData) {
                $pweb_mm = $pwebData->no_mm;
                $stokPweb = DB::table('warehouse_stocks')->where('no_mm', $pweb_mm)->sum('qty');
                $ropPwebTampil = $pwebData->rop ?? 0;
                $maxStockPweb  = $pwebData->max_stock ?? 0;
            }

            // --- PENCARIAN KOMPONEN CAP ---
            $cap_mm = null; $stokCap = 0; $ropCapTampil = 0; $maxStockCap = 0;
            $capData = DB::table('product_boms')
                ->join('master_materials', 'product_boms.component_mm', '=', 'master_materials.no_mm')
                ->where('product_boms.fg_mm', $fg_mm)
                ->where(function($q) {
                    $q->where('master_materials.nama_material', 'like', 'CAP %')
                      ->orWhere('master_materials.nama_material', 'like', '% CAP %');
                })->select('master_materials.*')->first();

            if ($capData) {
                $cap_mm = $capData->no_mm;
                $stokCap = DB::table('warehouse_stocks')->where('no_mm', $cap_mm)->sum('qty');
                $ropCapTampil = $capData->rop ?? 0;
                $maxStockCap  = $capData->max_stock ?? 0;
            }

            // --- PENCARIAN KOMPONEN HDPE ---
            $hdpeData = DB::table('product_boms')
                ->join('master_materials', 'product_boms.component_mm', '=', 'master_materials.no_mm')
                ->where('product_boms.fg_mm', $fg_mm)
                ->where('master_materials.nama_material', 'like', '%HDPE%')
                ->select('product_boms.qty_usage', 'product_boms.satuan', 'master_materials.no_mm')->first();

            $hdpe_mm = $hdpeData->no_mm ?? null;
            $stokHDPE = $hdpe_mm ? DB::table('warehouse_stocks')->where('no_mm', $hdpe_mm)->sum('qty') : 0;
            $usageHDPE = $hdpeData->qty_usage ?? 0;
            if(strtolower($hdpeData->satuan ?? '') == 'gram') { $usageHDPE = $usageHDPE / 1000; }

            // --- PENCARIAN KOMPONEN MASTER BATCH (MB) ---
            $mbData = DB::table('product_boms')
                ->join('master_materials', 'product_boms.component_mm', '=', 'master_materials.no_mm')
                ->where('product_boms.fg_mm', $fg_mm)
                ->where(function($q) {
                    $q->where('master_materials.nama_material', 'like', 'MB %')
                      ->orWhere('master_materials.nama_material', 'like', '%MASTER BATCH%')
                      ->orWhere('master_materials.nama_material', 'like', '% MB %');
                })->select('product_boms.qty_usage', 'product_boms.satuan', 'master_materials.no_mm')->first();

            $mb_mm = $mbData->no_mm ?? null;
            $stokMB = $mb_mm ? DB::table('warehouse_stocks')->where('no_mm', $mb_mm)->sum('qty') : 0;
            $usageMB = $mbData->qty_usage ?? 0;
            if(strtolower($mbData->satuan ?? '') == 'gram') { $usageMB = $usageMB / 1000; }

            // --- PENCARIAN KOMPONEN BOX ---
            $packData = DB::table('product_boms')
                ->join('master_materials', 'product_boms.component_mm', '=', 'master_materials.no_mm')
                ->where('product_boms.fg_mm', $fg_mm)
                ->whereIn('master_materials.kategori', ['Packaging', 'CSM'])
                ->where(function($q) {
                    $q->where('master_materials.nama_material', 'like', '%KARTON%')
                      ->orWhere('master_materials.nama_material', 'like', '%BOX%')
                      ->orWhere('master_materials.nama_material', 'like', '%IMPRABOARD%');
                })->select('product_boms.qty_usage', 'master_materials.no_mm', 'master_materials.nama_material', 'master_materials.kategori')->first();

            $pack_mm = $packData->no_mm ?? null;
            $namaPack = $packData->nama_material ?? '-';
            $stokPack = $pack_mm ? DB::table('warehouse_stocks')->where('no_mm', $pack_mm)->sum('qty') : 0;
            $usagePack = $packData->qty_usage ?? 0;
            $isCsm = (($packData->kategori ?? '') == 'CSM') ? true : false;

            // --- PENCARIAN KOMPONEN PLASTIK GUSSET ---
            $gussetData = DB::table('product_boms')
                ->join('master_materials', 'product_boms.component_mm', '=', 'master_materials.no_mm')
                ->where('product_boms.fg_mm', $fg_mm)
                ->whereIn('master_materials.kategori', ['Packaging', 'CSM'])
                ->where('master_materials.nama_material', 'like', '%GUSSET%')
                ->select('product_boms.qty_usage', 'master_materials.no_mm', 'master_materials.nama_material', 'master_materials.kategori')->first();

            $gusset_mm = $gussetData->no_mm ?? null;
            $namaGusset = $gussetData->nama_material ?? '-';
            $stokGusset = $gusset_mm ? DB::table('warehouse_stocks')->where('no_mm', $gusset_mm)->sum('qty') : 0;
            $usageGusset = $gussetData->qty_usage ?? 0;
            $isCsmGusset = (($gussetData->kategori ?? '') == 'CSM') ? true : false;


            // ========================================================================
            // IDE 2: CEK STATUS "ON ORDER" HANYA UNTUK PWEB & CAP (Min-Max System)
            // ========================================================================
            $prPendingPweb = $pweb_mm ? DB::table('purchase_requests')->where('no_mm', $pweb_mm)->whereNotIn('status', $statusPending)->sum('qty') : 0;
            $prPendingCap  = $cap_mm  ? DB::table('purchase_requests')->where('no_mm', $cap_mm)->whereNotIn('status', $statusPending)->sum('qty')  : 0;

            // Total Stok bayangan HANYA untuk PWEB dan CAP
            $totalPweb = $stokPweb + $prPendingPweb;
            $totalCap  = $stokCap + $prPendingCap;

            // --- DEKLARASI VARIABEL TAMPILAN ---
            $statusFG = '-'; $kebutuhanWO = 0; $ropFGTampil = 0;
            $statusPweb = '-'; $kebutuhanPO = 0; 
            $statusCap = '-'; $prCap = 0; 
            $keteranganCoverage = ''; $kebutuhanCoverage = 0; $statusCoverage = '-';

            // --- KALKULASI MIN-MAX ---
            if ($customer->tipe_kalkulasi_mrp == 'Min-Max') {
                $ropFGTampil = $masterItemFG->rop ?? 0;
                $maxStockFG = $masterItemFG->max_stock ?? 0;
                
                if ($stokFG <= $ropFGTampil) {
                    $statusFG = 'Shortage';
                    $kebutuhanWO = $maxStockFG - $stokFG;
                } else {
                    $statusFG = 'Aman';
                }

                // Kalkulasi PWEB (On Order Logic Aktif)
                if ($totalPweb <= $ropPwebTampil) {
                    $statusPweb = 'Shortage';
                    $kebutuhanPO = $maxStockPweb - $totalPweb;
                } else {
                    $statusPweb = ($stokPweb <= $ropPwebTampil && $prPendingPweb > 0) ? 'On Order' : 'Aman';
                }

                // Kalkulasi CAP (On Order Logic Aktif)
                if ($totalCap <= $ropCapTampil) {
                    $statusCap = 'Shortage';
                    $prCap = $maxStockCap - $totalCap;
                } else {
                    $statusCap = ($stokCap <= $ropCapTampil && $prPendingCap > 0) ? 'On Order' : 'Aman';
                }
            } 
            // --- KALKULASI COVERAGE ---
            elseif ($customer->tipe_kalkulasi_mrp == 'Coverage') {
                $yield = ($customer->yield_pweb ?? 100) / 100;
                $bulanTarget = $customer->batas_coverage_bulan ?? 1;
                $totalStokDiakui = $stokFG + ($stokPweb * $yield);

                $demand = DB::table('sales_forecasts')
                    ->where('no_mm', $fg_mm)
                    ->where('status', 'Aktif')
                    ->orderBy('tahun', 'asc')->orderBy('bulan', 'asc')->limit($bulanTarget)->sum('qty_forecast');

                $hasilMrp = $totalStokDiakui - $demand;

                if ($hasilMrp < 0) {
                    $statusCoverage = 'Shortage';
                    $kebutuhanCoverage = abs($hasilMrp);
                } else {
                    $statusCoverage = 'Aman';
                }
                $keteranganCoverage = "Target $bulanTarget Bulan: " . number_format($demand);
            }

            // --- KALKULASI BOM DEPENDENT DEMAND (DIKEMBALIKAN KE MURNI) ---
            $targetProduksi = ($customer->tipe_kalkulasi_mrp == 'Min-Max') ? $kebutuhanWO : $kebutuhanCoverage;

            // Hitung HDPE (Murni)
            $kebutuhanTotalHDPE = $targetProduksi * $usageHDPE;
            $selisihHDPE = $kebutuhanTotalHDPE - $stokHDPE;
            $statusHDPE = ($selisihHDPE > 0) ? 'Shortage' : 'Aman';
            $prHDPE = ($selisihHDPE > 0) ? $selisihHDPE : 0;

            // Hitung MB (Murni)
            $kebutuhanTotalMB = $targetProduksi * $usageMB;
            $selisihMB = $kebutuhanTotalMB - $stokMB;
            $statusMB = ($selisihMB > 0) ? 'Shortage' : 'Aman';
            $prMB = ($selisihMB > 0) ? $selisihMB : 0;

            // Hitung Packaging BOX (Murni)
            $kebutuhanTotalPack = ceil($targetProduksi * $usagePack);
            $selisihPack = $kebutuhanTotalPack - $stokPack;
            $statusPack = ($selisihPack > 0) ? 'Shortage' : 'Aman';
            $actionPack = ($selisihPack > 0) ? $selisihPack : 0;

            // Hitung Plastik GUSSET (Murni)
            $kebutuhanTotalGusset = ceil($targetProduksi * $usageGusset);
            $selisihGusset = $kebutuhanTotalGusset - $stokGusset;
            $statusGusset = ($selisihGusset > 0) ? 'Shortage' : 'Aman';
            $actionGusset = ($selisihGusset > 0) ? $selisihGusset : 0;


            // MASUKKAN SEMUANYA KE DALAM ARRAY
            $mrpData[] = (object) [
                'nama_customer' => $item->nama_customer,
                'no_mm' => $fg_mm,
                'nama_produk' => $item->nama_produk,
                'tipe_kalkulasi' => $customer->tipe_kalkulasi_mrp,
                
                'stok_fg' => $stokFG,
                'rop_fg' => $ropFGTampil,
                'max_stock_fg' => $maxStockFG ?? 0,
                'status_fg' => $statusFG,
                'kebutuhan_wo' => $kebutuhanWO,

                'pweb_mm' => $pweb_mm,
                'stok_pweb' => $stokPweb,
                'rop_pweb' => $ropPwebTampil,
                'max_stock_pweb' => $maxStockPweb ?? 0,
                'status_pweb' => $statusPweb,
                'kebutuhan_po' => $kebutuhanPO,

                'cap_mm' => $cap_mm,
                'stok_cap' => $stokCap,
                'rop_cap' => $ropCapTampil,
                'max_stock_cap' => $maxStockCap,
                'status_cap' => $statusCap,
                'pr_cap' => $prCap,

                'status_coverage' => $statusCoverage,
                'kebutuhan_coverage' => $kebutuhanCoverage,
                'ket_coverage' => $keteranganCoverage,

                'hdpe_mm' => $hdpe_mm,
                'stok_hdpe' => $stokHDPE,
                'kebutuhan_hdpe' => $kebutuhanTotalHDPE,
                'status_hdpe' => $statusHDPE,
                'pr_hdpe' => $prHDPE,

                'mb_mm' => $mb_mm,
                'stok_mb' => $stokMB,
                'kebutuhan_mb' => $kebutuhanTotalMB,
                'status_mb' => $statusMB,
                'pr_mb' => $prMB,

                'pack_mm' => $pack_mm,
                'nama_pack' => $namaPack,
                'stok_pack' => $stokPack,
                'kebutuhan_pack' => $kebutuhanTotalPack,
                'status_pack' => $statusPack,
                'action_pack' => $actionPack,
                'is_csm' => $isCsm,

                'gusset_mm' => $gusset_mm,
                'nama_gusset' => $namaGusset,
                'stok_gusset' => $stokGusset,
                'kebutuhan_gusset' => $kebutuhanTotalGusset,
                'status_gusset' => $statusGusset,
                'action_gusset' => $actionGusset,
                'is_csm_gusset' => $isCsmGusset,
            ];
        }

        return view('ppic.mrp-result', compact('mrpData'));
    }
}