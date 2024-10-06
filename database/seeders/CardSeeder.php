<?php

namespace Database\Seeders;

use App\Models\Card;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CardSeeder extends Seeder
{
    public function run()
    {
        $cards = [
            [
                'title' => 'PSN 75$ USE',
                'description' => 'Buy PlayStation Network Card $75 (USE). This product is compatible only with a KSA PSN Account.',
                'image' => 'images/cards/1.jpg',
                'price' => 100,
                'discount' => null,
                'country_id' => 1,
                'type' => 'PlayStation',
            ],
            [
                'title' => 'PSN 100$ USE',
                'description' => 'Buy PlayStation Network Card $100 (USE). This product is compatible only with a UAE PSN Account.',
                'image' => 'images/cards/2.jpg',
                'price' => 200,
                'discount' => 150,
                'country_id' => 2,
                'type' => 'PlayStation',
            ]
        ];

        foreach ($cards as $card) {
            $this->storeImage($card['image']);
            Card::create($card);
        }
    }

    protected function storeImage(string $imagePath)
    {
        $sourcePath = database_path('seeders/images/' . basename($imagePath));
        if (file_exists($sourcePath)) {
            Storage::disk('public')->put($imagePath, file_get_contents($sourcePath));
        }
    }
}
