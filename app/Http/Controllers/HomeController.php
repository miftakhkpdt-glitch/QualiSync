<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 

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
                            ->where('status', 'In Progress') 
                            ->count();

            return view('dashboards.admin', compact(
                'user', 'totalCoa', 'capaTerbuka', 'capaSelesai', 'prMenunggu', 'woAktif'
            ));
        } 
        
        // =========================================================
// KELOMPOK 2: OPERATOR (Selalu memanggil Cangkang dashboards.operator)
// =========================================================
if (str_contains($role, 'operator')) {
    return view('dashboards.operator', compact('user', 'departemen'));
}

// =========================================================
// KELOMPOK 3: STAFF (Selalu memanggil Cangkang dashboards.staff)
// =========================================================
return view('dashboards.staff', compact('user', 'departemen')); 
    }
}