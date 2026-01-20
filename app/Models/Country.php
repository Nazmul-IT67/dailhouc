<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $guarded = [];
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
    public function cities()
    {
        return $this->hasMany(City::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }

    protected $hidden = ['created_at', 'updated_at'];
}
