<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoaDetail extends Model
{
    use HasFactory;

    protected $table = 'qa_coa_details';
    protected $guarded = ['id'];

    // Relasi Detail balik ke Header
    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }
}