<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehiclePhoto extends Model
{
    //
    use HasFactory;
    protected $guarded = ['id'];
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
    protected $hidden = ['created_at', 'updated_at'];
}
