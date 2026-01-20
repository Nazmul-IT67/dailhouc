<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Define Categories (English as default or base)
        $categories = [
            ['id' => 1, 'name' => 'Car', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Motorbike', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Caravan', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Transporter', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'name' => 'Trail', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('categories')->insert($categories);

        // 2. Define French Translations
        // ধরি আপনার ল্যাঙ্গুয়েজ কলামে 'fr' বসবে
        $translations = [
            ['category_id' => 1, 'language' => 'fr', 'name' => 'Voiture', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 2, 'language' => 'fr', 'name' => 'Moto', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 3, 'language' => 'fr', 'name' => 'Caravane', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 4, 'language' => 'fr', 'name' => 'Transporteur', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 5, 'language' => 'fr', 'name' => 'Piste', 'created_at' => $now, 'updated_at' => $now],
        ];

        // ধরি আপনার ট্রান্সলেশন টেবিলের নাম 'category_translations'
        DB::table('category_translations')->insert($translations);
    }
}
