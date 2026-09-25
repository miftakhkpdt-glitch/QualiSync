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

        // --- 5. DATA REAL DARI TABEL `fg_inspections` & `fg_inspection_details` UNTUK GRAFIK ---
        $tahunIni = now()->year;
        $bulanIni = now()->month;

        $labels = ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'];
        $totalInspeksi = [];
        $totalReject = [];

        $namaTabelFG = 'fg_inspections'; 

        for ($i = 1; $i <= 4; $i++) {
            $startDay = ($i - 1) * 7 + 1;
            $endDay = ($i * 7);
            
            if ($i == 4) {
                $endDay = now()->daysInMonth;
            }

            $startDate = sprintf('%04d-%02d-%02d 00:00:00', $tahunIni, $bulanIni, max(1, min($startDay, now()->daysInMonth)));
            $endDate = sprintf('%04d-%02d-%02d 23:59:59', $tahunIni, $bulanIni, max(1, min($endDay, now()->daysInMonth)));

            // Hitung Total Inspeksi FG berdasarkan rentang tanggal `created_at`
            $inspeksiCount = DB::table($namaTabelFG)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            // Hitung Total Reject / NG menggunakan kolom `decision` pada tabel `fg_inspection_details`
            $rejectCount = DB::table('fg_inspection_details')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where(function($q) {
                    $q->where('decision', 'NG')
                      ->orWhere('decision', 'Reject')
                      ->orWhere('decision', 'REJECT')
                      ->orWhere('decision', 'BLOCKED');
                })
                ->count();

            $totalInspeksi[] = $inspeksiCount;
            $totalReject[] = $rejectCount;
        }

        return view('quality.dashboard-qa', compact(
            'pendingIncoming', 
            'qirHariIni', 
            'capaTerbuka', 
            'tugasSegera',
            'labels',
            'totalInspeksi',
            'totalReject'
        ));
    }
}