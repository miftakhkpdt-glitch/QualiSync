<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatCuti extends Model
{
    protected $table = 'riwayat_cuti';

    protected $fillable = [
        'karyawan_id', 'sisa_cuti'
    ];

    // Relasi ke tabel karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}