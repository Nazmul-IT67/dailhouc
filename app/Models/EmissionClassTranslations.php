<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmissionClassTranslations extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['emission_class_id', 'language', 'title'];
}
