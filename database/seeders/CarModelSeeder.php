<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarModel;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CarModelSeeder extends Seeder
{
    public function run(): void
    {
        $brands = Brand::pluck('id', 'name');
        $now = Carbon::now();

        $carModels = [
            // Honda Bikes
            ['name' => 'CBR 1000RR', 'brand_name' => 'Honda', 'fr' => 'CBR 1000RR'],
            ['name' => 'CB 500F', 'brand_name' => 'Honda', 'fr' => 'CB 500F'],

            // Yamaha Bikes
            ['name' => 'YZF-R1', 'brand_name' => 'Yamaha', 'fr' => 'YZF-R1'],
            ['name' => 'MT-07', 'brand_name' => 'Yamaha', 'fr' => 'MT-07'],

            // BMW Cars
            ['name' => 'X5', 'brand_name' => 'BMW', 'fr' => 'X5'],
            ['name' => 'M3', 'brand_name' => 'BMW', 'fr' => 'M3'],

            // Toyota Cars
            ['name' => 'Corolla', 'brand_name' => 'Toyota', 'fr' => 'Corolla'],
            ['name' => 'Camry', 'brand_name' => 'Toyota', 'fr' => 'Camry'],

            // Tesla Cars
            ['name' => 'Model S', 'brand_name' => 'Tesla', 'fr' => 'Modèle S'],
            ['name' => 'Model 3', 'brand_name' => 'Tesla', 'fr' => 'Modèle 3'],
        ];

        foreach ($carModels as $model) {
            if (isset($brands[$model['brand_name']])) {
                // 1. Main CarModel table-e insert kora
                $newModel = CarModel::create([
                    'name' => $model['name'],
                    'brand_id' => $brands[$model['brand_name']],
                ]);

                // 2. CarModel Translation table-e French data insert kora
                DB::table('car_model_translations')->insert([
                    'car_model_id' => $newModel->id,
                    'language'     => 'fr', // Apnar database column name 'language' hole
                    'name'         => $model['fr'],
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
            }
        }
    }
}
