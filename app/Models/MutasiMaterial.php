<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MutasiMaterial extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mutasi_materials';

    protected $fillable = [
        'tanggal', 
        'shift', 
        'dari_dept', 
        'ke_dept', 
        'mm', 
        'item_name',
        'batch',
        'status_asal',
        'qty', 
        'uom', 
        'pic_id', 
        'approved_by', 
        'status_approval', 
        'approved_at', 
        'catatan'
    ];

    // Relasi untuk mengambil nama pembuat mutasi
    public function pembuat()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    // Relasi untuk mengambil nama penyetuju mutasi
    public function penyetuju()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}