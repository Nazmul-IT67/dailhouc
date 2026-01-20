<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverTypeTranslations extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['driver_type_id', 'language', 'title'];
}
