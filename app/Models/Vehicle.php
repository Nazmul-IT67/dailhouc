<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'equipment_ids' => 'array',
    ];
    protected $appends = ['equipments'];
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function model()
    {
        return $this->belongsTo(CarModel::class);
    }
    public function subModel()
    {
        return $this->belongsTo(SubModel::class);
    }

    public function fuel()
    {
        return $this->belongsTo(Fuel::class);
    }
    public function body_type()
    {
        return $this->belongsTo(BodyType::class);
    }
    public function transmission()
    {
        return $this->belongsTo(Transmission::class);
    }
    public function power()
    {
        return $this->belongsTo(Power::class, 'power_id', 'id');
    }

    public function equipment_line()
    {
        return $this->belongsTo(EquipmentLine::class);
    }

    public function seller_type()
    {
        return $this->belongsTo(SellerType::class);
    }

    public function photos()
    {
        return $this->hasMany(VehiclePhoto::class);
    }

    public function getEquipmentsAttribute()
    {
        return Equipment::whereIn('id', $this->equipment_ids ?? [])->get();
    }

    public function equipments()
    {
        return $this->hasMany(Equipment::class, 'id', 'equipment_ids');
    }

    public function data()
    {
        return $this->hasOne(VehicleData::class);
    }
    
    public function engineAndEnvironment()
    {
        return $this->hasOne(EngineAndEnvironment::class);
    }

    public function conditionAndMaintenance()
    {
        return $this->hasOne(ConditionAndMaintenance::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorite_vehicles');
    }

    public function favorites()
    {
        return $this->hasMany(FavoriteVehicle::class, 'vehicle_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contactInfo()
    {
        return $this->hasOne(ContactInfo::class);
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function baseCurrency()
    {
        return $this->belongsTo(Currency::class, 'base_currency_id', 'id');
    }

    protected $hidden = ['updated_at', 'equipment_ids', 'is_featured'];
}
