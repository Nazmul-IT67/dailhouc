<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactInfo extends Model
{
    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];


    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
    // App\Models\ContactInfo.php
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
