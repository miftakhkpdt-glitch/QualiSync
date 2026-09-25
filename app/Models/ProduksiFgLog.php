<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduksiFgLog extends Model
{
    use HasFactory;

    protected $table = 'produksi_fg_logs';

    protected $fillable = [
        'work_order_id',
        'shift',
        'operator',
        'qty_good',
        'qty_reject',
        'jenis_reject',
        'status_laporan'
    ];
}