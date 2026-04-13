<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
=======
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
>>>>>>> 2bdbe6e (first commit)

class BodyColor extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function translations()
    {
        return $this->hasMany(BodyColorTranslations::class);
    }

    public function getNameAttribute($value)
    {
        if (app()->getLocale() == 'fr') {
            $translation = $this->translations()->where('language', 'fr')->first();
            return $translation ? $translation->name : $value;
        }
        return $value;
    }
<<<<<<< HEAD

    public function vehicleData()
    {
        return $this->hasMany(VehicleData::class, 'body_color_id');
    }
=======
>>>>>>> 2bdbe6e (first commit)
}
