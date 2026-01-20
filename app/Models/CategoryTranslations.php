<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryTranslations extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['category_id', 'language', 'name'];
}
