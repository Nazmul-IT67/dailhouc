<?php

namespace Database\Seeders;

use App\Models\InteriorColor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InteriorColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Purono data clear kora (Optional)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        InteriorColor::truncate();
        DB::table('interior_color_translations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = Carbon::now();

        $colors = [
            ['en' => 'Black', 'fr' => 'Noir',  'code' => '#000000'],
            ['en' => 'Beige', 'fr' => 'Beige', 'code' => '#F5F5DC'],
            ['en' => 'Brown', 'fr' => 'Marron', 'code' => '#A52A2A'],
            ['en' => 'Gray',  'fr' => 'Gris',   'code' => '#808080'],
            ['en' => 'Red',   'fr' => 'Rouge',  'code' => '#FF0000'],
            ['en' => 'White', 'fr' => 'Blanc',  'code' => '#FFFFFF'],
        ];

        foreach ($colors as $color) {
            // 1. Main InteriorColor Table-e insert
            $newColor = InteriorColor::create([
                'name'       => $color['en'],
                'color_code' => $color['code'],
            ]);

            // 2. Translation Table-e French data insert
            // Table name: interior_color_translations, FK: interior_color_id
            DB::table('interior_color_translations')->insert([
                'interior_color_id' => $newColor->id,
                'language'          => 'fr',
                'name'              => $color['fr'],
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);
        }
    }
}
