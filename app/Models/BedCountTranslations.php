<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BedCountTranslations extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['bed_count_id', 'language', 'number'];
}
