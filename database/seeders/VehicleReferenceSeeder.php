<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class VehicleReferenceSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('powers')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $now = Carbon::now();

        // --- 1. Body Types ---
        $bodyTypes = [
            ['title' => 'Sedan', 'fr' => 'Berline', 'cat' => 1],
            ['title' => 'SUV', 'fr' => 'SUV', 'cat' => 1],
            ['title' => 'Hatchback', 'fr' => 'Citadine', 'cat' => 2],
            ['title' => 'Pickup', 'fr' => 'Pick-up', 'cat' => 2],
        ];

        foreach ($bodyTypes as $item) {
            $id = DB::table('body_types')->insertGetId([
                'title' => $item['title'],
                'category_id' => $item['cat'],
                'icon' => 'https://automoto54.com/uploads/body_type_icons/17659669401191719422.jpg',
            ]);

            DB::table('body_type_translations')->insert([
                'body_type_id' => $id,
                'language' => 'fr',
                'title' => $item['fr'],
                'created_at' => $now,
            ]);
        }

        // --- 2. Fuel Types ---
        $fuels = [
            ['en' => 'Petrol', 'fr' => 'Essence'],
            ['en' => 'Diesel', 'fr' => 'Diesel'],
            ['en' => 'Electric', 'fr' => 'Électrique'],
            ['en' => 'Hybrid', 'fr' => 'Hybride'],
        ];

        foreach ($fuels as $fuel) {
            $id = DB::table('fuels')->insertGetId(['title' => $fuel['en']]);
            DB::table('fuel_translations')->insert([
                'fuel_id' => $id,
                'language' => 'fr',
                'title' => $fuel['fr'],
                'created_at' => $now,
            ]);
        }

        // --- 3. Transmissions ---
        $transmissions = [
            ['en' => 'Manual', 'fr' => 'Manuelle'],
            ['en' => 'Automatic', 'fr' => 'Automatique'],
        ];

        foreach ($transmissions as $trans) {
            $id = DB::table('transmissions')->insertGetId(['title' => $trans['en']]);
            DB::table('transmission_translations')->insert([
                'transmission_id' => $id,
                'language' => 'fr',
                'title' => $trans['fr'],
                'created_at' => $now,
            ]);
        }

        $powers = [
              ['value' => '90'],
              ['value' => '100'],
              ['value' => '150'],
          ];

        foreach ($powers as $power) {
            $kwValue = (float) $power['value'];

            // KW theke HP calculation (1.34102 multiplier)
            $power_hp = round($kwValue * 1.34102, 2);

            DB::table('powers')->insert([
                'value'      => $power['value'],
                'unit'       => 'KW', // Always KW
                'power_hp'   => $power_hp, // Calculated HP (e.g., 120.69)
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $equipments = [
                    ['en' => 'Standard', 'fr' => 'Standard'],
                    ['en' => 'Sport', 'fr' => 'Sport'],
                    ['en' => 'Luxury', 'fr' => 'Luxe'],
                ];

        foreach ($equipments as $eq) {
            $id = DB::table('equipment_lines')->insertGetId(['title' => $eq['en']]);
            DB::table('equipment_line_translations')->insert([
                'equipment_line_id' => $id,
                'language' => 'fr',
                'title' => $eq['fr'],
                'created_at' => $now,
            ]);
        }
        // Seller Types
        $sellerTypes = [
              ['en' => 'Private', 'fr' => 'Particulier'],
              ['en' => 'Dealer',  'fr' => 'Professionnel'],
          ];

        foreach ($sellerTypes as $item) {
            // 1. Main seller_types table-e insert kora
            $id = DB::table('seller_types')->insertGetId([
                'title'      => $item['en'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // 2. Translation table-e French data insert kora
            DB::table('seller_type_translations')->insert([
                'seller_type_id' => $id,
                'language'       => 'fr',
                'title'          => $item['fr'],
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }
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
        $driverTypes = [
            [
                'en_short' => 'FWD',
                'en_full'  => 'Front-Wheel Drive',
                'fr_full'  => 'Traction avant'
            ],
            [
                'en_short' => 'RWD',
                'en_full'  => 'Rear-Wheel Drive',
                'fr_full'  => 'Propulsion'
            ],
            [
                'en_short' => 'AWD',
                'en_full'  => 'All-Wheel Drive',
                'fr_full'  => 'Transmission intégrale'
            ],
            [
                'en_short' => '4WD',
                'en_full'  => 'Four-Wheel Drive',
                'fr_full'  => 'Quatre roues motrices'
            ],
        ];

        foreach ($driverTypes as $type) {
            // Main Table-e English Full Name ba Short Name insert korun
            $id = DB::table('driver_types')->insertGetId([
                'title'      => $type['en_short'] . ' (' . $type['en_full'] . ')',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Translation Table-e French Meaningful name insert korun
            DB::table('driver_type_translations')->insert([
                'driver_type_id' => $id,
                'language'       => 'fr',
                'title'          => $type['fr_full'],
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }

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
        $emissionClasses = [
              ['en' => 'Euro 4', 'fr' => 'Norme Euro 4'],
              ['en' => 'Euro 5', 'fr' => 'Norme Euro 5'],
              ['en' => 'Euro 6', 'fr' => 'Norme Euro 6'],
              ['en' => 'Euro 6d', 'fr' => 'Norme Euro 6d'],
              ['en' => 'Euro 6temp', 'fr' => 'Norme Euro 6d-TEMP'],
          ];

        foreach ($emissionClasses as $class) {
            // 1. Main Table-e English title insert
            $id = DB::table('emission_classes')->insertGetId([
                'title'      => $class['en'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // 2. Translation Table-e French title insert
            DB::table('emission_class_translations')->insert([
                'emission_class_id' => $id,
                'language'          => 'fr',
                'title'             => $class['fr'],
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);
        }

        // Axle Counts
        DB::table('axle_counts')->insert([
            ['count' => 2],
            ['count' => 3],
        ]);

        // Equipments
        $equipmentList = [
                  ['en' => 'Air Conditioning', 'fr' => 'Climatisation'],
                  ['en' => 'Leather Seats',    'fr' => 'Sièges en cuir'],
                  ['en' => 'Navigation System','fr' => 'Système de navigation'],
                  ['en' => 'Parking Sensors',  'fr' => 'Capteurs de stationnement'],
              ];

        foreach ($equipmentList as $item) {
            // 1. Main equipment table-e insert kora
            $id = DB::table('equipment')->insertGetId([
                'title'      => $item['en'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // 2. Equipment translation table-e insert kora
            DB::table('equipment_translations')->insert([
                'equipment_id' => $id,
                'language'     => 'fr',
                'title'        => $item['fr'],
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }
    }
}
