<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\Auth;

class DepartemenPrController extends Controller
{
    // 1. Menampilkan Form & Riwayat PR Departemen
    public function index()
    {
        // Ambil role user yang sedang login (engineering, quality, hrd_ga, development)
        $departemen = Auth::user()->role; 
        
        // Ambil riwayat PR khusus milik departemen ini saja
        $riwayatPr = DB::table('purchase_requests')
            ->leftJoin('master_materials', 'purchase_requests.no_mm', '=', 'master_materials.no_mm')
            ->select('purchase_requests.*', 'master_materials.nama_material')
            ->where('departemen', $departemen)
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil daftar material untuk pilihan di dropdown form
        $materials = DB::table('master_materials')->get();

        return view('departemen.pr-index', compact('riwayatPr', 'materials', 'departemen'));
    }

    // 2. Menyimpan PR Baru
    public function store(Request $request)
    {
        $request->validate([
            'no_mm' => 'required',
            'qty' => 'required|numeric|min:1',
            'estimasi_tiba' => 'required|date',
            'kategori' => 'required|string',
            'catatan' => 'required|string'
        ]);

        $noPr = 'PR-' . date('YmdHi') . '-' . rand(100, 999);
        
        // KUNCI UTAMA: Stempel departemen diambil OTOMATIS dari role user yang login!
        $departemenStempel = Auth::user()->role; 

        PurchaseRequest::create([
            'no_pr' => $noPr,
            'tanggal' => date('Y-m-d'),
            'no_mm' => $request->no_mm,
            'qty' => $request->qty,
            'estimasi_tiba' => $request->estimasi_tiba,
            'kategori' => $request->kategori,
            'status' => 'Pending',
            'status_approval' => 'Pending',
            'pemohon_id' => Auth::id() ?? 1,
            'catatan' => $request->catatan,
            'departemen' => $departemenStempel, // <--- STEMPEL OTOMATIS MENEMPEL DI SINI
        ]);

        return redirect()->back()->with('success', 'Purchase Request berhasil dikirim ke Purchasing!');
    }
}