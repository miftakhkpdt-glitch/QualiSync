<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('no_po');                  // Nomor PO dari Customer
            $table->string('nama_customer');          // Nama Perusahaan Customer
            $table->date('tanggal_po');               // Tanggal PO diterbitkan
            $table->date('delivery_date');            // Tanggal target kirim
            $table->string('nama_produk');            // Nama barang yang dipesan
            $table->integer('qty');                   // Jumlah kuantitas pesanan
            $table->string('satuan');                 // Pcs, Box, Set, dll
            $table->enum('status', ['Pending PPIC', 'In Production', 'Ready to Ship', 'Completed'])->default('Pending PPIC');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};