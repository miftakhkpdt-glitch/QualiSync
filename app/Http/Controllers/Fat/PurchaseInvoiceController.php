<?php

namespace App\Http\Controllers\Fat;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PurchaseInvoiceController extends Controller
{
    public function index()
    {
        // Tarik data dari incoming_materials yang sudah Pass QC tapi belum ditagih
        $antreanPenerimaan = DB::table('incoming_materials')
            ->leftJoin('purchase_invoices', 'incoming_materials.id', '=', 'purchase_invoices.incoming_material_id')
            
            // Aturan Bisnis: Hanya bayar barang yang Lulus QC
            ->where('incoming_materials.status_qc', 'Pass')
            
            // Hanya tampilkan yang belum dibuatkan faktur pembeliannya
            ->whereNull('purchase_invoices.id')
            
            ->select(
                'incoming_materials.id as penerimaan_id',
                'incoming_materials.sj_supplier_number',
                'incoming_materials.date as tanggal_terima',
                'incoming_materials.vendor_name',
                'incoming_materials.po_kpdt_number'
            )
            ->get();

        return view('fat.purchase_invoices.index', compact('antreanPenerimaan'));
    }
    
    public function store(Request $request)
    {
        // 1. Validasi data yang diinput staf
        $request->validate([
            'incoming_material_id' => 'required',
            'no_invoice_supplier'  => 'required|string|max:100',
            'tanggal_invoice'      => 'required|date',
            'jatuh_tempo'          => 'required|date',
            'subtotal'             => 'required|numeric',
            'ppn'                  => 'required|numeric',
            'total_tagihan'        => 'required|numeric',
        ]);

        // 2. Simpan data ke tabel purchase_invoices
        DB::table('purchase_invoices')->insert([
            'incoming_material_id'  => $request->incoming_material_id,
            'no_invoice_supplier'   => $request->no_invoice_supplier,
            'tanggal_invoice'       => $request->tanggal_invoice,
            'jatuh_tempo'           => $request->jatuh_tempo,
            'subtotal'              => $request->subtotal,
            'ppn'                   => $request->ppn,
            'total_tagihan'         => $request->total_tagihan,
            'status_pembayaran'     => 'Unpaid', // Default belum dibayar
            'created_at'            => \Carbon\Carbon::now(),
            'updated_at'            => \Carbon\Carbon::now(),
        ]);

        // 3. Kembalikan ke halaman Antrean dengan pesan sukses
        return redirect()->route('fat.purchase_invoices.index')
                         ->with('success', 'Faktur Pembelian berhasil dicatat! Hutang supplier telah ditambahkan ke sistem.');
    }
    public function history()
    {
        // Menarik data riwayat hutang dan menyambungkannya ke tabel gudang untuk dapat nama Vendor
        $invoices = DB::table('purchase_invoices')
            ->join('incoming_materials', 'purchase_invoices.incoming_material_id', '=', 'incoming_materials.id')
            ->select(
                'purchase_invoices.id',
                'purchase_invoices.no_invoice_supplier',
                'purchase_invoices.tanggal_invoice',
                'purchase_invoices.jatuh_tempo',
                'purchase_invoices.total_tagihan',
                'purchase_invoices.status_pembayaran',
                'incoming_materials.vendor_name'
            )
            ->orderBy('purchase_invoices.created_at', 'desc')
            ->get();

        return view('fat.purchase_invoices.history', compact('invoices'));
    }
    
