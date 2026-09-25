<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBom extends Model
{
    use HasFactory;

    // Arahkan ke nama tabel yang tepat sesuai di database Anda
    protected $table = 'product_boms';

    protected $fillable = [
        'fg_mm', 
        'component_mm', 
        'qty_usage', 
        'satuan', 
        'is_optional'
    ];
}