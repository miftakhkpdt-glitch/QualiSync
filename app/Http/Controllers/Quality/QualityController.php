<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- WAJIB DITAMBAHKAN AGAR BISA QUERY KE DATABASE

class QualityController extends Controller
{
    public function dashboard()
    {
        // 1. Hitung Pending Incoming QC 
        $pendingIncoming = DB::table('incoming_materials')
            ->where('status_qc', 'Pending')
            ->count();

        // 2. Hitung QIR Hari Ini
        $qirHariIni = DB::table('qir_records')
            ->whereDate('created_at', now()->toDateString())
            ->count();

        // 3. Hitung CAPA 8D Terbuka
        $capaTerbuka = DB::table('capa_customers')
            ->whereIn('status', ['Open', 'Pending', 'In Progress']) 
            ->count();

        // 4. Ambil Daftar Tugas Segera
        $tugasSegera = DB::table('quality_stocks')
            ->where('status', 'Waiting Approval')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // --- 5. TREN INSPEKSI FG DAN CATATAN NG PER MINGGU ---
        $awalBulan = now()->startOfMonth();
        $hariIni = now()->startOfDay();
        $jumlahHariDalamBulan = $awalBulan->daysInMonth;

        $labels = [];
        $totalInspeksi = [];
        $totalReject = [];
        $mingguBerjalan = [];

        for ($i = 1; $i <= 4; $i++) {
            $startDay = ($i - 1) * 7 + 1;
            $endDay = $i === 4 ? $jumlahHariDalamBulan : min($i * 7, $jumlahHariDalamBulan);
            $startDate = $awalBulan->copy()->day($startDay)->startOfDay();
            $endDate = $awalBulan->copy()->day($endDay)->endOfDay();
            $sudahDimulai = $startDate->lte($hariIni);

            $labels[] = $startDate->locale('id')->translatedFormat('j M') . ' - ' . $endDate->locale('id')->translatedFormat('j M');
            $mingguBerjalan[] = $sudahDimulai;

            if (!$sudahDimulai) {
                $totalInspeksi[] = null;
                $totalReject[] = null;
                continue;
            }

            // Satu inspeksi adalah satu header; tanggal mengikuti tanggal inspeksi.
            $inspeksiCount = DB::table('fg_inspections')
                ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
                ->count();

            // Satu NG adalah satu baris pemeriksaan detail dengan keputusan gagal.
            $rejectCount = DB::table('fg_inspection_details')
                ->join('fg_inspections', 'fg_inspection_details.inspection_id', '=', 'fg_inspections.id')
                ->whereBetween('fg_inspections.tanggal', [$startDate->toDateString(), $endDate->toDateString()])
                ->whereRaw('UPPER(fg_inspection_details.decision) IN (?, ?, ?)', ['NG', 'REJECT', 'BLOCKED'])
                ->count();

            $totalInspeksi[] = $inspeksiCount;
            $totalReject[] = $rejectCount;
        }

        $latestLogs = DB::query()
            ->fromSub($this->activityLogsQuery(), 'activity_logs')
            ->orderByDesc('occurred_at')
            ->limit(3)
            ->get();

        return view('quality.dashboard-qa', compact(
            'pendingIncoming', 
            'qirHariIni', 
            'capaTerbuka', 
            'tugasSegera',
            'labels',
            'totalInspeksi',
            'totalReject',
            'mingguBerjalan',
            'latestLogs'
        ));
    }

    public function activityLogs(Request $request)
    {
        $logsQuery = DB::query()->fromSub($this->activityLogsQuery(), 'activity_logs');

        if ($request->filled('search')) {
            $search = '%' . trim($request->input('search')) . '%';
            $logsQuery->where(function ($query) use ($search) {
                $query->where('event_type', 'like', $search)
                    ->orWhere('title', 'like', $search)
                    ->orWhere('description', 'like', $search);
            });
        }

        $logs = $logsQuery->orderByDesc('occurred_at')->paginate(30)->withQueryString();

        return view('quality.activity-logs', compact('logs'));
    }

    private function activityLogsQuery()
    {
        $coaLogs = DB::table('qa_coas')->selectRaw(
            "'COA' as event_type, 'COA Diterbitkan' as title, CONCAT(no_coa, ' - ', COALESCE(NULLIF(customer_name, ''), no_mm)) as description, created_at as occurred_at"
        );

        $qirLogs = DB::table('qir_records')
            ->leftJoin('master_materials', 'qir_records.no_mm', '=', 'master_materials.no_mm')
            ->selectRaw(
                "'QIR' as event_type, CONCAT('QIR ', COALESCE(qir_records.status, 'Dibuat')) as title, CONCAT(COALESCE(master_materials.nama_material, qir_records.no_mm), ' - Batch ', qir_records.no_batch) as description, qir_records.created_at as occurred_at"
            );

        $incomingLogs = DB::table('incoming_materials')
            ->whereNull('deleted_at')
            ->selectRaw(
                "'Incoming Material' as event_type, 'Incoming Material' as title, CONCAT(COALESCE(NULLIF(item_name, ''), mm), ' - ', COALESCE(vendor_name, 'Supplier'), ' (', COALESCE(stpb_number, '-'), ')') as description, created_at as occurred_at"
            );

        return $coaLogs->unionAll($qirLogs)->unionAll($incomingLogs);
    }
}