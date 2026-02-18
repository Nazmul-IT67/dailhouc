<?php

namespace Database\Seeders;

use App\Models\ModelYear;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelYearSeeder extends Seeder
{
    public function run(): void
    {
        
        $years = range(2030, 1950);
        foreach ($years as $year) {
            ModelYear::updateOrCreate(
                ['year' => $year],
                ['year' => $year]
            );
        }
    }
}