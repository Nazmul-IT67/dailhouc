<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BedTypeTranslations extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['bed_type_id', 'language', 'name'];
}
