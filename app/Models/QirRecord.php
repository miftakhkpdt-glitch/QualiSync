<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QirRecord extends Model
{
    // Memberitahu Laravel nama tabel yang sebenarnya
    protected $table = 'qir_records';

    // Mengizinkan semua kolom diisi secara massal, KECUALI id
    protected $guarded = ['id'];
}