<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentTranslations extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['equipment_id', 'language', 'title'];
}
