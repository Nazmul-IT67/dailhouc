<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarModel;
use App\Models\SubModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubModelSeeder extends Seeder
{
    public function run(): void
    {
        $carModels = CarModel::pluck('id', 'name');
        $now = Carbon::now();

        $subModels = [
            // Honda
            ['name' => 'CBR 1000RR SP', 'car_model_name' => 'CBR 1000RR', 'fr' => 'CBR 1000RR SP'],
            ['name' => 'CBR 500R', 'car_model_name' => 'CBR 1000RR', 'fr' => 'CBR 500R'],
            ['name' => 'CB 500F Deluxe', 'car_model_name' => 'CB 500F', 'fr' => 'CB 500F Deluxe'],

            // Yamaha
            ['name' => 'YZF-R1M', 'car_model_name' => 'YZF-R1', 'fr' => 'YZF-R1M'],
            ['name' => 'MT-07 ABS', 'car_model_name' => 'MT-07', 'fr' => 'MT-07 ABS'],

            // BMW
            ['name' => 'X5 M', 'car_model_name' => 'X5', 'fr' => 'X5 M'],
            ['name' => 'X5 xDrive', 'car_model_name' => 'X5', 'fr' => 'X5 xDrive'],
            ['name' => 'M3 Competition', 'car_model_name' => 'M3', 'fr' => 'M3 Compétition'],

            // Toyota
            ['name' => 'Corolla Altis', 'car_model_name' => 'Corolla', 'fr' => 'Corolla Altis'],
            ['name' => 'Corolla Cross', 'car_model_name' => 'Corolla', 'fr' => 'Corolla Cross'],
            ['name' => 'Camry Hybrid', 'car_model_name' => 'Camry', 'fr' => 'Camry Hybride'],

            // Tesla
            ['name' => 'Model S Plaid', 'car_model_name' => 'Model S', 'fr' => 'Modèle S Plaid'],
            ['name' => 'Model 3 Performance', 'car_model_name' => 'Model 3', 'fr' => 'Modèle 3 Performance'],
        ];

        foreach ($subModels as $sub) {
            if (isset($carModels[$sub['car_model_name']])) {
                // 1. Main SubModel Table-e insert
                $newSubModel = SubModel::create([
                    'name' => $sub['name'],
                    'car_model_id' => $carModels[$sub['car_model_name']],
                ]);

                // 2. SubModel Translation Table-e insert
                // টেবিলের নাম 'sub_model_translations' এবং ফরেন কি 'sub_model_id' চেক করে নিন
                DB::table('sub_model_translations')->insert([
                    'sub_model_id' => $newSubModel->id,
                    'language'     => 'fr',
                    'name'         => $sub['fr'],
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
            }
        }
    }
}
