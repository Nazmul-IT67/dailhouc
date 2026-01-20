<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubModel extends Model
{
    
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];

   public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'sub_model_id'); 
    }
    public function carModel()
    {
        return $this->belongsTo(CarModel::class);
    }

    public function translations()
    {
        return $this->hasMany(SubModelTranslations::class);
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
