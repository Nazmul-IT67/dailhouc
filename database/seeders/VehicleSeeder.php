<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            Vehicle::create([
                'engine_displacement' => '2.0L Turbo',
                'category_id'         => $i,
                'brand_id'            => $i,
                'model_id'            => $i,
                'sub_model_id'        => $i,
                'first_registration'  => '2023-01-15',
                'body_type_id'        => 1,
                'fuel_id'             => 1,
                'transmission_id'     => 1,
                'power_id'            => 1,
                'equipment_line_id'   => 1,
                'seller_type_id'      => 1,
                'price'               => 25000.00,
                'price_in_base'       => 25000.00,
                'currency_id'         => 1,
                'milage'              => 5000.50,
                'description'         => 'Excellent condition vehicle with low mileage.',
                'featured_request'    => 1,
                'is_featured'         => 1,
                'equipment_ids'       => [1, 2, 3],
                'user_id'             => 1,
                'status'              => 1,
                'base_currency_id'    => 1,
            ]);
        }
    }
}