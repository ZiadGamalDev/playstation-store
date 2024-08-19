<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CardCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|min:3|max:255|unique:card_codes',
        ];
    }
}
