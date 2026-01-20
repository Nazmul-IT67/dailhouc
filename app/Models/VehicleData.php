<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleData extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = [
        'metalic' => 'boolean',
        'negotiable' => 'boolean',
        'indicate_vat' => 'boolean',
        'bed_type_id' => 'array',
    ];

    public function condition()
    {
        return $this->belongsTo(VehicleCondition::class, 'vehicle_conditions_id');
    }

    public function bodyColor()
    {
        return $this->belongsTo(BodyColor::class, 'body_color_id');
    }
    public function translations()
    {
        // Check korun apnar translation model-er nam 'BedTypeTranslation' kina
        return $this->hasMany(BedTypeTranslations::class, 'bed_type_id');
    }
    public function upholstery()
    {
        return $this->belongsTo(Upholstery::class, 'upholstery_id');
    }

    public function interiorColor()
    {
        return $this->belongsTo(InteriorColor::class, 'interior_color_id');
    }

    public function previousOwner()
    {
        return $this->belongsTo(PreviousOwner::class, 'previous_owner_id');
    }

    public function numOfDoor()
    {
        return $this->belongsTo(NumberOfDoor::class, 'num_of_door_id');
    }

    public function numOfSeats()
    {
        return $this->belongsTo(NumberOfSeat::class, 'num_of_seats_id');
    }
    public function bedCount()
    {
        return $this->belongsTo(BedCount::class, 'bed_count_id');
    }

    public function getBedTypesAttribute()
    {
        // Jodi controller theke manually 'bed_types' set kora hoy, tobe shetai return korbe
        if (isset($this->attributes['bed_types'])) {
            return is_string($this->attributes['bed_types'])
                ? json_decode($this->attributes['bed_types'])
                : $this->attributes['bed_types'];
        }

        if (!$this->bed_type_id) {
            return [];
        }

        // Default fallback (English name)
        return \App\Models\BedType::whereIn('id', (array)$this->bed_type_id)->pluck('name')->toArray();
    }
}
