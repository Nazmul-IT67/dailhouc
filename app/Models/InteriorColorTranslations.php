<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InteriorColorTranslations extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['interior_color_id', 'language', 'name'];
}
