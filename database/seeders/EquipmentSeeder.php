<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('equipment')->truncate();
        
        if (Schema::hasTable('equipment_translations')) {
            DB::table('equipment_translations')->truncate();
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $equipmentList = [
            ['en' => 'Air Conditioning', 'fr' => 'Climatisation'],
            ['en' => 'GPS Navigation', 'fr' => 'Système de navigation GPS'],
            ['en' => 'Bluetooth', 'fr' => 'Bluetooth'],
            ['en' => 'Sunroof', 'fr' => 'Toit ouvrant'],
            ['en' => 'Leather Seats', 'fr' => 'Sièges en cuir'],
            ['en' => 'Parking Sensors', 'fr' => 'Capteurs de stationnement'],
            ['en' => 'Backup Camera', 'fr' => 'Caméra de recul'],
            ['en' => 'Cruise Control', 'fr' => 'Régulateur de vitesse'],
            ['en' => 'ABS', 'fr' => 'ABS'],
            ['en' => 'Heated Seats', 'fr' => 'Sièges chauffants'],
        ];

        foreach ($equipmentList as $item) {
            $equipmentId = DB::table('equipment')->insertGetId([
                'title'      => $item['en']
            ]);

            DB::table('equipment_translations')->insert([
                'equipment_id' => $equipmentId,
                'language'     => 'fr',
                'title'        => $item['fr']
            ]);
        }
    }
}