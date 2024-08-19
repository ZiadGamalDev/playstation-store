<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $mandatory = $this->method() == 'POST' ? 'required' : 'sometimes';

        return [
            'quantity' => 'nullable|integer|min:1',
            'card_id' => "$mandatory|exists:cards,id",
        ];
    }
}
