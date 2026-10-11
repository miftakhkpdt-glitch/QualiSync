<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CalibrationEquipment extends Model
{
    protected $table = 'calibration_equipment';

    protected $fillable = [
        'identity_number',
        'item_name',
        'type',
        'serial_number',
        'expiration_date',
        'tool_condition',
        'label_condition',
        'holder',
        'notes',
    ];

    protected function casts(): array
    {
        return ['expiration_date' => 'date'];
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(CalibrationVerification::class, 'equipment_id');
    }
}
