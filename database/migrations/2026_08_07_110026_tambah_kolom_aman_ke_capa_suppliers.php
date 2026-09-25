<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capa_suppliers', function (Blueprint $table) {
            // Daftar semua kolom yang dibutuhkan beserta tipe datanya
            $kolomBaru = [
                'nama_produk'            => 'string',
                'no_lot'                 => 'string',
                'tanggal_produksi_shift' => 'string',
                'no_mesin_line'          => 'string',
                'history_man'            => 'string',
                'history_machine'        => 'string',
                'history_method'         => 'string',
                'history_mold'           => 'string',
                'history_material'       => 'string',
                'history_env'            => 'string',
                'containment_action'     => 'text',
                'root_cause_category'    => 'string',
                'why_1'                  => 'text',
                'why_2'                  => 'text',
                'why_3'                  => 'text',
                'why_4'                  => 'text',
                'why_5'                  => 'text',
                'kondisi_std'            => 'text',
                'kondisi_act'            => 'text',
                'verifikasi'             => 'text',
                'kesimpulan_verifikasi'  => 'text',
                'root_cause'             => 'text',
                'corrective_action'      => 'text',
                'preventive_action'      => 'text',
                'val_hasil_ca'           => 'string',
                'val_status_ca'          => 'string',
                'val_pic_ca'             => 'string',
                'val_hasil_pa'           => 'string',
                'val_status_pa'          => 'string',
                'val_pic_pa'             => 'string',
                'doc_types'              => 'string',
                'deskripsi_dokumen'      => 'text',
                'doc_plan_date'          => 'date',
                'doc_act_date'           => 'date',
                'control_chart'          => 'string',
                'team_celebration'       => 'string',
                'pic_supplier'           => 'string',
                'tanggal_implementasi'   => 'date',
                'tanggal_submit_supplier'=> 'date',
                'file_supplier'          => 'string',
            ];

            // Loop untuk mengecek dan menambahkan kolom HANYA JIKA belum ada
            foreach ($kolomBaru as $namaKolom => $tipeData) {
                if (!Schema::hasColumn('capa_suppliers', $namaKolom)) {
                    if ($tipeData === 'string') {
                        $table->string($namaKolom)->nullable();
                    } elseif ($tipeData === 'text') {
                        $table->text($namaKolom)->nullable();
                    } elseif ($tipeData === 'date') {
                        $table->date($namaKolom)->nullable();
                    }
                }
            }
        });
    }

    public function down(): void
    {
        // Kosongkan fungsi down agar rollback aman
    }
};