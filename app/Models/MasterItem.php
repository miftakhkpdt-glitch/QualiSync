<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterItem extends Model
{
    use HasFactory;

    protected $table = 'master_items'; // Sesuaikan jika nama tabel Anda berbeda

    // TAMBAHKAN KODE INI
    protected $fillable = [
        'no_mm',
        'item_name',
        'min_weight',
        'max_weight',
        'min_height',
        'max_height',
        'min_lof',
        'max_lof',
        'sep_force',
        'std_air_tight',
        'std_tape_test',
        'std_unzip',
        'std_pinch',
    ];
}