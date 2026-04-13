<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
<<<<<<< HEAD
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class carModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('car_models')->truncate();
        DB::table('car_model_translations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = now();

        $models = [
            // 1. Honda (Brand ID: 1)
            ['name' => 'Civic', 'brand_id' => 1, 'en' => 'Civic', 'fr' => 'Civic'],
            ['name' => 'CR-V', 'brand_id' => 1, 'en' => 'CR-V', 'fr' => 'CR-V'],

            // 2. Yamaha (Brand ID: 2)
            ['name' => 'MT-07', 'brand_id' => 2, 'en' => 'MT-07', 'fr' => 'MT-07'],
            ['name' => 'R1', 'brand_id' => 2, 'en' => 'YZF-R1', 'fr' => 'YZF-R1'],

            // 3. Harley-Davidson (Brand ID: 3)
            ['name' => 'Iron 883', 'brand_id' => 3, 'en' => 'Iron 883', 'fr' => 'Iron 883'],

            // 4. Lamborghini (Brand ID: 4)
            ['name' => 'Urus', 'brand_id' => 4, 'en' => 'Urus', 'fr' => 'Urus'],
            ['name' => 'Aventador', 'brand_id' => 4, 'en' => 'Aventador', 'fr' => 'Aventador'],

            // 5. Porsche (Brand ID: 5)
            ['name' => '911 Carrera', 'brand_id' => 5, 'en' => '911 Carrera', 'fr' => '911 Carrera'],

            // 6. Ferrari (Brand ID: 6)
            ['name' => '488 GTB', 'brand_id' => 6, 'en' => '488 GTB', 'fr' => '488 GTB'],

            // 7. Audi (Brand ID: 7)
            ['name' => 'A4', 'brand_id' => 7, 'en' => 'A4', 'fr' => 'A4'],
            ['name' => 'Q7', 'brand_id' => 7, 'en' => 'Q7', 'fr' => 'Q7'],

            // 8. BMW (Brand ID: 8)
            ['name' => 'X5', 'brand_id' => 8, 'en' => 'X5', 'fr' => 'X5'],
            ['name' => 'M4', 'brand_id' => 8, 'en' => 'M4', 'fr' => 'M4'],

            // 9. Toyota (Brand ID: 9)
            ['name' => 'Corolla', 'brand_id' => 9, 'en' => 'Corolla', 'fr' => 'Corolla'],
            ['name' => 'Camry', 'brand_id' => 9, 'en' => 'Camry', 'fr' => 'Camry'],

            // 10. Tesla (Brand ID: 10)
            ['name' => 'Model S', 'brand_id' => 10, 'en' => 'Model S', 'fr' => 'Modèle S'],
            ['name' => 'Model 3', 'brand_id' => 10, 'en' => 'Model 3', 'fr' => 'Modèle 3'],
        ];

        foreach ($models as $modelData) {
            // Main Table Insert
            $modelId = DB::table('car_models')->insertGetId([
                'brand_id'   => $modelData['brand_id'],
                'name'       => $modelData['name'],
            ]);

            // French Translation
            DB::table('car_model_translations')->insert([
                'car_model_id' => $modelId,
                'language'     => 'fr',
                'name'         => $modelData['fr'],
            ]);
        }
    }
}
=======
use App\Models\CarModel;
use App\Models\Brand;

class CarModelSeeder extends Seeder
{
    public function run(): void
    {
        $brands = Brand::pluck('id', 'name'); // ['Toyota' => 9, ...]

        $carModels = [
            // Honda Bikes
            ['name' => 'CBR 1000RR', 'brand_name' => 'Honda'],
            ['name' => 'CB 500F', 'brand_name' => 'Honda'],

            // Yamaha Bikes
            ['name' => 'YZF-R1', 'brand_name' => 'Yamaha'],
            ['name' => 'MT-07', 'brand_name' => 'Yamaha'],

            // BMW Cars
            ['name' => 'X5', 'brand_name' => 'BMW'],
            ['name' => 'M3', 'brand_name' => 'BMW'],

            // Toyota Cars
            ['name' => 'Corolla', 'brand_name' => 'Toyota'],
            ['name' => 'Camry', 'brand_name' => 'Toyota'],

            // Tesla Cars
            ['name' => 'Model S', 'brand_name' => 'Tesla'],
            ['name' => 'Model 3', 'brand_name' => 'Tesla'],
        ];

        foreach ($carModels as $model) {
            if (isset($brands[$model['brand_name']])) {
                CarModel::create([
                    'name' => $model['name'],
                    'brand_id' => $brands[$model['brand_name']],
                ]);
            }
        }
    }
}
>>>>>>> 2bdbe6e (first commit)
