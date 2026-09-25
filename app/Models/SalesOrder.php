<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use HasFactory;

    // Pastikan SEMUA kolom ini terdaftar di sini agar tidak diblokir oleh Laravel
    protected $fillable = [
        'nama_customer', 
        'tanggal_po', 
        'no_po', 
        'delivery_date', 
        'no_mm', 
        'nama_produk', // <-- Kolom ini wajib ada
        'price', 
        'qty', 
        'ospo',
        'satuan',
        'status'
    ];
}