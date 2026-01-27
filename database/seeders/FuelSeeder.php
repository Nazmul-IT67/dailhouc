<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FuelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('fuels')->truncate();
        
        if (Schema::hasTable('fuel_translations')) {
            DB::table('fuel_translations')->truncate();
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $fuels = [
            ['en' => 'Petrol', 'fr' => 'Essence', 'desc' => 'Standard gasoline fuel'],
            ['en' => 'Diesel', 'fr' => 'Diesel', 'desc' => 'Standard diesel fuel'],
            ['en' => 'Electric', 'fr' => 'Électrique', 'desc' => 'Electric battery power'],
            ['en' => 'Hybrid', 'fr' => 'Hybride', 'desc' => 'Combination of fuel and electric'],
            ['en' => 'LPG', 'fr' => 'GPL', 'desc' => 'Liquefied petroleum gas'],
            ['en' => 'CNG', 'fr' => 'GNV', 'desc' => 'Compressed natural gas'],
            ['en' => 'Hydrogen', 'fr' => 'Hydrogène', 'desc' => 'Hydrogen fuel cell technology'],
        ];

        foreach ($fuels as $fuelData) {
            $fuelId = DB::table('fuels')->insertGetId([
                'title'       => $fuelData['en'],
                'description' => $fuelData['desc'],
            ]);

            DB::table('fuel_translations')->insert([
                'fuel_id'    => $fuelId,
                'language'   => 'fr',
                'title'      => $fuelData['fr'],
            ]);
        }
    }
}