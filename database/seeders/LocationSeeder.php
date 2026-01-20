<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;
use App\Models\Country;
use App\Models\City;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // Insert Currencies
        $usd = Currency::updateOrCreate(
            ['code' => 'USD'],
            ['name' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 1, 'is_default' => 1]
        );

        $bdt = Currency::updateOrCreate(
            ['code' => 'BDT'],
            ['name' => 'Bangladeshi Taka', 'symbol' => '৳', 'exchange_rate' => 110]
        );

        // Insert Countries
        $usa = Country::updateOrCreate(
            ['code' => 'US'],
            ['name' => 'United States', 'currency_id' => $usd->id]
        );

        $bangladesh = Country::updateOrCreate(
            ['code' => 'BD'],
            ['name' => 'Bangladesh', 'currency_id' => $bdt->id]
        );

        // Insert Cities
        City::updateOrCreate(['name' => 'New York', 'country_id' => $usa->id]);
        City::updateOrCreate(['name' => 'Los Angeles', 'country_id' => $usa->id]);
        City::updateOrCreate(['name' => 'Dhaka', 'country_id' => $bangladesh->id]);
        City::updateOrCreate(['name' => 'Chittagong', 'country_id' => $bangladesh->id]);
    }
}
