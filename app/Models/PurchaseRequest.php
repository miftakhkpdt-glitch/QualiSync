<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    // Pastikan 'departemen' ditambahkan ke dalam array ini!
    protected $fillable = [
        'no_pr', 
        'tanggal', 
        'no_mm', 
        'qty', 
        'estimasi_tiba', 
        'kategori', 
        'status', 
        'status_approval', 
        'pemohon_id', 
        'catatan',
        'departemen', // <--- INI TAMBAHANNYA
    ];

    // ... relasi lainnya ...
}