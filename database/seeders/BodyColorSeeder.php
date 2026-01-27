<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BodyColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('body_colors')->truncate();
        
        if (Schema::hasTable('body_color_translations')) {
            DB::table('body_color_translations')->truncate();
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $colors = [
            ['en' => 'White', 'fr' => 'Blanc', 'code' => '#FFFFFF'],
            ['en' => 'Black', 'fr' => 'Noir', 'code' => '#000000'],
            ['en' => 'Silver', 'fr' => 'Argent', 'code' => '#C0C0C0'],
            ['en' => 'Gray', 'fr' => 'Gris', 'code' => '#808080'],
            ['en' => 'Red', 'fr' => 'Rouge', 'code' => '#FF0000'],
            ['en' => 'Blue', 'fr' => 'Bleu', 'code' => '#0000FF'],
            ['en' => 'Green', 'fr' => 'Vert', 'code' => '#008000'],
            ['en' => 'Yellow', 'fr' => 'Jaune', 'code' => '#FFFF00'],
            ['en' => 'Orange', 'fr' => 'Orange', 'code' => '#FFA500'],
            ['en' => 'Brown', 'fr' => 'Marron', 'code' => '#A52A2A'],
        ];

        foreach ($colors as $colorData) {
            $colorId = DB::table('body_colors')->insertGetId([
                'name'       => $colorData['en'],
                'color_code' => $colorData['code']
            ]);

            DB::table('body_color_translations')->insert([
                'body_color_id' => $colorId,
                'language'      => 'fr',
                'name'         => $colorData['fr']
            ]);
        }
    }
}