<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesOrderController extends Controller
{
    public function create(Request $request) 
    {
        $materials = DB::table('master_materials')->get();
        $priceTiers = DB::table('material_price_tiers')->get(); 

        // 1. Tangkap parameter filter dari URL
        $filterTahun = $request->input('tahun', date('Y')); // Default tahun ini
        $filterBulan = $request->input('bulan'); 
        $filterItem  = $request->input('item');

        // 2. Query data riwayat dari model SalesOrder YANG MASIH AKTIF (Belum dihapus)
        $query = SalesOrder::query()->whereNull('deleted_at');

        // 3. Terapkan filter jika dipilih
        if ($filterTahun) {
            $query->whereYear('tanggal_po', $filterTahun);
        }
        if ($filterBulan) {
            $query->whereMonth('tanggal_po', $filterBulan);
        }
        if ($filterItem) {
            $query->where(function($q) use ($filterItem) {
                $q->where('no_mm', 'like', "%{$filterItem}%")
                  ->orWhere('nama_produk', 'like', "%{$filterItem}%");
            });
        }

        // 4. Ambil data yang sudah difilter, urutkan dari yang terbaru
        $riwayatPO = $query->orderBy('tanggal_po', 'desc')->get();
        
        // 5. Ambil data PO yang SUDAH DIHAPUS (Riwayat Hapus)
        $deletedPO = SalesOrder::query()
                    ->whereNotNull('deleted_at')
                    ->orderBy('deleted_at', 'desc')
                    ->get();

        // 6. Kirim semua data ke view
        return view('sales.po-input', compact('materials', 'priceTiers', 'riwayatPO', 'deletedPO', 'filterTahun', 'filterBulan', 'filterItem'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_customer' => 'required',
            'tanggal_po' => 'required|date',
            'no_po' => 'required',
            'delivery_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.no_mm' => 'required',
            'items.*.nama_item' => 'required',
            'items.*.price' => 'required|numeric',
            'items.*.qty' => 'required|numeric|min:1',
        ]);

        // Looping untuk menyimpan setiap baris item produk ke dalam database
        foreach ($request->items as $item) {
            $po = new SalesOrder();
            $po->nama_customer = $request->nama_customer;
            $po->tanggal_po    = $request->tanggal_po;
            $po->no_po         = $request->no_po;
            $po->delivery_date = $request->delivery_date;
            
            // =========================================================
            // INI TAMBAHAN KODE ITEM CUSTOMERNYA UNTUK CREATE BARU
            // =========================================================
            $po->no_mm              = $item['no_mm'];
            $po->kode_item_customer = $item['kode_item_customer'] ?? null; 
            
            $po->nama_produk   = $item['nama_item']; 
            $po->price         = $item['price'];
            $po->qty           = $item['qty'];
            $po->ospo          = $item['qty']; 
            $po->satuan        = 'Pcs';
            $po->status        = 'New';
            $po->created_by    = Auth::user()->name; 
            $po->save(); 
        }

        return redirect()->back()->with('success', 'Purchase Order beserta seluruh item berhasil disimpan!');
    }

    public function index()
    {
        // Mengambil semua data PO untuk dilihat PPIC
        $poList = SalesOrder::latest()->get();
        return view('ppic.dashboard-po', compact('poList'));
    }

    public function edit($id)
    {
        // Ambil data utama PO (berdasarkan nomor PO atau ID Header)
        $po = DB::table('sales_orders')->where('id', $id)->first();
        
        // Ambil semua detail item produk untuk PO tersebut
        $poDetails = DB::table('sales_order_details')->where('sales_order_id', $id)->get();
        
        $materials = DB::table('master_materials')->get();

        return view('sales.po-edit', compact('po', 'poDetails', 'materials'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_customer' => 'required',
            'tanggal_po' => 'required|date',
            'no_po' => 'required',
            'delivery_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items[0][no_mm]' => 'required',
            'items[0][nama_item]' => 'required',
            'items[0][price]' => 'required|numeric',
            'items[0][qty]' => 'required|numeric|min:1',
        ]);

        // Ambil item pertama dari form edit inline
        $item = $request->items[0];

        // Update data pada baris sales_orders berdasarkan ID yang dipilih
        $salesOrder = SalesOrder::findOrFail($id);
        $salesOrder->update([
            'nama_customer' => $request->nama_customer,
            'tanggal_po'    => $request->tanggal_po,
            'no_po'         => $request->no_po,
            'delivery_date' => $request->delivery_date,
            
            // =========================================================
            // INI TAMBAHAN KODE ITEM CUSTOMERNYA UNTUK UPDATE
            // =========================================================
            'no_mm'              => $item['no_mm'],
            'kode_item_customer' => $item['kode_item_customer'] ?? null,
            
            'nama_produk'   => $item['nama_item'],
            'price'         => $item['price'],
            'qty'           => $item['qty'],
            'ospo'          => $item['qty'],
            'updated_by'    => Auth::user()->name 
        ]);

        return redirect()->route('sales.po.input')->with('success', 'Data PO Customer berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $po = SalesOrder::findOrFail($id);
        
        $po->deleted_at = now();
        $po->deleted_by = Auth::user()->name ?? 'System';
        $po->save(); 
        
        return redirect()->back()->with('success', 'Data PO berhasil dihapus dan dipindahkan ke riwayat!');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids; 

        if ($ids && count($ids) > 0) {
            DB::table('sales_orders')->whereIn('id', $ids)->update([
                'deleted_at' => now(),
                'deleted_by' => Auth::user()->name ?? 'System'
            ]);
            
            return redirect()->back()->with('success', count($ids) . ' data PO berhasil dihapus sekaligus!');
        }

        return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk dihapus.');
    }

    public function ospo()
    {
        // Mengambil semua data PO, diurutkan dari yang terbaru
        $data_ospo = \App\Models\SalesOrder::orderBy('created_at', 'desc')->get();

        return view('sales.ospo', compact('data_ospo'));
    }
}