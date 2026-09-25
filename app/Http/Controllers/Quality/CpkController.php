<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CpkController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ?? now()->subDays(7)->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');
        $no_mm = $request->no_mm; 
        $selectedParams = $request->parameter_ids ?? []; 
        $selectedBatches = $request->batch_checkboxes ?? []; // Menangkap array checkbox batch yang dicentang

        $masterItems = DB::table('master_materials')->where('kategori', 'Finish Good')->get();
        $parameters = DB::table('master_parameters')->where('tipe_input', 'Angka')->get();

        // Ambil daftar No. Batch (Diperbaiki agar lolos validasi SQL strict mode)
        $availableBatches = [];
        if ($no_mm) {
            $availableBatches = DB::table('qir_records')
                ->where('no_mm', $no_mm)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->select('no_batch', DB::raw('MAX(tanggal) as max_tanggal'), DB::raw('MAX(id) as max_id'))
                ->groupBy('no_batch')
                ->orderBy('max_tanggal', 'desc')
                ->orderBy('max_id', 'desc')
                ->pluck('no_batch')
                ->toArray();
        }

        $multiCpkResults = [];

        if ($no_mm && !empty($selectedParams)) {
            
            foreach ($selectedParams as $param_id) {
                
                $standard = DB::table('master_item_standards')
                    ->where('no_mm', $no_mm)
                    ->where('parameter_id', $param_id)
                    ->first();

                $paramInfo = DB::table('master_parameters')->where('id', $param_id)->first();

                if ($standard && $standard->min_value !== null && $standard->max_value !== null && $paramInfo) {
                    
                    $lsl = (float) $standard->min_value;
                    $usl = (float) $standard->max_value;

                    $query = DB::table('qir_records')
                        ->join('qir_details', 'qir_records.id', '=', 'qir_details.qir_id')
                        ->join('qir_results', 'qir_details.id', '=', 'qir_results.qir_detail_id')
                        ->where('qir_records.no_mm', $no_mm)
                        ->where('qir_results.parameter_id', $param_id)
                        ->whereBetween('qir_records.tanggal', [$startDate, $endDate]);

                    // Jika ada batch yang dicentang oleh user, saring berdasarkan batch tersebut
                    if (!empty($selectedBatches)) {
                        $query->whereIn('qir_records.no_batch', $selectedBatches);
                    }

                    $results = $query->select('qir_records.no_batch', 'qir_details.sample_no', 'qir_results.hasil_aktual')
                        ->orderBy('qir_records.tanggal', 'asc')
                        ->orderBy('qir_details.sample_no', 'asc')
                        ->get();

                    if ($results->count() > 1) {
                        $dataAktual = [];
                        $labels = [];

                        foreach ($results as $res) {
                            $val = (float) $res->hasil_aktual;
                            $dataAktual[] = $val;
                            $labels[] = $res->no_batch . ' (S' . $res->sample_no . ')'; 
                        }

                        $n = count($dataAktual);
                        $mean = array_sum($dataAktual) / $n;
                        
                        $variance = 0;
                        foreach ($dataAktual as $val) {
                            $variance += pow($val - $mean, 2);
                        }
                        $stdDev = sqrt($variance / ($n - 1));

                        if ($stdDev > 0) {
                            $cp  = ($usl - $lsl) / (6 * $stdDev);
                            $cpu = ($usl - $mean) / (3 * $stdDev);
                            $cpl = ($mean - $lsl) / (3 * $stdDev);
                            $cpk = min($cpu, $cpl);
                        } else {
                            $cp = $cpu = $cpl = $cpk = 0;
                        }

                        if ($cpk >= 1.33) {
                            $status = 'Capable (Excellent)';
                            $color = '#10b981';
                        } elseif ($cpk >= 1.00) {
                            $status = 'Acceptable';
                            $color = '#eab308';
                        } else {
                            $status = 'Not Capable';
                            $color = '#ef4444';
                        }

                        $multiCpkResults[$param_id] = [
                            'param_name' => $paramInfo->nama_parameter . ($paramInfo->satuan ? " ({$paramInfo->satuan})" : ''),
                            'n' => $n,
                            'mean' => round($mean, 3),
                            'stdDev' => round($stdDev, 4),
                            'usl' => $usl,
                            'lsl' => $lsl,
                            'cp' => round($cp, 2),
                            'cpk' => round($cpk, 2),
                            'status' => $status,
                            'color' => $color,
                            'chartData' => [
                                'labels' => $labels,
                                'data' => $dataAktual,
                                'usl_line' => array_fill(0, $n, $usl),
                                'lsl_line' => array_fill(0, $n, $lsl),
                                'mean_line' => array_fill(0, $n, $mean)
                            ]
                        ];
                    }
                }
            }
        }

        return view('quality.inproses.fg.laporan_cpk', compact(
            'startDate', 'endDate', 'masterItems', 'parameters', 
            'no_mm', 'selectedParams', 'availableBatches', 'selectedBatches', 'multiCpkResults'
        ));
    }
}