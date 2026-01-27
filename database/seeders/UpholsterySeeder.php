<?php

namespace Database\Seeders;

use App\Models\Upholstery;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpholsterySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $upholsteries = [
            ['name' => 'Leather'],
            ['name' => 'Fabric'],
            ['name' => 'Alcantara'],
            ['name' => 'Vinyl'],
            ['name' => 'Suede'],
        ];

        foreach ($upholsteries as $upholstery) {
            Upholstery::create($upholstery);
        }
    }
}
