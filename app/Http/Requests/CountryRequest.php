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
        $mandatory = $this->method() == 'POST' ? 'required' : 'sometimes';

        return [
            'name' => "$mandatory|string|max:255" . ($this->method() == 'POST' ? '|unique:countries' : ''),
            'flag' => "$mandatory|image|max:2048|mimes:png,jpg,jpeg",
        ];
    }
}
