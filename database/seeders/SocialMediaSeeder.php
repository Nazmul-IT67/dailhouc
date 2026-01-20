<?php

namespace Database\Seeders;

use App\Models\SocialMedia;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SocialMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $socials = [
            [
                'social_media' => 'facebook',
                'profile_link' => 'https://facebook.com/yourpage',
            ],
            [
                'social_media' => 'twitter',
                'profile_link' => 'https://twitter.com/yourprofile',
            ],
            [
                'social_media' => 'instagram',
                'profile_link' => 'https://instagram.com/yourprofile',
            ],
        ];

        foreach ($socials as $social) {
            SocialMedia::updateOrCreate(
                ['social_media' => $social['social_media']],
                ['profile_link' => $social['profile_link']]
            );
        }
    }
}
