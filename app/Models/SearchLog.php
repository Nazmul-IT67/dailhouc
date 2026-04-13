<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = [];
<<<<<<< HEAD
    
=======
>>>>>>> 2bdbe6e (first commit)
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

<<<<<<< HEAD
=======
    // Relation with User (optional)
>>>>>>> 2bdbe6e (first commit)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
