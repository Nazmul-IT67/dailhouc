<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InteriorColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('interior_colors')->truncate();
        
        if (Schema::hasTable('interior_color_translations')) {
            DB::table('interior_color_translations')->truncate();
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $colors = [
            ['en' => 'Black', 'fr' => 'Noir', 'code' => '#000000'],
            ['en' => 'Beige', 'fr' => 'Beige', 'code' => '#F5F5DC'],
            ['en' => 'Grey', 'fr' => 'Gris', 'code' => '#808080'],
            ['en' => 'Brown', 'fr' => 'Marron', 'code' => '#A52A2A'],
            ['en' => 'Red', 'fr' => 'Rouge', 'code' => '#FF0000'],
            ['en' => 'White', 'fr' => 'Blanc', 'code' => '#FFFFFF'],
            ['en' => 'Tan', 'fr' => 'Tanné', 'code' => '#D2B48C'],
            ['en' => 'Other', 'fr' => 'Autre', 'code' => null],
        ];

        foreach ($colors as $colorData) {
            $colorId = DB::table('interior_colors')->insertGetId([
                'name'       => $colorData['en'],
                'color_code' => $colorData['code']
            ]);

            DB::table('interior_color_translations')->insert([
                'interior_color_id' => $colorId,
                'language'          => 'fr',
                'name'             => $colorData['fr']
            ]);
        }
    }
}