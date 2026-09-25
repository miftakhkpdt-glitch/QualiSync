<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkOrderController extends Controller
{
    public function index()
    {
        // Tarik data Work Order, gabungkan dengan Master Material agar nama produknya muncul
        $workOrders = DB::table('work_orders')
            ->leftJoin('master_materials', 'work_orders.no_mm', '=', 'master_materials.no_mm')
            ->select('work_orders.*', 'master_materials.nama_material')
            ->orderBy('work_orders.created_at', 'desc')
            ->get();

        return view('warehouse.work-order', compact('workOrders'));
    }
}