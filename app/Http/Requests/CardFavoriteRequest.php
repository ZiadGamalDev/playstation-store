<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CardFavoriteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'card_id' => 'required|exists:cards,id'
        ];
    }
}
