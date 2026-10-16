<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use App\Services\Quality\OperatorDashboardStatsService;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = strtolower($user->role ?? '');
        $departemen = strtolower($user->departemen ?? '');

        // =========================================================
        // PINTU KHUSUS DIREKSI & MANAGER PLAN (REDIRECT)
        // =========================================================
        if ($role === 'direktur' || $role === 'presiden_direktur') {
            return redirect('/qualisync/purchasing/approval-po'); 
        }
        if ($role === 'manager_plan') {
            return redirect('/qualisync/purchasing/purchase-request');
        }

        // =========================================================
        // KELOMPOK 1: ADMIN UTAMA (Dashboard "Helicopter View")
        // =========================================================
        if ($role === 'admin') {
            $totalCoa = DB::table('coas')->count();
            
            $capaTerbuka = DB::table('capa_customers')
                            ->whereIn('status', ['Open', 'Draft', 'Pending', 'In Progress']) 
                            ->count();
                            
            $capaSelesai = DB::table('capa_customers')
                            ->where('status', 'Closed') 
                            ->count();
                            
            $prMenunggu = DB::table('purchase_requests')
                            ->whereIn('status', ['Pending', 'Menunggu Approval']) 
                            ->count();
                            
            $woAktif = DB::table('work_orders')
                            ->whereIn('status', ['On Progress', 'In Progress', 'In Production'])
                            ->count();

            $paretoData = DB::table('fg_inspection_details')
                            ->whereNotNull('defect')
                            ->where('defect', '!=', '')
                            ->select(
                                'defect as jenis_reject',
                                DB::raw('SUM(COALESCE(critical, 0) + COALESCE(major, 0) + COALESCE(minor, 0)) as total')
                            )
                            ->groupBy('defect')
                            ->having('total', '>', 0)
                            ->orderByDesc('total')
                            ->limit(5)
                            ->get();

            $waktuDashboard = now()->locale('id')->translatedFormat('l, d F Y, H:i') . ' WIB';

            return view('dashboards.admin', compact(
                'user', 'totalCoa', 'capaTerbuka', 'capaSelesai', 'prMenunggu', 'woAktif',
                'paretoData', 'waktuDashboard'
            ));
        } 
        
        // =========================================================
        // KELOMPOK 2: OPERATOR
        // =========================================================
        if (str_contains($role, 'operator')) {
            $dashboardStats = $role === 'operator_quality'
                ? app(OperatorDashboardStatsService::class)->counts()
                : [];

            return view('dashboards.operator', compact('user', 'departemen', 'dashboardStats'));
        }

        // =========================================================
        // KELOMPOK 3: STAFF
        // =========================================================
        return view('dashboards.staff', compact('user', 'departemen'));
    }

    public function operatorQualityStats(OperatorDashboardStatsService $statsService)
    {
        return response()->json($statsService->counts());
    }
}