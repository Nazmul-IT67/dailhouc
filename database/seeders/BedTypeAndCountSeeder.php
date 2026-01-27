<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BedTypeAndCountSeeder extends Seeder
{
    public function run(): void
    {

        DB::table('bed_types')->insert([
            ['name' => 'Single'],
            ['name' => 'Double'],
            ['name' => 'Queen'],
            ['name' => 'King'],
            ['name' => 'Twin'],
            ['name' => 'Bunk'],
        ]);
        DB::table('bed_counts')->insert([
            ['number' => '1 Bed'],
            ['number' => '2 Beds'],
            ['number' => '3 Beds'],
            ['number' => '4 Beds'],
            ['number' => '5 Beds'],
        ]);
    }
}
