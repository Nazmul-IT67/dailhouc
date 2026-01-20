<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpholsteryTranslations extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['upholstery_id', 'language', 'name'];
}
