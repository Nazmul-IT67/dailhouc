<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleSeeder extends Seeder
{
    // public function run(): void
    // {
    //     for ($i = 1; $i <= 5; $i++) {
    //         Vehicle::create([
    //             'engine_displacement' => '2.0L Turbo',
    //             'category_id'         => $i,
    //             'brand_id'            => $i,
    //             'model_id'            => $i,
    //             'sub_model_id'        => $i,
    //             'first_registration'  => '2023-01-15',
    //             'body_type_id'        => 1,
    //             'fuel_id'             => 1,
    //             'transmission_id'     => 1,
    //             'power_id'            => 1,
    //             'equipment_line_id'   => 1,
    //             'seller_type_id'      => 1,
    //             'price'               => 25000.00,
    //             'price_in_base'       => 25000.00,
    //             'currency_id'         => 1,
    //             'milage'              => 5000.50,
    //             'description'         => 'Excellent condition vehicle with low mileage.',
    //             'featured_request'    => 1,
    //             'is_featured'         => 1,
    //             'equipment_ids'       => [1, 2, 3],
    //             'user_id'             => 1,
    //             'status'              => 1,
    //             'base_currency_id'    => 1,
    //         ]);
    //     }
    // }

    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $vehicle = \App\Models\Vehicle::create([
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
                'description'         => 'Excellent condition vehicle ' . $i . ' with low mileage.',
                'featured_request'    => 1,
                'is_featured'         => 1,
                'equipment_ids'       => [1, 2, 3],
                'user_id'             => 1,
                'status'              => 1,
                'base_currency_id'    => 1,
            ]);

            \App\Models\VehiclePhoto::create([
                'vehicle_id' => $vehicle->id,
                'file_path'  => 'uploads/Vehicle/simple.jpg',
                'is_primary' => 1,
            ]);

            \App\Models\VehicleData::create([
                'vehicle_id'            => $vehicle->id,
                'vehicle_conditions_id' => 1,
                'body_color_id'         => 1,
                'upholstery_id'         => 1,
                'interior_color_id'     => 1,
                'previous_owner_id'     => 1,
                'num_of_door_id'        => 1,
                'num_of_seats_id'       => 1,
                'metalic'               => 1,
                'negotiable'            => 0,
                'indicate_vat'          => 1,
            ]);

            \App\Models\ContactInfo::create([
                'vehicle_id'     => $vehicle->id,
                'name'           => 'Seller ' . $i,
                'email'          => 'seller' . $i . '@example.com',
                'phone'          => '0170000000' . $i,
                'country_id'     => 1,
                'city_id'        => 1,
                'is_email_show'  => 1,
                'is_number_show' => 1,
                'is_whatsapp_show' => 1,
            ]);
        }
    }
}