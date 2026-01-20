<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConditionAndMaintenance extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = [
        'service_history' => 'boolean',
        'non_smoker_car'  => 'boolean',
        'damaged_vehicle' => 'boolean',
        'guarantee'       => 'boolean',
        'recent_change_of_timing_belt'   => 'date',
        'recent_technical_service'       => 'date',
        'technical_inspection_valid_until' => 'date',
    ];
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
