<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngineAndEnvironment extends Model
{
    //
    use HasFactory;
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = [
        'catalytic_converter' => 'boolean',
        'particle_filter' => 'boolean',
    ];
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driverType()
    {
        return $this->belongsTo(DriverType::class, 'driver_type_id');
    }

    public function transmission()
    {
        return $this->belongsTo(Transmission::class, 'transmission_id');
    }

    public function numOfGears()
    {
        return $this->belongsTo(NumOfGear::class, 'num_of_gears_id');
    }

    public function cylinders()
    {
        return $this->belongsTo(Cylinder::class, 'cylinders_id');
    }

    public function emissionClass()
    {
        return $this->belongsTo(EmissionClass::class, 'emission_classes_id');
    }
    public function axleCount()
    {
        return $this->belongsTo(AxleCount::class, 'axle_count_id');
    }
}
