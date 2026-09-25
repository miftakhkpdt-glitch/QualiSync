<?php

namespace App\Http\Controllers\PPIC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkOrder;

class WorkOrderController extends Controller
{
    public function index()
    {
        // 1. Ambil semua data WO untuk ditampilkan di tabel
        $workOrders = \App\Models\WorkOrder::with('salesOrder')->orderBy('created_at', 'desc')->get();

        // 2. RUMUS MENGHITUNG KOTAK DASHBOARD
        // Kita gabungkan status 'Pending' dan 'Menunggu Material' ke dalam kotak "Belum Mulai"
        $woPending = \App\Models\WorkOrder::whereIn('status', ['Pending', 'Menunggu Material'])->count();
        
        // Hitung yang sedang jalan di produksi
        $woProses = \App\Models\WorkOrder::where('status', 'On Progress')->count();
        
        // Hitung yang sudah selesai
        $woSelesai = \App\Models\WorkOrder::where('status', 'Completed')->count();

        // 3. Kirim data dan hasil hitungan ke tampilan (Blade)
        return view('ppic.work_order.index', compact('workOrders', 'woPending', 'woProses', 'woSelesai'));
    }
    public function requestMaterial($id)
    {
        // 1. Ambil data WO beserta relasi SO-nya
        $wo = WorkOrder::with('salesOrder')->findOrFail($id);

        // Pastikan WO memiliki nomor MM Barang Jadi (Finish Goods)
        $fg_mm = $wo->salesOrder->no_mm;
        $targetQty = $wo->salesOrder->qty;

        if (!$fg_mm) {
            return redirect()->back()->with('error', 'Gagal! Nomor MM (Finish Goods) belum diisi pada Sales Order.');
        }

        // 2. Lakukan BOM Explosion (Kalikan resep dengan target pesanan)
        $kebutuhanMaterial = \App\Models\ProductBom::where('fg_mm', $fg_mm)->get()->map(function ($bom) use ($targetQty) {
            // Hitung total material yang dibutuhkan
            $bom->total_kebutuhan = $bom->qty_usage * $targetQty;
            
            // Opsional: Coba ambil nama material dari tabel master jika ada
            // (Sesuaikan dengan nama tabel master item Anda jika perlu)
            $master = \Illuminate\Support\Facades\DB::table('master_materials')->where('no_mm', $bom->component_mm)->first();
            $bom->nama_komponen = $master ? $master->nama_material : '-';

            return $bom;
        });

        return view('ppic.work_order.request_material', compact('wo', 'kebutuhanMaterial', 'targetQty'));
    }
    // 1. Menampilkan Form Input WO
    public function create()
    {
        // Ambil data Sales Order (Pesanan) beserta Nama Materialnya
        $salesOrders = \Illuminate\Support\Facades\DB::table('sales_orders')
            ->leftJoin('master_materials', 'sales_orders.no_mm', '=', 'master_materials.no_mm')
            ->select('sales_orders.*', 'master_materials.nama_material')
            ->whereNull('sales_orders.deleted_at')
            ->orderBy('sales_orders.tanggal_po', 'desc')
            ->get();

        return view('ppic.work_order.create', compact('salesOrders'));
    }

    // 2. Menyimpan Data WO Baru
    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'sales_order_id' => 'required',
            'qty_target' => 'required|numeric|min:1',
            'start_date' => 'required|date'
        ]);

        // Cari tahu 'no_mm' dari Sales Order yang dipilih
        $so = \Illuminate\Support\Facades\DB::table('sales_orders')->where('id', $request->sales_order_id)->first();

        // Generate Nomor WO Otomatis (Contoh: WO-20260822-1234)
        $noWo = 'WO-' . date('Ymd') . '-' . rand(1000, 9999);

        \App\Models\WorkOrder::create([
            'sales_order_id' => $request->sales_order_id,
            'no_mm' => $so->no_mm,
            'qty_target' => $request->qty_target,
            'qty_good' => 0,      // Awal produksi selalu 0
            'qty_reject' => 0,    // Awal produksi selalu 0
            'no_wo' => $noWo,
            'start_date' => $request->start_date,
            'status' => 'Pending' // Status awal
        ]);

        return redirect('/ppic/work-order')->with('success', 'Luar biasa! Work Order berhasil diterbitkan.');
    }
    // Mengeksekusi permintaan material ke Gudang
    public function sendRequestToWarehouse($id)
    {
        // Cari data WO berdasarkan ID
        $wo = \App\Models\WorkOrder::findOrFail($id);
        
        // Ubah statusnya agar tim Gudang tahu ada order masuk
        $wo->status = 'Menunggu Material'; 
        $wo->save();

        // (Opsional) Nanti di sini kita bisa tambahkan kode untuk mengirim notifikasi ke Gudang

        return redirect('/ppic/work-order')->with('success', 'Hebat! Permintaan material untuk WO-'.$wo->no_wo.' telah berhasil dikirim ke Gudang.');
    }
}