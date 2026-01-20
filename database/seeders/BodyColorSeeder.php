<?php

namespace Database\Seeders;

use App\Models\BodyColor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BodyColorSeeder extends Seeder
{
    public function run(): void
    {
        // Table reset kora (Optional)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        BodyColor::truncate();
        DB::table('body_color_translations')->truncate(); // Table name check kore nin
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = Carbon::now();

        $colors = [
            ['en' => 'White',  'fr' => 'Blanc'],
            ['en' => 'Black',  'fr' => 'Noir'],
            ['en' => 'Silver', 'fr' => 'Argent'],
            ['en' => 'Gray',   'fr' => 'Gris'],
            ['en' => 'Blue',   'fr' => 'Bleu'],
            ['en' => 'Red',    'fr' => 'Rouge'],
            ['en' => 'Green',  'fr' => 'Vert'],
            ['en' => 'Yellow', 'fr' => 'Jaune'],
            ['en' => 'Brown',  'fr' => 'Marron'],
            ['en' => 'Orange', 'fr' => 'Orange'],
            ['en' => 'Gold',   'fr' => 'Or'],
            ['en' => 'Purple', 'fr' => 'Violet'],
        ];

        foreach ($colors as $color) {
            // 1. Main BodyColor Table-e insert
            $newColor = BodyColor::create([
                'name' => $color['en'],
            ]);

            // 2. Translation Table-e insert
            DB::table('body_color_translations')->insert([
                'body_color_id' => $newColor->id,
                'language'      => 'fr',
                'name'          => $color['fr'],
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }
    }
}