    public function markAsPaid($id)
    {
        // 1. Tarik data invoice untuk mendapatkan Nominal dan Nomor Invoice
        $invoice = DB::table('purchase_invoices')->where('id', $id)->first();

        if ($invoice && $invoice->status_pembayaran != 'Paid') {
            
            // 2. Ubah status Hutang menjadi Paid
            DB::table('purchase_invoices')
                ->where('id', $id)
                ->update([
                    'status_pembayaran' => 'Paid',
                    'updated_at'        => \Carbon\Carbon::now()
                ]);

            // 3. AUTO-JURNAL: Catat Uang Keluar ke Buku Kas & Bank
            DB::table('cash_bank_ledgers')->insert([
                'tanggal'    => \Carbon\Carbon::now()->format('Y-m-d'),
                'keterangan' => 'Pelunasan Hutang Vendor - Inv: ' . $invoice->no_invoice_supplier,
                'tipe'       => 'Keluar',
                'nominal'    => $invoice->total_tagihan,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ]);
        }

        return redirect()->route('fat.purchase_invoices.history')
                         ->with('success', 'Pembayaran berhasil! Saldo Kas & Bank telah otomatis dikurangi.');
    }
    public function create($id)
    {
        // Kodingan bersih: Langsung JOIN karena kolom 'no_po' sudah ada
        $data = DB::table('incoming_materials')
            ->leftJoin('purchase_orders', 'incoming_materials.po_kpdt_number', '=', 'purchase_orders.no_po')
            ->where('incoming_materials.id', $id)
            ->select(
                'incoming_materials.id as penerimaan_id',
                'incoming_materials.sj_supplier_number',
                'incoming_materials.date as tanggal_terima',
                'incoming_materials.vendor_name',
                'incoming_materials.po_kpdt_number',
                'incoming_materials.item_name',
                'incoming_materials.quantity',
                'purchase_orders.harga_satuan'
            )
            ->first();

        if (!$data) {
            return redirect()->route('fat.purchase_invoices.index')->with('error', 'Data penerimaan tidak ditemukan.');
        }

        $hargaSatuan = $data->harga_satuan ?? 0;
        $subtotal = $data->quantity * $hargaSatuan;

        return view('fat.purchase_invoices.create', compact('data', 'subtotal'));
    }

    public function show($id)
    {
        // Kodingan bersih: Join ke Gudang lalu tembus ke PO
        $invoice = DB::table('purchase_invoices')
            ->join('incoming_materials', 'purchase_invoices.incoming_material_id', '=', 'incoming_materials.id')
            ->leftJoin('purchase_orders', 'incoming_materials.po_kpdt_number', '=', 'purchase_orders.no_po')
            ->where('purchase_invoices.id', $id)
            ->select(
                'purchase_invoices.*',
                'incoming_materials.sj_supplier_number',
                'incoming_materials.date as tanggal_terima',
                'incoming_materials.vendor_name',
                'incoming_materials.po_kpdt_number',
                'incoming_materials.item_name',
                'incoming_materials.quantity',
                'purchase_orders.harga_satuan'
            )
            ->first();

        if (!$invoice) {
            return redirect()->route('fat.purchase_invoices.history')->with('error', 'Detail tagihan tidak ditemukan.');
        }

        return view('fat.purchase_invoices.show', compact('invoice'));
    }
    public function print($id)
    {
        // Kodingan bersih: Join ke Gudang lalu tembus ke PO
        $invoice = DB::table('purchase_invoices')
            ->join('incoming_materials', 'purchase_invoices.incoming_material_id', '=', 'incoming_materials.id')
            ->leftJoin('purchase_orders', 'incoming_materials.po_kpdt_number', '=', 'purchase_orders.no_po')
            ->where('purchase_invoices.id', $id)
            ->select(
                'purchase_invoices.*',
                'incoming_materials.sj_supplier_number',
                'incoming_materials.date as tanggal_terima',
                'incoming_materials.vendor_name',
                'incoming_materials.po_kpdt_number',
                'incoming_materials.item_name',
                'incoming_materials.quantity',
                'purchase_orders.harga_satuan'
            )
            ->first();

        if (!$invoice) {
            return redirect()->route('fat.purchase_invoices.history')->with('error', 'Detail tagihan tidak ditemukan.');
        }

        return view('fat.purchase_invoices.print', compact('invoice'));
    }
}