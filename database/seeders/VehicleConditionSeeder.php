<?php

namespace Database\Seeders;

use App\Models\VehicleCondition;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VehicleConditionSeeder extends Seeder
{
    public function run(): void
    {
        // Jodi truncate korte chan (optional)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        VehicleCondition::truncate();
        DB::table('vehicle_condition_translations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = Carbon::now();

        $conditions = [
            [
                'name' => 'New',
                'description' => 'Brand new vehicle',
                'fr_name' => 'Neuf',
                'fr_desc' => 'Véhicule neuf'
            ],
            [
                'name' => 'Used',
                'description' => 'Previously owned vehicle',
                'fr_name' => 'Occasion',
                'fr_desc' => 'Véhicule d\'occasion'
            ],
            [
                'name' => 'Certified Pre-Owned',
                'description' => 'Inspected and certified',
                'fr_name' => 'Certifié',
                'fr_desc' => 'Inspecté et certifié'
            ],
        ];

        foreach ($conditions as $condition) {
            // 1. Main Table-e save kora
            $newCondition = VehicleCondition::create([
                'name' => $condition['name'],
                'description' => $condition['description'],
            ]);

            // 2. Translation Table-e save kora
            DB::table('vehicle_condition_translations')->insert([
                'vehicle_condition_id' => $newCondition->id,
                'language'             => 'fr',
                'name'                 => $condition['fr_name'],
                'description'          => $condition['fr_desc'],
                'created_at'           => $now,
                'updated_at'           => $now,
            ]);
        }
    }
}
