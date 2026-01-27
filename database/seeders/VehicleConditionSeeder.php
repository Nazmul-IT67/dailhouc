<?php

namespace Database\Seeders;

use App\Models\VehicleCondition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $conditions = [
            ['name' => 'New', 'description' => 'Brand new vehicle'],
            ['name' => 'Used', 'description' => 'Previously owned vehicle'],
            ['name' => 'Certified Pre-Owned', 'description' => 'Inspected and certified'],
        ];

        foreach ($conditions as $condition) {
            VehicleCondition::create($condition);
        }
    }
}
