<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerTypeTranslations extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['seller_type_id', 'language', 'title'];
}
