<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubModelTranslations extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['sub_model_id', 'language', 'name'];
}
