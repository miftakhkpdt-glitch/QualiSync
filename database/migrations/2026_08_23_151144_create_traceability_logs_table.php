<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('traceability_logs', function (Blueprint $table) {
            $table->id();
            // Relasi ke Work Order
            $table->foreignId('work_order_id')->constrained('work_orders')->onDelete('cascade');
            
            // Info Umum
            $table->date('tanggal');
            $table->string('shift', 50)->nullable();
            $table->string('grup', 50)->nullable();
            $table->string('batch_num', 100)->nullable();

            // Traceability: Printed Web
            $table->date('web_incoming_date')->nullable();
            $table->string('web_item_name')->nullable();
            $table->string('web_lot_num')->nullable();
            $table->integer('web_qty')->default(0); // Kolom Qty (Sesuai request Anda)

            // Traceability: CAP (Tutup)
            $table->date('cap_incoming_date')->nullable();
            $table->string('cap_lot_num')->nullable();
            $table->string('cap_color')->nullable();
            $table->integer('cap_qty')->default(0); // Kolom Qty (Sesuai request Anda)

            // Traceability: Resin / Plastik (Input Manual)
            $table->string('lot_master_batch')->nullable();
            $table->string('lot_hdpe')->nullable();

            // Verifikasi Operator & Quality
            $table->string('operator')->nullable(); // Nama operator
            
            // Sistem Stempel QA
            $table->enum('qa_status', ['Pending', 'Pass', 'Hold', 'NG'])->default('Pending');
            $table->string('qa_checked_by')->nullable(); // Nama orang QA
            $table->timestamp('qa_checked_at')->nullable(); // Waktu QA melakukan pengecekan
            $table->text('qa_note')->nullable(); // Catatan QA jika Hold/NG

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('traceability_logs');
    }
};