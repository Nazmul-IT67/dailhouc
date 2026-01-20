<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BodyTypeTranslations extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['body_type_id', 'language', 'title'];
}
