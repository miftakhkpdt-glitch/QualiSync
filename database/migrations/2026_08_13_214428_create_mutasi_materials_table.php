<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('mutasi_materials', function (Blueprint $table) {
        $table->id();
        $table->date('tanggal');
        $table->string('shift'); // Contoh: Shift 1, Shift 2, Shift 3
        $table->string('dari_dept'); // Contoh: Warehouse, Produksi, QC
        $table->string('ke_dept'); // Contoh: Warehouse, Produksi, QC
        $table->string('mm'); // Nomor MM material
        $table->string('item_name')->nullable();
        $table->decimal('qty', 15, 2);
        $table->string('uom')->default('Pcs');
        
        // Relasi ke tabel Users
        $table->unsignedBigInteger('pic_id'); // User yang membuat mutasi (Kirim)
        $table->unsignedBigInteger('approved_by')->nullable(); // User yang menyetujui (Terima)
        
        $table->string('status_approval')->default('Pending'); // Pending, Approved, Rejected
        $table->timestamp('approved_at')->nullable();
        $table->text('catatan')->nullable(); // Alasan mutasi / keterangan retur
        
        $table->timestamps();
        $table->softDeletes(); // Agar riwayat tidak benar-benar terhapus dari database

        // Foreign keys
        $table->foreign('pic_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');
    });
}
};
