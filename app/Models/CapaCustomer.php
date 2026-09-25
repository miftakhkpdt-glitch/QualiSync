<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapaCustomer extends Model
{
    use HasFactory;

    protected $table = 'capa_customers';

    protected $fillable = [
        'complaint_month',
        'customer',
        'no_capa_customer',
        'mm',
        'item',
        'defect',
        'source',
        'category_defect',
        'sncr',
        'status'
    ];
}