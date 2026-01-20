<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleConditionTranslations extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['fuel_id', 'language', 'name', 'description'];
}
