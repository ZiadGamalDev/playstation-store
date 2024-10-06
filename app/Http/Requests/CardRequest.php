<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $mandatory = $this->method() == 'POST' ? 'required' : 'sometimes';

        return [
            'title' => "$mandatory|string|max:255",
            'description' => 'nullable|string|max:1000',
            'image' => "$mandatory|image|max:2048|mimes:png,jpg,jpeg",
            'price' => "$mandatory|numeric",
            'discount' => 'nullable|numeric|gt:0',
            'country_id' => "$mandatory|exists:countries,id",
            'type_id' => "$mandatory|exists:types,id",
        ];
    }
}
