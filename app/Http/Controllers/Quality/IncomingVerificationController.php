<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IncomingVerificationController extends Controller
{
    public function index(Request $request)
    {
        // 1. Data untuk verifikasi (yang masih Pending)
        $incoming = DB::table('incoming_materials')
            ->leftJoin('users', 'incoming_materials.user_id', '=', 'users.id')
            ->select('incoming_materials.*', 'users.name as input_by')
            ->whereNull('incoming_materials.deleted_at')
            ->where('incoming_materials.status_qc', 'Pending')
            ->orderBy('incoming_materials.id', 'DESC')
            ->get();

        // 2. Data riwayat untuk yang sudah di-approve (Pass / Hold / NG)
        $riwayatApproval = DB::table('incoming_materials')
            ->leftJoin('users', 'incoming_materials.user_id', '=', 'users.id')
            ->select('incoming_materials.*', 'users.name as input_by')
            ->whereNull('incoming_materials.deleted_at')
            ->whereIn('incoming_materials.status_qc', ['Pass', 'Hold', 'NG'])
            ->orderBy('incoming_materials.updated_at', 'DESC')
            ->get();

        return view('quality.incoming.incoming-verification', compact('incoming', 'riwayatApproval'));
    }

    public function updateStatus(Request $request, $id)
    {
        $incomingItem = DB::table('incoming_materials')->where('id', $id)->first();

        if (!$incomingItem) {
            return back()->with('error', 'Data material tidak ditemukan.');
        }

        if ($incomingItem->status_qc !== 'Pending') {
            return back()->with('error', 'Data ini sudah diproses sebelumnya.');
        }

        // =========================================================
        // 1. PERSIAPAN DATA ROW SPLITTING (PEMISAHAN QTY)
        // =========================================================
        $qtyPass = $request->qty_pass ?? 0;
        $qtyHold = $request->qty_hold ?? 0;
        $qtyNg   = $request->qty_ng ?? 0;
        
        $totalInput = $qtyPass + $qtyHold + $qtyNg;

        // Validasi Qty: Total yang diproses harus persis sama dengan Qty awal
        if ($totalInput != $incomingItem->quantity) {
            return back()->with('error', "Gagal! Total Qty Pass + Hold + NG ($totalInput) tidak sama dengan Qty Awal ($incomingItem->quantity).");
        }

        DB::beginTransaction();

        try {
            // Kumpulkan data yang jumlahnya lebih dari 0
            $splits = [];
            if ($qtyPass > 0) $splits['Pass'] = ['qty' => $qtyPass, 'lokasi' => 'Warehouse'];
            if ($qtyHold > 0) $splits['Hold'] = ['qty' => $qtyHold, 'lokasi' => 'Karantina'];
            if ($qtyNg > 0)   $splits['NG']   = ['qty' => $qtyNg,   'lokasi' => 'Karantina'];

            $isFirstRow = true;

            // =========================================================
            // 2. PROSES UPDATE & KLONING BARIS INCOMING MATERIAL
            // =========================================================
            foreach ($splits as $status => $data) {
                // Atur keterangan: Hanya Hold dan NG yang pakai keterangan
                $keterangan = ($status == 'Hold' || $status == 'NG') ? $request->keterangan_qc : null;

                if ($isFirstRow) {
                    // Update baris asli (pertama)
                    DB::table('incoming_materials')->where('id', $id)->update([
                        'quantity'      => $data['qty'],
                        'status_qc'     => $status,
                        'lokasi_stok'   => $data['lokasi'],
                        'keterangan_qc' => $keterangan, // <--- TAMBAHKAN BARIS INI
                        'updated_at'    => Carbon::now()
                    ]);
                    $isFirstRow = false;
                } else {
                    // Kloning baris untuk sisa pecahannya
                    $newRow = (array) $incomingItem;
                    unset($newRow['id']); // Buang ID agar auto-increment
                    
                    $newRow['quantity']    = $data['qty'];
                    $newRow['status_qc']   = $status;
                    $newRow['lokasi_stok'] = $data['lokasi'];
                    $newRow['keterangan_qc'] = $keterangan; // <--- TAMBAHKAN BARIS INI JUGA
                    $newRow['created_at']  = Carbon::now();
                    $newRow['updated_at']  = Carbon::now();
                    
                    DB::table('incoming_materials')->insert($newRow);
                }

                // =========================================================
                // 3. MUTASI STOK (Menggunakan logika asli buatan Anda)
                // =========================================================
                if ($status == 'Pass') {
                    $cekStokWh = DB::table('warehouse_stocks')->where('no_mm', $incomingItem->mm)->first();

                    if ($cekStokWh) {
                        DB::table('warehouse_stocks')->where('no_mm', $incomingItem->mm)->increment('qty', $data['qty']);
                    } else {
                        DB::table('warehouse_stocks')->insert([
                            'no_mm'      => $incomingItem->mm,
                            'qty'        => $data['qty'],
                            'status'     => 'Aktif',
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                    }
                } 
                else if ($status == 'Hold' || $status == 'NG') {
                    $cekStokKarantina = DB::table('karantina_stocks')
                        ->where('no_mm', $incomingItem->mm)
                        ->where('status_karantina', $status) 
                        ->first();

                    if ($cekStokKarantina) {
                        $keteranganBaru = $cekStokKarantina->keterangan ? $cekStokKarantina->keterangan . ' | ' . $keterangan : $keterangan;

                        DB::table('karantina_stocks')
                            ->where('no_mm', $incomingItem->mm)
                            ->where('status_karantina', $status)
                            ->update([
                                'qty'        => $cekStokKarantina->qty + $data['qty'],
                                'keterangan' => $keteranganBaru,
                                'updated_at' => Carbon::now()
                            ]);
                    } else {
                        DB::table('karantina_stocks')->insert([
                            'no_mm'            => $incomingItem->mm,
                            'qty'              => $data['qty'],
                            'status_karantina' => $status, 
                            'keterangan'       => $keterangan,
                            'created_at'       => Carbon::now(),
                            'updated_at'       => Carbon::now(),
                        ]);
                    }
                }
            }

            // =========================================================
            // 4. TRIGGER SINKRONISASI KE PURCHASING
            // =========================================================
            $this->syncPurchaseOrderBalance($incomingItem->po_kpdt_number);

            DB::commit();

            return back()->with('success', 'Berhasil! Data material telah di-judgement, stok diperbarui, dan status PO Purchasing disinkronkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // =================================================================================
    // FUNGSI HELPER: SINKRONISASI PURCHASING
    // =================================================================================
    private function syncPurchaseOrderBalance($poNumber)
    {
        if (empty($poNumber)) return;

        DB::statement("
            UPDATE purchase_orders po
            JOIN purchase_requests pr ON po.pr_id = pr.id
            LEFT JOIN (
                SELECT po_kpdt_number, mm, SUM(quantity) as total_aktual
                FROM incoming_materials 
                WHERE po_kpdt_number = :po_num1
                AND deleted_at IS NULL
                AND status_qc != 'NG' -- NG tidak dihitung masuk PO
                GROUP BY po_kpdt_number, mm
            ) gudang ON gudang.po_kpdt_number = CONCAT('PO-', DATE_FORMAT(po.tanggal_po, '%Y%m%d'), '-', po.id)
                     AND gudang.mm = pr.no_mm
            SET 
                po.qty_diterima = IFNULL(gudang.total_aktual, 0),
                po.status_barang = CASE 
                    WHEN IFNULL(gudang.total_aktual, 0) >= pr.qty THEN 'Sudah Diterima'
                    WHEN IFNULL(gudang.total_aktual, 0) > 0 THEN 'Parsial'
                    ELSE 'Belum Datang'
                END
            WHERE CONCAT('PO-', DATE_FORMAT(po.tanggal_po, '%Y%m%d'), '-', po.id) = :po_num2
        ", [
            'po_num1' => $poNumber, 
            'po_num2' => $poNumber
        ]);
    }
}