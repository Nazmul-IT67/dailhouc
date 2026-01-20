<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Blog;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blogs = [
            [
                'title'   => 'Getting Started with Laravel 10',
                'content' => 'Laravel 10 is the latest version of the Laravel framework. In this blog, we will explore its new features and improvements.',
                'slug'    => Str::slug('Getting Started with Laravel 10'),
                'status'  => 'published',
                'image'   => null,
            ],
            [
                'title'   => 'Top 5 PHP Tips for Clean Code',
                'content' => 'Writing clean code in PHP is essential for maintainability. Here are the top 5 tips to keep your codebase clean.',
                'slug'    => Str::slug('Top 5 PHP Tips for Clean Code'),
                'status'  => 'published',
                'image'   => null,
            ],
            [
                'title'   => 'How to Deploy Laravel on Shared Hosting',
                'content' => 'Deploying a Laravel project to shared hosting can be tricky. In this article, I will guide you step by step.',
                'slug'    => Str::slug('How to Deploy Laravel on Shared Hosting'),
                'status'  => 'draft',
                'image'   => null,
            ],
        ];

        Blog::insert($blogs);
    }
}
