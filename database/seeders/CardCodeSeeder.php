<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\CardCode;

class CardCodeSeeder extends Seeder
{
    public function run()
    {
        for ($i = 0; $i < 20; $i++) {
            CardCode::create([
                'code' => Str::random(20),
            ]);
        }
    }
}
