<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSearchLog extends Model
{
    protected $fillable = [
<<<<<<< HEAD
        'user_id',
        'category_id',
        'filters',
        'results_count',
        'ip_address'
    ];
=======
    'user_id',
    'filters',
    'results_count',
    'ip_address'
];
>>>>>>> 2bdbe6e (first commit)

    protected $casts = [
        'filters' => 'array',
    ];
<<<<<<< HEAD

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function contactInfo()
    {
        return $this->hasOne(ContactInfo::class, 'vehicle_id', 'vehicle_id');
    }
=======
>>>>>>> 2bdbe6e (first commit)
}
