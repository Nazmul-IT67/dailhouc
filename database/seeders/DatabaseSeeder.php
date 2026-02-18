<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            AdminUserSeeder::class,
            SystemSettingSeeder::class,
            SocialMediaSeeder::class,
            BlogSeeder::class,
            DynamicPagesSeeder::class,
            LocationSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            CarModelSeeder::class,
            SubModelSeeder::class,
            VehicleConditionSeeder::class,
            BodyColorSeeder::class,
            UpholsterySeeder::class,
            InteriorColorSeeder::class,
            VehicleReferenceSeeder::class,
            BedTypeAndCountSeeder::class,
            BodyTypeSeeder::class,
            FuelSeeder::class,
            EquipmentSeeder::class,
            PowerSeeder::class,
            EquipmentLineSeeder::class,
            SellerTypeSeeder::class,
            VehicleSeeder::class,
            ModelYearSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@user.com',
            'password' => Hash::make('12345678'),
        ]);
    }
}
