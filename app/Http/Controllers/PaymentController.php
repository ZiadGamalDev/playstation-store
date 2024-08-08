<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        if (auth()->user()->is_admin) {
            $payments = Payment::with('order', 'user')->get();
        } else {
            $payments = Payment::where('user_id', auth()->id())->with('order')->get();
        }

        return $this->respondWithData('Payments retrieved successfully', $payments, 200);
    }

    public function show(Payment $payment)
    {
        if (auth()->user()->is_admin) {
            $payment->load('order', 'user');
        } elseif ($payment->user_id != auth()->id()) {
            abort(404);
        } else {
            $payment->load('order');
        }

        return $this->respondWithData('Payment retrieved successfully', $payment, 200);
    }
}
