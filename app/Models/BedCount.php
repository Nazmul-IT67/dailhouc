<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BedCount extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function translations()
    {
        return $this->hasMany(BedCountTranslations::class);
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
