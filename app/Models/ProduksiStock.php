<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduksiStock extends Model
{
    use HasFactory;

    // Mendefinisikan nama tabel secara eksplisit (opsional tapi disarankan)
    protected $table = 'produksi_stocks';

    // Kolom-kolom yang diizinkan untuk diisi datanya (Mass Assignment)
    protected $fillable = [
        'no_mm',
        'nama_material', // Opsional, sesuaikan jika di tabel ada kolom ini
        'kategori',      // Opsional, sesuaikan jika di tabel ada kolom ini
        'batch',
        'qty',
        'status'
    ];
}