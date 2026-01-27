<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SubModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('sub_models')->truncate();
        if (Schema::hasTable('sub_model_translations')) {
            DB::table('sub_model_translations')->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = now();

        $subModels = [
            // --- HONDA (Model ID: 1, 2) ---
            ['model_id' => 1, 'name' => 'Type R', 'en' => 'Type R', 'fr' => 'Type R'],
            ['model_id' => 1, 'name' => 'Si', 'en' => 'Si', 'fr' => 'Si Sport'],
            ['model_id' => 2, 'name' => 'Hybrid', 'en' => 'Hybrid', 'fr' => 'Hybride'],

            // --- YAMAHA (Model ID: 3, 4) ---
            ['model_id' => 3, 'name' => 'ABS', 'en' => 'ABS Edition', 'fr' => 'Édition ABS'],
            ['model_id' => 4, 'name' => 'M Edition', 'en' => 'M Performance', 'fr' => 'M Performance'],

            // --- HARLEY (Model ID: 5) ---
            ['model_id' => 5, 'name' => 'Custom', 'en' => 'Custom Look', 'fr' => 'Look Personnalisé'],

            // --- LAMBORGHINI (Model ID: 6, 7) ---
            ['model_id' => 6, 'name' => 'Performante', 'en' => 'Performante', 'fr' => 'Performante'],
            ['model_id' => 7, 'name' => 'SVJ', 'en' => 'SVJ Roadster', 'fr' => 'SVJ Roadster'],

            // --- PORSCHE (Model ID: 8) ---
            ['model_id' => 8, 'name' => 'Turbo S', 'en' => 'Turbo S', 'fr' => 'Turbo S'],

            // --- FERRARI (Model ID: 9) ---
            ['model_id' => 9, 'name' => 'Spider', 'en' => 'Spider', 'fr' => 'Spider'],

            // --- AUDI (Model ID: 10, 11) ---
            ['model_id' => 10, 'name' => 'Premium Plus', 'en' => 'Premium Plus', 'fr' => 'Premium Plus'],
            ['model_id' => 11, 'name' => 'S-Line', 'en' => 'S-Line', 'fr' => 'S-Line'],

            // --- BMW (Model ID: 12, 13) ---
            ['model_id' => 12, 'name' => 'xDrive', 'en' => 'xDrive AWD', 'fr' => 'xDrive Traction Intégrale'],
            ['model_id' => 13, 'name' => 'Competition', 'en' => 'Competition Package', 'fr' => 'Pack Compétition'],

            // --- TOYOTA (Model ID: 14, 15) ---
            ['model_id' => 14, 'name' => 'XLE', 'en' => 'XLE Premium', 'fr' => 'XLE Premium'],
            ['model_id' => 15, 'name' => 'TRD', 'en' => 'TRD Off-Road', 'fr' => 'TRD Hors Route'],

            // --- TESLA (Model ID: 16, 17) ---
            ['model_id' => 16, 'name' => 'Plaid', 'en' => 'Plaid Edition', 'fr' => 'Édition Plaid'],
            ['model_id' => 17, 'name' => 'Long Range', 'en' => 'Long Range', 'fr' => 'Grande Autonomie'],
        ];

        foreach ($subModels as $data) {
            // Main Sub Model Table-e insert
            $subModelId = DB::table('sub_models')->insertGetId([
                'car_model_id' => $data['model_id'],
                'name'         => $data['name'],
            ]);

            // French Translation
            DB::table('sub_model_translations')->insert([
                'sub_model_id' => $subModelId,
                'language'     => 'fr',
                'name'         => $data['fr'],
            ]);
        }
    }
}