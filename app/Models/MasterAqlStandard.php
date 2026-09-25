<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterAqlStandard extends Model
{
    use HasFactory;

    protected $table = 'master_aql_standards';

    protected $fillable = [
        'customer_id',
        'customer_name',
        'tipe_standar', // <-- Tambahkan ini agar bisa diisi
        'aql_zero_defect',
        'aql_critical',
        'aql_major',
        'aql_minor',
        'crqs_amber',
        'crqs_red',
    ];
}