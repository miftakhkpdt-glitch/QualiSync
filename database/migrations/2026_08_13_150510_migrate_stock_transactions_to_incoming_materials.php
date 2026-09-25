<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Pastikan kolom pendukung QC ada di tabel incoming_materials
        if (Schema::hasTable('incoming_materials')) {
            Schema::table('incoming_materials', function (Blueprint $table) {
                if (!Schema::hasColumn('incoming_materials', 'status_qc')) {
                    $table->string('status_qc')->default('Pending')->nullable();
                }
                if (!Schema::hasColumn('incoming_materials', 'lokasi_stok')) {
                    $table->string('lokasi_stok')->default('Karantina')->nullable();
                }
            });

            // 2. Pindahkan data dari stock_transactions jika tabelnya ada
            if (Schema::hasTable('stock_transactions')) {
                $oldTransactions = DB::table('stock_transactions')->get();

                foreach ($oldTransactions as $trx) {
                    $material = DB::table('master_materials')->where('no_mm', $trx->no_mm ?? $trx->mm ?? '')->first();

                    DB::table('incoming_materials')->insert([
                        'date'               => $trx->tanggal ?? $trx->date ?? now()->toDateString(),
                        'mm'                 => $trx->no_mm ?? $trx->mm ?? '-',
                        'item_name'          => $material->nama_material ?? 'Material Lama',
                        'quantity'           => $trx->jumlah ?? $trx->quantity ?? 0,
                        'uom'                => $material->satuan ?? 'Pcs',
                        'stpb_number'        => $trx->stpb_number ?? '-',
                        'po_kpdt_number'     => $trx->po_kpdt_number ?? '-',
                        'sj_supplier_number' => $trx->sj_supplier_number ?? '-',
                        'vendor_code'        => $trx->vendor_code ?? '-',
                        'vendor_name'        => $trx->supplier ?? $trx->vendor_name ?? '-',
                        'address'            => $trx->address ?? '-',
                        'remarks'            => $trx->remarks ?? 'Migrasi dari stock_transactions',
                        'status_qc'          => $trx->status_qc ?? 'Pending',
                        'lokasi_stok'        => $trx->lokasi_stok ?? 'Karantina',
                        'created_at'         => $trx->created_at ?? now(),
                        'updated_at'         => $trx->updated_at ?? now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};