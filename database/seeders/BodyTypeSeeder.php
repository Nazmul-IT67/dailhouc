<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BodyTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('body_types')->truncate();
        DB::table('body_type_translations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = now();

        $bodyTypes = [
            // --- Category 1: Car ---
            ['en' => 'Sedan', 'fr' => 'Berline', 'cat' => 1, 'icon' => 'sedan.png'],
            ['en' => 'SUV', 'fr' => 'VUS', 'cat' => 1, 'icon' => 'suv.png'],
            ['en' => 'Coupe', 'fr' => 'Coupé', 'cat' => 1, 'icon' => 'coupe.png'],
            ['en' => 'Convertible', 'fr' => 'Cabriolet', 'cat' => 1, 'icon' => 'convertible.png'],

            // --- Category 2: Motorbike ---
            ['en' => 'Sportbike', 'fr' => 'Moto Sportive', 'cat' => 2, 'icon' => 'sportbike.png'],
            ['en' => 'Cruiser', 'fr' => 'Cruiser', 'cat' => 2, 'icon' => 'cruiser.png'],
            ['en' => 'Scooter', 'fr' => 'Scooter', 'cat' => 2, 'icon' => 'scooter.png'],

            // --- Category 3: Caravan ---
            ['en' => 'Travel Trailer', 'fr' => 'Roulotte', 'cat' => 3, 'icon' => 'trailer.png'],
            ['en' => 'Motorhome', 'fr' => 'Autocaravane', 'cat' => 3, 'icon' => 'motorhome.png'],

            // --- Category 4: Transporter ---
            ['en' => 'Panel Van', 'fr' => 'Fourgonnette', 'cat' => 4, 'icon' => 'van.png'],
            ['en' => 'Minibus', 'fr' => 'Minibus', 'cat' => 4, 'icon' => 'minibus.png'],

            // --- Category 5: Trail (Trailer) ---
            ['en' => 'Cargo Trailer', 'fr' => 'Remorque fermée', 'cat' => 5, 'icon' => 'cargo.png'],
            ['en' => 'Utility Trailer', 'fr' => 'Remorque utilitaire', 'cat' => 5, 'icon' => 'utility.png'],
        ];

        foreach ($bodyTypes as $type) {
            // Main Table-e Insert
            $bodyTypeId = DB::table('body_types')->insertGetId([
                'category_id' => $type['cat'],
                'title'       => $type['en'],
                'icon'        => $type['icon'],
            ]);

            // French Translation
            DB::table('body_type_translations')->insert([
                'body_type_id' => $bodyTypeId,
                'language'     => 'fr',
                'title'        => $type['fr']
            ]);
        }
    }
}