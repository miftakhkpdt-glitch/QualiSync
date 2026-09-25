<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FgInspectionDefectAql extends Model
{
    use HasFactory;

    protected $table = 'fg_inspection_defect_aqls';
    protected $guarded = ['id'];

    // Relasi balik ke baris detail defect
    public function detailDefect()
    {
        // Sesuaikan dengan nama Model Detail Anda
        return $this->belongsTo(FgInspectionDetail::class, 'fg_inspection_detail_id');
    }
}