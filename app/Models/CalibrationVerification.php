<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalibrationVerification extends Model
{
    protected $fillable = [
        'equipment_id',
        'measurement_point',
        'capacity',
        'resolution',
        'internal_lop',
        'certificate_correction',
        'certificate_uncertainty',
        'te',
        'verification_result',
        'equipment_status',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(CalibrationEquipment::class, 'equipment_id');
    }
}
