<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use App\Models\CarModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        CarModel::truncate();
        Brand::truncate();
        // Translation table-o truncate kora bhalo jate duplicate na hoy
        DB::table('brand_translations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = Carbon::now();

        // Brand list with French translations
        $brands = [
            // category_id 3 (Bikes)
            ['name' => 'Honda', 'category_id' => 3, 'fr' => 'Honda'],
            ['name' => 'Yamaha', 'category_id' => 3, 'fr' => 'Yamaha'],
            ['name' => 'Harley-Davidson', 'category_id' => 3, 'fr' => 'Harley-Davidson'],

            // category_id 2 (Cars)
            ['name' => 'Lamborghini', 'category_id' => 2, 'fr' => 'Lamborghini'],
            ['name' => 'Porsche', 'category_id' => 2, 'fr' => 'Porsche'],
            ['name' => 'Ferrari', 'category_id' => 2, 'fr' => 'Ferrari'],
            ['name' => 'Audi', 'category_id' => 2, 'fr' => 'Audi'],
            ['name' => 'BMW', 'category_id' => 2, 'fr' => 'BMW'],
            ['name' => 'Toyota', 'category_id' => 2, 'fr' => 'Toyota'],
            ['name' => 'Tesla', 'category_id' => 2, 'fr' => 'Tesla'],
        ];

        foreach ($brands as $item) {
            // 1. Prothome main brand table-e data insert korchi
            $brand = Brand::create([
                'name' => $item['name'],
                'category_id' => $item['category_id'],
            ]);

            // 2. Tarpor brand_translations table-e French data insert korchi
            DB::table('brand_translations')->insert([
                'brand_id'    => $brand->id,
                'language'    => 'fr', // Apnar column 'language' hole 'fr' hobe
                'name'        => $item['fr'],
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }
    }
}
