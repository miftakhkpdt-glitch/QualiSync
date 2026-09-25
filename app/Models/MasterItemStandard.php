<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterItemStandard extends Model
{
    use HasFactory;

    // Menghubungkan model ini ke tabel master_item_standards
    protected $table = 'master_item_standards'; 

    // Mendaftarkan kolom apa saja yang boleh diisi (termasuk kolom Yasulor)
    protected $fillable = [
        'no_mm',
        'parameter_id',
        'min_value',
        'max_value',
        'standar_teks',
        'urutan',
        // --- Kolom Yasulor ---
        'control_method',
        'insp_level',
        'aql',
        'freq',
        'n',
    ];
}