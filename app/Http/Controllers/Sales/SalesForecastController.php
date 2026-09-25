<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesForecast;

class SalesForecastController extends Controller
{
    // 1. Menampilkan Daftar Forecast
    public function index()
    {
        // 1. Ambil semua data mentah yang aktif
        $rawForecasts = \App\Models\SalesForecast::where('status', 'Aktif')
                        ->orderBy('tahun', 'desc')
                        ->orderBy('bulan', 'asc')
                        ->get();

        // 2. Kelompokkan data berdasarkan Customer, Produk, dan Tahun
        // Agar tidak muncul berulang-ulang ke bawah
        $groupedForecasts = $rawForecasts->groupBy(function ($item) {
            // Kita gabungkan data identitasnya menggunakan pemisah khusus '|||'
            return $item->nama_customer . '|||' . $item->no_mm . '|||' . $item->nama_produk . '|||' . $item->tahun . '|||' . $item->versi;
        });
                        
        return view('sales.forecast.index', compact('groupedForecasts'));
    }

    // 2. Menampilkan Form Input Forecast Baru
    public function create()
    {
        // Tarik data pelanggan dari Master Customer untuk ditampilkan di dropdown
        $customers = \App\Models\MasterCustomer::orderBy('nama_customer', 'asc')->get();
        
        return view('sales.forecast.create', compact('customers'));
    }

    // 3. Menyimpan Data Forecast ke Database
    // FUNGSI BARU: Mencari nama produk di Master Data
    public function getProduk($mm)
    {
        try {
            // Ubah 'mm' menjadi 'no_mm' (atau sesuaikan dengan nama kolom asli di DB Anda)
            $produk = \Illuminate\Support\Facades\DB::table('master_items')->where('no_mm', $mm)->first();
            
            if(!$produk){
                $produk = \Illuminate\Support\Facades\DB::table('master_materials')->where('no_mm', $mm)->first();
            }

            if ($produk) {
                // Mengambil nama produk/material (mencoba beberapa kemungkinan nama kolom)
                $nama = $produk->item_name ?? $produk->nama_material ?? $produk->nama_produk ?? $produk->nama_item ?? 'Nama kolom tidak cocok';
                return response()->json(['success' => true, 'nama_produk' => $nama]);
            }

            return response()->json(['success' => false, 'message' => 'MM tidak ditemukan di database']);
            
        } catch (\Exception $e) {
            // Jika ada kolom/tabel yang salah, tampilkan pesan error aslinya
            return response()->json(['success' => false, 'message' => 'Error DB: ' . $e->getMessage()]);
        }
    }

    // UPDATE FUNGSI STORE: Untuk menyimpan multi-bulan (array)
    public function store(Request $request)
    {
        $request->validate([
            'nama_customer' => 'required',
            'no_mm'         => 'required',
            'nama_produk'   => 'required',
            'bulan'         => 'required|array', // Sekarang berupa array
            'tahun'         => 'required|array',
            'qty_forecast'  => 'required|array',
        ]);

        // Looping untuk menyimpan setiap baris bulan yang diinputkan
        foreach ($request->bulan as $key => $bulan) {
            // Pastikan qty-nya diisi
            if (!empty($bulan) && !empty($request->qty_forecast[$key])) {
                \App\Models\SalesForecast::create([
                    'nama_customer' => $request->nama_customer,
                    'no_mm'         => $request->no_mm,
                    'nama_produk'   => $request->nama_produk,
                    'bulan'         => $bulan,
                    'tahun'         => $request->tahun[$key],
                    'qty_forecast'  => $request->qty_forecast[$key],
                    'satuan'        => 'Pcs',
                    'versi'         => 1,
                    'status'        => 'Aktif'
                ]);
            }
        }

        return redirect()->route('sales.forecast.index')->with('success', 'Data Forecast multi-bulan berhasil disimpan!');
    }
    // 4. Menampilkan Form Revisi (Menarik data versi lama)
    public function revisi($id)
    {
        $baseForecast = \App\Models\SalesForecast::findOrFail($id);
        
        // Ambil semua bulan untuk Customer, MM, dan Tahun yang sama pada Versi aktif ini
        $forecasts = \App\Models\SalesForecast::where('nama_customer', $baseForecast->nama_customer)
                        ->where('no_mm', $baseForecast->no_mm)
                        ->where('tahun', $baseForecast->tahun)
                        ->where('versi', $baseForecast->versi)
                        ->where('status', 'Aktif')
                        ->get();

        return view('sales.forecast.revisi', compact('baseForecast', 'forecasts'));
    }

    // 5. Menyimpan Data Revisi (Membuat Versi Baru)
    public function storeRevisi(Request $request, $id)
    {
        $baseForecast = \App\Models\SalesForecast::findOrFail($id);
        
        $request->validate([
            'alasan_revisi' => 'required',
            'bulan'         => 'required|array',
            'qty_forecast'  => 'required|array',
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            // 1. Nonaktifkan versi yang lama (Ubah status menjadi 'Direvisi' agar masuk arsip)
            \App\Models\SalesForecast::where('nama_customer', $baseForecast->nama_customer)
                ->where('no_mm', $baseForecast->no_mm)
                ->where('tahun', $baseForecast->tahun)
                ->where('versi', $baseForecast->versi)
                ->update(['status' => 'Direvisi']);

            // 2. Simpan data yang baru sebagai Versi + 1
            $newVersi = $baseForecast->versi + 1;

            foreach ($request->bulan as $key => $bulan) {
                if (!empty($bulan) && !empty($request->qty_forecast[$key])) {
                    \App\Models\SalesForecast::create([
                        'nama_customer' => $baseForecast->nama_customer, // Data master tetap sama
                        'no_mm'         => $baseForecast->no_mm,
                        'nama_produk'   => $baseForecast->nama_produk,
                        'bulan'         => $bulan,
                        'tahun'         => $request->tahun[$key],
                        'qty_forecast'  => $request->qty_forecast[$key],
                        'satuan'        => 'Pcs',
                        'versi'         => $newVersi,
                        'alasan_revisi' => $request->alasan_revisi, // Mencatat mengapa direvisi
                        'status'        => 'Aktif' // Ini yang akan muncul di tabel Index
                    ]);
                }
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('sales.forecast.index')->with('success', 'Revisi berhasil disimpan! Sekarang menjadi Versi ' . $newVersi);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}