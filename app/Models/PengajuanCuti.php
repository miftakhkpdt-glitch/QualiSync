<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanCuti extends Model
{
    // Arahkan ke nama tabel yang benar di database MySQL Anda
    protected $table = 'cuti_records';

    protected $fillable = [
        'karyawan_id', 'nama_karyawan', 'jumlah_hari', 'departemen',
        'tanggal_mulai', 'tanggal_selesai', 'alasan', 'status_dept', 'status_hrd'
    ];

    // Relasi ke tabel karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}