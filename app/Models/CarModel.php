<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];
    
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'model_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function subModels()
    {
        return $this->hasMany(SubModel::class);
    }

    public function translations()
    {
        return $this->hasMany(CarModelTranslations::class);
    }

    public function getNameAttribute($value)
    {
        if (app()->getLocale() == 'fr') {
            $translation = $this->translations()->where('language', 'fr')->first();
            return $translation ? $translation->name : $value;
        }
        return $value;
    }
}
