<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SupplierPerformanceController extends Controller
{
    // 1. HALAMAN UTAMA & FILTER CETAK
    public function index(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        
        $performances = DB::table('supplier_performances')
            ->join('master_vendors', 'supplier_performances.supplier_id', '=', 'master_vendors.id')
            ->where('periode_tahun', $tahun)
            ->select('supplier_performances.*', 'master_vendors.vendor_name')
            ->orderBy('master_vendors.vendor_name')
            ->get();
            
        // Urutkan bulan secara kronologis (bukan abjad)
        $bulanOrder = ['Januari'=>1, 'Februari'=>2, 'Maret'=>3, 'April'=>4, 'Mei'=>5, 'Juni'=>6, 'Juli'=>7, 'Agustus'=>8, 'September'=>9, 'Oktober'=>10, 'November'=>11, 'Desember'=>12];
        $performances = $performances->sortBy(function($item) use ($bulanOrder) {
            return $bulanOrder[$item->periode_bulan] ?? 99;
        });

        $vendors = DB::table('master_vendors')->orderBy('vendor_name')->get();

        return view('quality.performa.index', compact('performances', 'tahun', 'vendors'));
    }

    // 2. HALAMAN FORM INPUT
    public function create()
    {
        $vendors = DB::table('master_vendors')->orderBy('vendor_name')->get();
        // Pastikan nama file Anda sekarang adalah create.blade.php di dalam folder performa
        return view('quality.performa.create', compact('vendors'));
    }

    // 3. PROSES SIMPAN (Sama seperti sebelumnya)
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'periode_bulan' => 'required',
            'periode_tahun' => 'required',
        ]);

        $getGrade = function($score) {
            if ($score >= 91) return 'A';
            if ($score >= 80) return 'B';
            if ($score >= 61) return 'C';
            return 'D';
        };

        $getValue = function($grade, $weight) {
            if ($grade == 'A') return $weight;
            if ($grade == 'B') return $weight * 0.75;
            if ($grade == 'C') return $weight * 0.5;
            return $weight * 0.25; 
        };

        // Kuantitatif
        $total_kualitas = $getValue($getGrade($request->q_item_diterima), 30) + $getValue($getGrade($request->q_scar), 20) + $getValue($getGrade($request->q_qty), 10) + $getValue($getGrade($request->q_jenis), 10) + $getValue($getGrade($request->q_coa), 20) + $getValue($getGrade($request->q_packaging), 10);
        $total_pengiriman = $getValue($getGrade($request->q_pengiriman), 100);
        $skor_kuantitatif = (0.7 * $total_kualitas) + (0.3 * $total_pengiriman);
        
        $status_kuantitatif = 'POOR';
        if($skor_kuantitatif >= 91) $status_kuantitatif = 'EXCELLENCE';
        elseif($skor_kuantitatif >= 80) $status_kuantitatif = 'AVERAGE';
        elseif($skor_kuantitatif >= 61) $status_kuantitatif = 'NEED IMPROVEMENT';

        // Kualitatif
        $skor_kualitatif = $getValue($request->k_manajemen, 25) + $getValue($request->k_kompetensi, 25) + $getValue($request->k_efek_internal, 25) + $getValue($request->k_masalah_kritis, 25);
        
        $status_kualitatif = 'POOR';
        if($skor_kualitatif >= 91) $status_kualitatif = 'EXCELLENCE';
        elseif($skor_kualitatif >= 80) $status_kualitatif = 'AVERAGE';
        elseif($skor_kualitatif >= 61) $status_kualitatif = 'NEED IMPROVEMENT';

        DB::table('supplier_performances')->insert([
            'supplier_id' => $request->supplier_id,
            'periode_bulan' => $request->periode_bulan,
            'periode_tahun' => $request->periode_tahun,
            'q_item_diterima' => $request->q_item_diterima ?? 0,
            'q_scar' => $request->q_scar ?? 0,
            'q_qty' => $request->q_qty ?? 0,
            'q_jenis' => $request->q_jenis ?? 0,
            'q_coa' => $request->q_coa ?? 0,
            'q_packaging' => $request->q_packaging ?? 0,
            'q_pengiriman' => $request->q_pengiriman ?? 0,
            'k_manajemen' => $request->k_manajemen,
            'k_kompetensi' => $request->k_kompetensi,
            'k_efek_internal' => $request->k_efek_internal,
            'k_masalah_kritis' => $request->k_masalah_kritis,
            'skor_kuantitatif' => $skor_kuantitatif,
            'status_kuantitatif' => $status_kuantitatif,
            'skor_kualitatif' => $skor_kualitatif,
            'status_kualitatif' => $status_kualitatif,
            'user_id' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Penilaian berhasil disimpan!');
    }

    // 4. EKSEKUSI CETAK (PRINT)
    public function print(Request $request)
    {
        $bulan_selected = $request->bulan ?? [];
        $tahun = $request->tahun ?? date('Y');
        $supplier_id = $request->supplier_id;

        if (empty($bulan_selected)) {
            return redirect()->back()->with('error', 'Silakan centang minimal 1 bulan untuk dicetak.');
        }

        $query = DB::table('supplier_performances')
            ->join('master_vendors', 'supplier_performances.supplier_id', '=', 'master_vendors.id')
            ->where('periode_tahun', $tahun)
            ->whereIn('periode_bulan', $bulan_selected);

        if ($supplier_id) {
            $query->where('supplier_id', $supplier_id);
        }

        $data = $query->select('supplier_performances.*', 'master_vendors.vendor_name')->get();

        // Urutkan berdasarkan kronologi bulan
        $bulanOrder = ['Januari'=>1, 'Februari'=>2, 'Maret'=>3, 'April'=>4, 'Mei'=>5, 'Juni'=>6, 'Juli'=>7, 'Agustus'=>8, 'September'=>9, 'Oktober'=>10, 'November'=>11, 'Desember'=>12];
        $data = $data->sortBy(function($item) use ($bulanOrder) {
            return $bulanOrder[$item->periode_bulan] ?? 99;
        });

        // Kelompokkan data per supplier agar cetaknya rapi
        $groupedData = $data->groupBy('vendor_name');

        return view('quality.performa.print', compact('groupedData', 'bulan_selected', 'tahun'));
    }
}