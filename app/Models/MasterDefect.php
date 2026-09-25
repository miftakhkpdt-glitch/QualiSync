<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterDefect extends Model
{
    use HasFactory;

    protected $table = 'master_defects'; // Sesuaikan dengan nama tabel di database Anda
    protected $guarded = ['id'];
}
