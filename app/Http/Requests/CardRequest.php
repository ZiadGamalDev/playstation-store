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
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'required|image|max:2048|mimes:png,jpg,jpeg',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'quantity' => 'nullable|integer',
            'country_id' => 'required|exists:countries,id',
        ];
    }
}
