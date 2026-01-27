<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SellerTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('seller_types')->truncate();
        
        if (Schema::hasTable('seller_type_translations')) {
            DB::table('seller_type_translations')->truncate();
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $sellerTypes = [
            ['en' => 'Private Seller', 'fr' => 'Vendeur Particulier'],
            ['en' => 'Dealer', 'fr' => 'Concessionnaire'],
            ['en' => 'Professional', 'fr' => 'Professionnel'],
        ];

        foreach ($sellerTypes as $type) {
            $sellerTypeId = DB::table('seller_types')->insertGetId([
                'title'      => $type['en']
            ]);

            DB::table('seller_type_translations')->insert([
                'seller_type_id' => $sellerTypeId,
                'language'       => 'fr',
                'title'          => $type['fr']
            ]);
        }
    }
}