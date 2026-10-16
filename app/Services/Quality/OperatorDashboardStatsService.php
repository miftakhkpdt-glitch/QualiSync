<?php

namespace App\Services\Quality;

use Illuminate\Support\Facades\DB;

class OperatorDashboardStatsService
{
    public function counts(): array
    {
        $fgInspections = DB::table('fg_inspections')
            ->whereDate('created_at', now()->toDateString())
            ->select('tanggal', 'no_batch', 'no_mm', 'shift', 'line_produksi');

        $qirInspections = DB::table('qir_records')
            ->whereDate('created_at', now()->toDateString())
            ->select('tanggal', 'no_batch', 'no_mm', 'shift', 'line_produksi');

        $inProsesCount = DB::query()
            ->fromSub($fgInspections->union($qirInspections), 'daily_inspections')
            ->count();

        $coaCount = DB::table('qa_coas')
            ->where('created_at', '>=', now()->startOfMonth())
            ->where('created_at', '<=', now())
            ->count();

        return compact('inProsesCount', 'coaCount');
    }
}
