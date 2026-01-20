<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandTranslations extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['brand_id', 'language', 'name'];
}
