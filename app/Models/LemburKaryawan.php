<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LemburKaryawan extends Model
{
    use HasFactory;

    protected $table = 'lembur_karyawans';

    protected $fillable = [
        'user_id',
        'tanggal_lembur',
        'jam_mulai',
        'jam_selesai',
        'keterangan_pekerjaan',
        'status_dept',
        'status_hrd',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}