<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];
    public function category()

    {
        return $this->belongsTo(Category::class);
    }
      public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function translations()
    {
        return $this->hasMany(BrandTranslations::class);
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
