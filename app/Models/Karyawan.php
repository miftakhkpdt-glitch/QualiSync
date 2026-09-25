<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawans';

    protected $fillable = [
    'nik', 
    'nama_karyawan', 
    'email', 
    'departemen', 
    'jabatan', 
    'tanggal_masuk', 
    'status_karyawan',
    'sisa_cuti', // Tambahkan ini agar input cuti awal tidak di-block
    'ktp',       // Tambahkan jika Anda menggunakan kolom ini di form
    'kk'         // Tambahkan jika Anda menggunakan kolom ini di form
];

    // Relasi ke tabel riwayat_cuti
    public function riwayatCuti()
    {
        return $this->hasOne(RiwayatCuti::class, 'karyawan_id');
    }

    // Relasi ke tabel cuti_records
    public function pengajuanCuti()
    {
        return $this->hasMany(PengajuanCuti::class, 'karyawan_id');
    }
}