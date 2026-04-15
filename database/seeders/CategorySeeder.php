<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $categories = [
            ['en' => 'Car', 'fr' => 'Voiture'],
            ['en' => 'Motorbike', 'fr' => 'Moto'],
            ['en' => 'Caravan', 'fr' => 'Caravane'],
            ['en' => 'Transporter', 'fr' => 'Transporteur'],
            ['en' => 'Trail', 'fr' => 'Piste'],
        ];

        foreach ($categories as $cat) {
            $categoryId = DB::table('categories')->insertGetId([
                'name' => $cat['en'],
            ]);

            DB::table('category_translations')->insert([
                [
                    'category_id' => $categoryId,
                    'language' => 'en',
                    'name' => $cat['en'],
                ],
                [
                    'category_id' => $categoryId,
                    'language' => 'fr',
                    'name' => $cat['fr'],
                ],
            ]);
        }
    }
}
