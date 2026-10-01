<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevelopmentProject extends Model
{
    protected $fillable = [
        'judul_riset',
        'kode_material',
        'target_suhu',
        'status',
        'keterangan',
        'dibuat_oleh',
    ];
}
