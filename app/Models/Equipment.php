<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
=======
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
>>>>>>> 2bdbe6e (first commit)

class Equipment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function translations()
    {
        return $this->hasMany(EquipmentTranslations::class);
    }

    public function getNameAttribute($value)
    {
        if (app()->getLocale() == 'fr') {
            $translation = $this->translations()->where('language', 'fr')->first();
            return $translation ? $translation->title : $value;
        }
        return $value; 
    }
<<<<<<< HEAD

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'equipment_id');
    }
=======
>>>>>>> 2bdbe6e (first commit)
}
