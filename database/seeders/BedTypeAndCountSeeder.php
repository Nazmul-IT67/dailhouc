<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BedTypeAndCountSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // --- 1. Bed Types Translation ---
        $bedTypes = [
            ['en' => 'Single', 'fr' => 'Simple'],
            ['en' => 'Double', 'fr' => 'Double'],
            ['en' => 'Queen',  'fr' => 'Grand lit'],
            ['en' => 'King',   'fr' => 'Très grand lit'],
            ['en' => 'Twin',   'fr' => 'Lits jumeaux'],
            ['en' => 'Bunk',   'fr' => 'Lits superposés'],
        ];

        foreach ($bedTypes as $type) {
            $id = DB::table('bed_types')->insertGetId([
                'name'       => $type['en'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('bed_type_translations')->insert([
                'bed_type_id' => $id,
                'language'    => 'fr',
                'name'        => $type['fr'],
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        // --- 2. Bed Counts Translation ---
        $bedCounts = [
            ['en' => '1 Bed',  'fr' => '1 Lit'],
            ['en' => '2 Beds', 'fr' => '2 Lits'],
            ['en' => '3 Beds', 'fr' => '3 Lits'],
            ['en' => '4 Beds', 'fr' => '4 Lits'],
            ['en' => '5 Beds', 'fr' => '5 Lits'],
        ];

        foreach ($bedCounts as $count) {
            $id = DB::table('bed_counts')->insertGetId([
                'number'     => $count['en'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('bed_count_translations')->insert([
                'bed_count_id' => $id,
                'language'     => 'fr',
                'number'       => $count['fr'],
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }
    }
}
