<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PowerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('powers')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $powers = [
            ['value' => '55',  'unit' => 'KW', 'hp' => 74.80],
            ['value' => '75',  'unit' => 'KW', 'hp' => 101.97],
            ['value' => '100', 'unit' => 'KW', 'hp' => 135.96],
            ['value' => '150', 'unit' => 'KW', 'hp' => 203.94],
            ['value' => '200', 'unit' => 'KW', 'hp' => 271.92],
            ['value' => '250', 'unit' => 'KW', 'hp' => 339.90],
            ['value' => '300', 'unit' => 'KW', 'hp' => 407.89],
        ];

        foreach ($powers as $power) {
            DB::table('powers')->insert([
                'value'      => $power['value'],
                'unit'       => $power['unit'],
                'power_hp'   => $power['hp']
            ]);
        }
    }
}