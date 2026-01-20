<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = [];
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    // Relation with User (optional)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
