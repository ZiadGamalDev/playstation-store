<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            [
                'name' => 'USA',
                'flag' => 'images/countries/usa.jpg',
            ],
            [
                'name' => 'UAE',
                'flag' => 'images/countries/uae.jpg',
            ],
            [
                'name' => 'UK',
                'flag' => 'images/countries/uk.jpg',
            ],
            [
                'name' => 'Lebanon',
                'flag' => 'images/countries/lebanon.jpg',
            ],
            [
                'name' => 'Bahrain',
                'flag' => 'images/countries/bahrain.jpg',
            ],
            [
                'name' => 'Qatar',
                'flag' => 'images/countries/qatar.jpg',
            ],
            [
                'name' => 'KSA',
                'flag' => 'images/countries/ksa.jpg',
            ],
        ];

        foreach($countries as $country) {
            $path = database_path('seeders/' . $country['flag']);
            if (file_exists($path)) {
                Storage::disk('public')->put($country['flag'], file_get_contents($path));
            }
            Country::create($country);
        }
    }
}
