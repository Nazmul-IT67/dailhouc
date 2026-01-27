<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EquipmentLineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('equipment_lines')->truncate();
        
        if (Schema::hasTable('equipment_line_translations')) {
            DB::table('equipment_line_translations')->truncate();
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $lines = [
            ['en' => 'Standard', 'fr' => 'Standard'],
            ['en' => 'Executive', 'fr' => 'Exécutif'],
            ['en' => 'Luxury', 'fr' => 'Luxe'],
            ['en' => 'Sport', 'fr' => 'Sport'],
            ['en' => 'Premium', 'fr' => 'Premium'],
            ['en' => 'Business', 'fr' => 'Affaires'],
            ['en' => 'Comfort', 'fr' => 'Confort'],
        ];

        foreach ($lines as $lineData) {
            $lineId = DB::table('equipment_lines')->insertGetId([
                'title'      => $lineData['en']
            ]);

            DB::table('equipment_line_translations')->insert([
                'equipment_line_id' => $lineId,
                'language'          => 'fr',
                'title'             => $lineData['fr']
            ]);
        }
    }
}