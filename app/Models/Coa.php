<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    use HasFactory;

    protected $table = 'qa_coas';
    protected $guarded = ['id'];

    // Relasi Header ke Detail
    public function details()
    {
        return $this->hasMany(CoaDetail::class, 'coa_id');
    }
}