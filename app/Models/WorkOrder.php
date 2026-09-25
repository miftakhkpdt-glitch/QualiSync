<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id', 
        'no_mm', 
        'qty_target', 
        'qty_good', 
        'qty_reject', 
        'no_wo', 
        'start_date', 
        'status'
    ];

    // INI ADALAH JEMBATAN RELASI YANG TADI KURANG
    // Mengubah sales_order_id menjadi data utuh dari tabel sales_orders
    public function salesOrder()
    {
        return $this->belongsTo(\App\Models\SalesOrder::class, 'sales_order_id');
    }
}