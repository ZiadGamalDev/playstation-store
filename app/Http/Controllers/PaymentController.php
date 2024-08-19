<?php

namespace App\Http\Controllers;

use App\Http\Requests\StripeRequest;
use App\Http\Services\StripeService;
use App\Models\Payment;
use App\Http\Traits\ApiResponseTrait;
use App\Mail\CardCodeEmail;
use App\Models\CardCode;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected StripeService $stripeService) {}


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

    public function checkout0(StripeRequest $request, Order $order)
    {
        $data = $request->validated();

        if ($order->paid) {
            return $this->errorResponse('Payment already done', 409);
        }

        try {
            $token = $this->stripeService->createToken($data);

            $amount = $order->total * 100;

            $charge = $this->stripeService->createCharge($amount, 'egp', $token);

            $order->update(['status' => true]);

            Payment::create([
                'amount' => $order->total,
                'method' => 'stripe',
                'status' => $charge->status,
                'session_id' => $charge->id,
                'user_id' => $order->user_id,
                'order_id' => $order->id
            ]);

            return $this->successResponse('Payment successful');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function checkout(Order $order)
    {
        if ($order->paid) {
            return $this->errorResponse('Payment already done', 409);
        } elseif (CardCode::whereNull('used_at')->count() < $order->items->count()) {
            return $this->errorResponse('Not enough card codes available', 409);
        }

        try {
            $amount = $order->total * 100;

            $session = $this->stripeService->createCheckoutSession($amount, 'egp', $order->id);
            
            return $this->respondWithData('Redirect to Stripe', ['url' => $session->url], 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create Stripe Checkout session: ' . $e->getMessage(), 500);
        }
    }


    public function success(Request $request, Order $order)
    {
        if ($order->paid) {
            return view('payments.error', ['message' => 'Payment already processed']);
        }

        $sessionId = $request->query('session_id');

        DB::beginTransaction();

        try {
            $session = $this->stripeService->retrieveCheckoutSession($sessionId);

            if (!$session || $session->payment_status !== 'paid') {
                throw new \Exception('Invalid or incomplete payment session');
            }

            // Mark order as paid and create a payment record
            $order->update(['paid' => true]);

            Payment::create([
                'amount' => $order->total,
                'method' => 'stripe',
                'status' => 'success',
                'session_id' => $sessionId,
                'user_id' => $order->user_id,
                'order_id' => $order->id,
            ]);

            // Assign card codes and clean up cart items
            foreach ($order->items as $item) {
                $cardCode = CardCode::whereNull('used_at')->firstOrFail();
                $cardCode->update(['used_at' => now()]);
                $item->update(['code' => $cardCode->code]);
                $item->cart()->delete();
            }

            // Send email with card codes
            Mail::to($order->user->email)->send(new CardCodeEmail($order));

            DB::commit();

            return view('payments.success', ['order' => $order]);
        } catch (\Exception $e) {
            DB::rollback();
            return view('payments.error', ['message' => 'Payment processing failed: ' . $e->getMessage()]);
        }
    }

    public function cancel()
    {
        return view('payments.error', ['message' => 'Payment canceled']);
    }
}
