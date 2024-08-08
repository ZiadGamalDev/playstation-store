<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->method() == 'POST') {
            return [
                'name' => 'required|string|max:255|unique:countries',
                'flag' => 'required|image|max:2048|mimes:png,jpg,jpeg',
            ];
        }

        return [
            'name' => 'required|string|max:255',
            'flag' => 'required|image|max:2048|mimes:png,jpg,jpeg',
        ];
    }
}
