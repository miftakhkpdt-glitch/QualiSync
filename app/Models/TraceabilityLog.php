<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TraceabilityLog extends Model
{
    use HasFactory;

    protected $table = 'traceability_logs';

    protected $fillable = [
        'work_order_id',
        'tanggal',
        'shift',
        'grup',
        'batch_num',
        'web_incoming_date',
        'web_item_name',
        'web_lot_num',
        'web_qty',
        'cap_incoming_date',
        'cap_lot_num',
        'cap_color',
        'cap_qty',
        'lot_master_batch',
        'lot_hdpe',
        'operator',
        'qa_status',
        'qa_checked_by',
        'qa_checked_at',
        'qa_note'
    ];

    // Relasi balik ke Work Order
    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }
}