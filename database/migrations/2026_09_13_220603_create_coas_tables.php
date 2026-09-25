<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tabel Header Certificate of Analysis (Quality)
        Schema::create('qa_coas', function (Blueprint $table) {
            $table->id();
            $table->string('no_coa')->unique();
            $table->string('no_mm');
            $table->string('no_batch'); 
            $table->string('template_type')->default('GENERAL'); 

            // Informasi Umum & Customer
            $table->string('customer_name')->nullable();
            $table->string('item_code_customer')->nullable();
            $table->string('po_number')->nullable();
            $table->integer('delivery_quantity')->nullable();
            $table->date('delivery_date')->nullable();

            // Informasi Ekstra (Untuk Yasulor atau Kebutuhan Khusus)
            $table->string('machine_no')->nullable();
            $table->integer('sample_quantity')->nullable();
            $table->string('production_date')->nullable(); 
            $table->date('issue_date')->nullable();
            $table->string('cavity_mandrel')->nullable();
            $table->string('expire_date')->nullable(); 

            // Status, Keputusan & Catatan Tambahan
            $table->enum('status_decision', ['PASSED', 'PASSED WITH NOTE', 'RELEASE', 'BLOCKED'])->default('PASSED');
            $table->text('remark')->nullable();
            $table->string('box_qty_note')->nullable(); 

            // 2 Tanda Tangan Wajib
            $table->string('prepared_by')->default('Miftakh'); 
            $table->string('approved_by')->default('Rajib');   

            $table->timestamps();
        });

        // Tabel Detail Parameter
        Schema::create('qa_coa_details', function (Blueprint $table) {
            $table->id();
            // Arahkan foreign key ke tabel qa_coas
            $table->foreignId('coa_id')->constrained('qa_coas')->onDelete('cascade');
            
            $table->string('nama_parameter');
            
            // Kolom General COA
            $table->string('uom')->nullable();
            $table->string('min_val')->nullable();
            $table->string('max_val')->nullable();
            $table->string('result_avg')->nullable(); 

            // Kolom Ekstra Yasulor COA
            $table->string('control_method')->nullable();
            $table->string('insp_level')->nullable();
            $table->string('aql')->nullable();
            $table->string('frequency')->nullable();
            $table->integer('n_sampling')->nullable();
            $table->string('standar_text')->nullable();
            $table->enum('decision', ['OK', 'NG'])->nullable();
            $table->string('remark')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('qa_coa_details');
        Schema::dropIfExists('qa_coas');
    }
};