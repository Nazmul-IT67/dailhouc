<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileOption extends Model
{
    protected $fillable = [
        'group',
        'key',
        'label',
        'info',
        'sort_order'
    ];

    public $timestamps = true;
}
