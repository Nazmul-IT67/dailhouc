<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleReferenceSeeder extends Seeder
{
    public function run(): void
    {
        // Body Types
        DB::table('body_types')->insert([
            [
                'title' => 'Sedan',
                'category_id' => 1,
                'icon' => 'https://automoto54.com/uploads/body_type_icons/17659669401191719422.jpg',
            ],
            [
                'title' => 'SUV',
                'category_id' => 1,
                'icon' => 'https://automoto54.com/uploads/body_type_icons/17659669401191719422.jpg',
            ],
            [
                'title' => 'Hatchback',
                'category_id' => 2,
                'icon' => 'https://automoto54.com/uploads/body_type_icons/17659669401191719422.jpg',
            ],
            [
                'title' => 'Pickup',
                'category_id' => 2,
                'icon' => 'https://automoto54.com/uploads/body_type_icons/17659669401191719422.jpg',
            ],
        ]);

        // Fuel Types
        DB::table('fuels')->insert([
            ['title' => 'Petrol'],
            ['title' => 'Diesel'],
            ['title' => 'Electric'],
            ['title' => 'Hybrid'],
        ]);

        // Transmissions
        DB::table('transmissions')->insert([
            ['title' => 'Manual'],
            ['title' => 'Automatic'],
        ]);

        // Powers
        DB::table('powers')->insert([
            ['value' => '65 hp', 'unit' => 'hp'],
            ['value' => '81 kw', 'unit' => 'kw'],
        ]);

        // Equipment Lines
        DB::table('equipment_lines')->insert([
            ['title' => 'Standard'],
            ['title' => 'Sport'],
            ['title' => 'Luxury'],
        ]);

        // Seller Types
        DB::table('seller_types')->insert([
            ['title' => 'Private'],
            ['title' => 'Dealer'],
        ]);

        // Previous Owners
        DB::table('previous_owners')->insert([
            ['number' => 1],
            ['number' => 2],
        ]);

        // Doors & Seats
        DB::table('number_of_doors')->insert([
            ['number' => 2],
            ['number' => 4],
            ['number' => 5],
        ]);

        DB::table('number_of_seats')->insert([
            ['number' => 2],
            ['number' => 4],
            ['number' => 5],
            ['number' => 7],
        ]);

        // Driver Types
        DB::table('driver_types')->insert([
            ['title' => 'FWD'],
            ['title' => 'RWD'],
            ['title' => 'AWD'],
        ]);

        // Gears
        DB::table('num_of_gears')->insert([
            ['number' => 5],
            ['number' => 6],
            ['number' => 7],
        ]);

        // Cylinders
        DB::table('cylinders')->insert([
            ['number' => 3],
            ['number' => 4],
            ['number' => 6],
        ]);

        // Emission Classes
        DB::table('emission_classes')->insert([
            ['title' => 'Euro 4'],
            ['title' => 'Euro 5'],
            ['title' => 'Euro 6'],
        ]);

        // Axle Counts
        DB::table('axle_counts')->insert([
            ['count' => 2],
            ['count' => 3],
        ]);

        // Equipments
        DB::table('equipment')->insert([
            ['title' => 'Air Conditioning'],
            ['title' => 'Leather Seats'],
            ['title' => 'Navigation System'],
            ['title' => 'Parking Sensors'],
        ]);
    }
}
