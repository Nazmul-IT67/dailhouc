<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    //
    protected $guarded = [];
    public function countries()
    {
        return $this->hasMany(Country::class);
    }
}
