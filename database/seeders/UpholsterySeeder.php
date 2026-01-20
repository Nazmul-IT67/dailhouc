<?php

namespace Database\Seeders;

use App\Models\Upholstery;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpholsterySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ডুপ্লিকেট এড়াতে টেবিল ট্রাঙ্কেট করা (ঐচ্ছিক)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Upholstery::truncate();
        DB::table('upholstery_translations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = Carbon::now();

        $upholsteries = [
            ['en' => 'Leather',   'fr' => 'Cuir'],
            ['en' => 'Fabric',    'fr' => 'Tissu'],
            ['en' => 'Alcantara', 'fr' => 'Alcantara'],
            ['en' => 'Vinyl',     'fr' => 'Vinyle'],
            ['en' => 'Suede',     'fr' => 'Suède'],
        ];

        foreach ($upholsteries as $item) {
            // ১. মেইন Upholstery টেবিলে ইনসার্ট
            $newUpholstery = Upholstery::create([
                'name' => $item['en'],
            ]);

            // ২. ট্রান্সলেশন টেবিলে ফ্রেঞ্চ ডেটা ইনসার্ট
            // টেবিলের নাম 'upholstery_translations' এবং ফরেন কি 'upholstery_id' মিলিয়ে নিন
            DB::table('upholstery_translations')->insert([
                'upholstery_id' => $newUpholstery->id,
                'language'      => 'fr',
                'name'          => $item['fr'],
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }
    }
}
