<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentLineTranslations extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['equipment_line_id', 'language', 'title'];
}
