<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric',
            'method' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'session_id' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'order_id' => 'required|exists:orders,id',
        ];
    }
}
