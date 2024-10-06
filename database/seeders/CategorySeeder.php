<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'PlayStation',
                'description' => 'PlayStation Network Category.',
                'image' => 'images/categories/playstation.png',
            ],
            [
                'name' => 'Google Play',
                'description' => 'Google Play Network Category.',
                'image' => 'images/categories/google-play.png',
            ],
            [
                'name' => 'Itunes',
                'description' => 'Itunes Network Category.',
                'image' => 'images/categories/itunes.png',
            ],
            [
                'name' => 'Steam',
                'description' => 'Steam Network Category.',
                'image' => 'images/categories/steam.png',
            ],
            [
                'name' => 'Recharge',
                'description' => 'Recharge Network Category.',
                'image' => 'images/categories/recharge.png',
            ],
        ];

        foreach ($categories as $category) {
            $path = database_path('seeders/' . $category['image']);
            if (file_exists($path)) {
                Storage::disk('public')->put($category['image'], file_get_contents($path));
            }
            Category::create($category);
        }
    }
}
