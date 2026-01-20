<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelTranslations extends Model
{
    protected $fillable = ['fuel_id', 'language', 'title', 'description'];
}
