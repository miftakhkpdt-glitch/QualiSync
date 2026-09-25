<?php

namespace App\Http\Controllers\Fat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Gunakan Query Builder agar mudah

class SalesInvoiceController extends Controller
{
    public function index()
{
    // Mengambil data dari database sesuai dengan struktur tabel Kimpai Dyna Tubes
    $antreanSuratJalan = DB::table('warehouse_outgoings')
        // Join menggunakan 'no_po' (karena di database Anda relasinya pakai nomor PO)
        ->join('sales_orders', 'warehouse_outgoings.no_po', '=', 'sales_orders.no_po')
        
        // Cek apakah sudah ada di tabel invoice
        ->leftJoin('sales_invoices', 'warehouse_outgoings.id', '=', 'sales_invoices.warehouse_outgoing_id')
        
        // Kondisi: Hanya tampilkan yang sudah di-Approve dan BELUM ditagih
        ->where('warehouse_outgoings.status', 'Approved')
        ->whereNull('sales_invoices.id')
        
        // Pilih kolom yang persis ada di database Anda
        ->select(
            'warehouse_outgoings.id as sj_id',
            'warehouse_outgoings.tanggal_pengiriman', // Sesuai database
            'sales_orders.no_po',                     // Sesuai database
            'sales_orders.nama_customer'              // Sesuai database
        )
        ->get();

    return view('fat.invoices.index', compact('antreanSuratJalan'));
  }
  public function create($sj_id)
    {
        // 1. Tarik data Surat Jalan beserta PO Referensinya
        $data = DB::table('warehouse_outgoings')
            ->join('sales_orders', 'warehouse_outgoings.no_po', '=', 'sales_orders.no_po')
            ->where('warehouse_outgoings.id', $sj_id)
            ->select(
                'warehouse_outgoings.id as sj_id',
                'warehouse_outgoings.tanggal_pengiriman',
                'warehouse_outgoings.qty_kirim', // Qty dari Gudang
                'sales_orders.no_po',
                'sales_orders.nama_customer',
                'sales_orders.nama_produk',
                'sales_orders.price' // Harga dari Sales
            )
            ->first();

        // Jika data tidak ditemukan, kembalikan ke halaman sebelumnya
        if (!$data) {
            return redirect()->route('fat.invoices.index')->with('error', 'Data Surat Jalan tidak ditemukan.');
        }

        // 2. Hitung Subtotal Otomatis (Qty Kirim x Harga PO)
        $subtotal = $data->qty_kirim * $data->price;

        // 3. Tampilkan halaman Form Invoice dengan membawa data yang sudah dihitung
        return view('fat.invoices.create', compact('data', 'subtotal'));
    }
    public function store(Request $request)
    {
        // 1. Validasi keamanan data yang masuk
        $request->validate([
            'warehouse_outgoing_id' => 'required',
            'no_invoice'          => 'required',
            'tanggal_invoice'     => 'required|date',
            'jatuh_tempo'         => 'required|date',
            'subtotal'            => 'required|numeric',
            'ppn'                 => 'required|numeric',
            'total_tagihan'       => 'required|numeric',
        ]);

        // 2. Masukkan data ke tabel sales_invoices
        DB::table('sales_invoices')->insert([
            'no_invoice'            => $request->no_invoice,
            'warehouse_outgoing_id' => $request->warehouse_outgoing_id,
            'tanggal_invoice'       => $request->tanggal_invoice,
            'jatuh_tempo'           => $request->jatuh_tempo,
            'subtotal'              => $request->subtotal,
            'ppn'                   => $request->ppn,
            'total_tagihan'         => $request->total_tagihan,
            'status_pembayaran'     => 'Unpaid', // Default selalu Unpaid saat pertama dibuat
            
            // Catat waktu pembuatan
            'created_at'            => \Carbon\Carbon::now(),
            'updated_at'            => \Carbon\Carbon::now(),
            
            // (Opsional) Jika Anda pakai fitur Login Laravel:
            // 'created_by' => auth()->user()->name,
        ]);

        // 3. Kembalikan user ke halaman Antrean dengan pesan sukses
        return redirect()->route('fat.invoices.index')
                         ->with('success', 'Faktur Penjualan (Invoice) berhasil diterbitkan!');
    }
    public function history()
    {
        // Menarik data invoice dan menyambungkannya ke tabel Surat Jalan & PO untuk dapat nama Customer
        $invoices = DB::table('sales_invoices')
            ->join('warehouse_outgoings', 'sales_invoices.warehouse_outgoing_id', '=', 'warehouse_outgoings.id')
            ->join('sales_orders', 'warehouse_outgoings.no_po', '=', 'sales_orders.no_po')
            ->select(
                'sales_invoices.id',
                'sales_invoices.no_invoice',
                'sales_invoices.tanggal_invoice',
                'sales_invoices.jatuh_tempo',
                'sales_invoices.total_tagihan',
                'sales_invoices.status_pembayaran',
                'sales_orders.nama_customer'
            )
            ->orderBy('sales_invoices.created_at', 'desc') // Urutkan dari yang terbaru
            ->get();

        return view('fat.invoices.history', compact('invoices'));
    }
    public function print($id)
    {
        // Menarik data lengkap 1 invoice beserta relasinya
        $invoice = DB::table('sales_invoices')
            ->join('warehouse_outgoings', 'sales_invoices.warehouse_outgoing_id', '=', 'warehouse_outgoings.id')
            ->join('sales_orders', 'warehouse_outgoings.no_po', '=', 'sales_orders.no_po')
            ->where('sales_invoices.id', $id)
            ->select(
                'sales_invoices.*', // Ambil semua data invoice
                'warehouse_outgoings.id as sj_id',
                'warehouse_outgoings.qty_kirim',
                'warehouse_outgoings.tanggal_pengiriman',
                'sales_orders.nama_customer',
                'sales_orders.no_po',
                'sales_orders.nama_produk',
                'sales_orders.price'
            )
            ->first();

        // Jika data tidak ada (misal ID salah), kembalikan ke riwayat
        if (!$invoice) {
            return redirect()->route('fat.invoices.history')->with('error', 'Invoice tidak ditemukan.');
        }

        return view('fat.invoices.print', compact('invoice'));
    }
    public function show($id)
    {
        // Menarik data lengkap 1 invoice beserta relasinya
        $invoice = DB::table('sales_invoices')
            ->join('warehouse_outgoings', 'sales_invoices.warehouse_outgoing_id', '=', 'warehouse_outgoings.id')
            ->join('sales_orders', 'warehouse_outgoings.no_po', '=', 'sales_orders.no_po')
            ->where('sales_invoices.id', $id)
            ->select(
                'sales_invoices.*', 
                'warehouse_outgoings.id as sj_id',
                'warehouse_outgoings.qty_kirim',
                'warehouse_outgoings.tanggal_pengiriman',
                'sales_orders.nama_customer',
                'sales_orders.no_po',
                'sales_orders.nama_produk',
                'sales_orders.price'
            )
            ->first();

        // Jika data tidak ada
        if (!$invoice) {
            return redirect()->route('fat.invoices.history')->with('error', 'Invoice tidak ditemukan.');
        }

        return view('fat.invoices.show', compact('invoice'));
    }
    public function markAsPaid($id)
    {
        // Update status_pembayaran menjadi 'Paid' di database
        // AUTO-JURNAL: Catat Uang Masuk ke Buku Kas & Bank
            DB::table('cash_bank_ledgers')->insert([
                'tanggal'    => \Carbon\Carbon::now()->format('Y-m-d'),
                'keterangan' => 'Penerimaan Pembayaran Customer - Inv: ' . $invoice->no_invoice, // Sesuaikan nama variabel invoice Anda
                'tipe'       => 'Masuk',
                'nominal'    => $invoice->total_tagihan, // Sesuaikan nama field nominal Anda
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ]);

        // Kembalikan ke halaman riwayat dengan pesan sukses
        return redirect()->route('fat.invoices.history')
                         ->with('success', 'Pembayaran berhasil dicatat! Status tagihan kini menjadi LUNAS.');
    }
}