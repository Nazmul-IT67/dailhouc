<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BodyType extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'body_type_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function translations()
    {
        return $this->hasMany(BodyTypeTranslations::class);
    }

    public function getNameAttribute($value)
    {
        if (app()->getLocale() == 'fr') {
            $translation = $this->translations()->where('language', 'fr')->first();
            return $translation ? $translation->title : $value;
        }
        return $value; 
    }
}
