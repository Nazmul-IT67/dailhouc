<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSearchLog extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'filters',
        'results_count',
        'ip_address'
    ];

    protected $casts = [
        'filters' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function contactInfo()
    {
        return $this->hasOne(ContactInfo::class, 'vehicle_id', 'vehicle_id');
    }
}
