<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesForecast extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_customer',
        'no_mm',
        'nama_produk',
        'bulan',
        'tahun',
        'qty_forecast',
        'satuan',
        'versi',
        'alasan_revisi',
        'status'
    ];
}