<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@admin.com'], // prevent duplicates
            [
                'name' => 'Admin',
                'password' => Hash::make('12345678'),
                // 'is_admin' => true, // optional
                'email_verified_at' => Carbon::now(),
            ]
        );
    }
}
