<?php

namespace App\Http\Controllers;

use App\Http\Requests\CountryRequest;
use App\Models\Country;
use Illuminate\Support\Facades\Storage;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::all();
        
        return $this->respondWithData('Countries retrieved successfully', $countries, 200);
    }

    public function store(CountryRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('flag')) {
            $data['flag'] = $request->file('flag')->store('images/countries', 'public');
        }

        $country = Country::create($data);

        return $this->respondWithData('Country created successfully', $country, 201);
    }

    public function show(Country $country)
    {
        return $this->respondWithData('Country retrieved successfully', $country, 200);
    }

    public function update(CountryRequest $request, Country $country)
    {
        $data = $request->validated();
        if ($request->hasFile('flag')) {
            if ($country->flag) {
                Storage::disk('public')->delete($country->flag);
            }
            $data['flag'] = $request->file('flag')->store('images/countries', 'public');
        }

        $country->update($data);

        return $this->respondWithData('Country updated successfully', $country, 200);
    }

    public function destroy(Country $country)
    {
        if ($country->flag) {
            Storage::disk('public')->delete($country->flag);
        }
        $country->delete();

        return $this->successResponse('Country deleted successfully', 200);
    }
}
