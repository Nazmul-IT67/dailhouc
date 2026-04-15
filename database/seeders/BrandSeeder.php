<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use App\Models\CarModel;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('car_models')->truncate(); 
        DB::table('brands')->truncate();
        DB::table('brand_translations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = now();

        // Data Structure: Category -> Brands -> Models
        $data = [
            // --- Category: Car (ID: 1) ---
            [
                'category_id' => 1,
                'brands' => [
                    ['name' => 'Toyota', 'models' => ['Corolla', 'Camry', 'RAV4', 'Supra']],
                    ['name' => 'BMW', 'models' => ['3 Series', 'X5', 'M4', 'i8']],
                    ['name' => 'Mercedes-Benz', 'models' => ['C-Class', 'E-Class', 'S-Class']],
                    ['name' => 'Audi', 'models' => ['A4', 'A6', 'Q7', 'R8']],
                    ['name' => 'Tesla', 'models' => ['Model S', 'Model 3', 'Model X']],
                ]
            ],
            // --- Category: Motorbike (ID: 2) ---
            [
                'category_id' => 2,
                'brands' => [
                    ['name' => 'Honda', 'models' => ['CBR500R', 'Africa Twin', 'Gold Wing']],
                    ['name' => 'Yamaha', 'models' => ['MT-07', 'YZF-R1', 'XSR900']],
                ]
            ],
            // --- Category: Caravan (ID: 3) ---
            [
                'category_id' => 3,
                'brands' => [
                    ['name' => 'Hobby', 'models' => ['Excellent', 'Prestige', 'Landhaus']],
                ]
            ],
            // --- Category: Transporter (ID: 4) ---
            [
                'category_id' => 4,
                'brands' => [
                    ['name' => 'Volkswagen', 'models' => ['Transporter T6', 'Crafter', 'Caddy']],
                ]
            ],
            // --- Category: Trail (ID: 5) ---
            [
                'category_id' => 5,
                'brands' => [
                    ['name' => 'Brenderup', 'models' => ['Series 1000', 'Series 2000']],
                ]
            ],
        ];

        foreach ($data as $item) {
            foreach ($item['brands'] as $brandData) {
                $brandId = DB::table('brands')->insertGetId([
                    'category_id' => $item['category_id'],
                    'name'        => $brandData['name'],
                ]);

                DB::table('brand_translations')->insert([
                    'brand_id' => $brandId,
                    'language' => 'fr',
                    'name'     => $brandData['name'],
                ]);
            }
        }
    }
}
