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
            [
                'name' => 'UAE',
                'flag' => 'images/countries/uae.jpg',
            ],
            [
                'name' => 'USA',
                'flag' => 'images/countries/usa.jpg',
            ],
        ];

        foreach($countries as $country) {
            $image = file_get_contents(database_path('seeders/' . $country['flag']));
            Storage::disk('public')->put($country['flag'], $image);

            Country::create($country);
        }
    }
}
