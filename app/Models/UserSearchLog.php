<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSearchLog extends Model
{
    protected $fillable = [
    'user_id',
    'filters',
    'results_count',
    'ip_address'
];

    protected $casts = [
        'filters' => 'array',
    ];
}
