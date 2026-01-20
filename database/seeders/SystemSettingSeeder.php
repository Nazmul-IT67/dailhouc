<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemSetting::create([
            'title'          => 'My Application',
            'email'          => 'admin@example.com',
            'system_name'    => 'Admin Panel',
            'copyright_text' => '© 2025 My Application. All rights reserved.',
            'logo'           => 'logo.png',
            'favicon'        => 'favicon.ico',
            'description'    => 'This is the default system setting created by seeder.',
        ]);
    }
}
