<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Tambahkan ini!
use Illuminate\Database\Eloquent\Model; // Tambahkan ini!

class DailyReportDowntime extends Model
{
    use HasFactory;
    
    protected $guarded = [];
}