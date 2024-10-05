<?php

namespace App\Http\Controllers;

use App\Http\Services\StripeService;
use App\Models\Payment;
use App\Mail\CardCodeEmail;
use App\Models\CardCode;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
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

    public function checkout(Order $order)
    {
        if ($order->paid) {
            return $this->errorResponse('Payment already done', 409);
        } 
        if (CardCode::whereNull('used_at')->count() < $order->items->sum('quantity')) {
            return $this->errorResponse('Not enough card codes available', 409);
        }

        try {
            $session = $this->stripeService->createCheckoutSession($order->total * 100, 'egp', $order->id);
            return $this->respondWithData('Redirect to Stripe', ['url' => $session->url], 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create Stripe Checkout session: ' . $e->getMessage(), 500);
        }
    }

    public function success(Request $request, Order $order)
    {
        $sessionId = $request->query('session_id');

        DB::beginTransaction();
        try {
            $session = $this->stripeService->retrieveCheckoutSession($sessionId);
            if (!$session || $session->payment_status !== 'paid') {
                throw new \Exception('Invalid or incomplete payment session');
            }

            $this->processOrderPayment($order, $sessionId);

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

    private function processOrderPayment(Order $order, string $sessionId)
    {
        $order->update(['paid' => true]);

        Payment::create([
            'amount' => $order->total,
            'method' => 'stripe',
            'status' => 'success',
            'session_id' => $sessionId,
            'user_id' => $order->user_id,
            'order_id' => $order->id,
        ]);

        $cardCodes = CardCode::whereNull('used_at')->limit($order->items->sum('quantity'))->get();
        $now = now();

        foreach ($order->items as $item) {
            foreach ($cardCodes->splice(0, $item->quantity) as $code) {
                $code->update([
                    'card_id' => $item->card_id,
                    'order_id' => $order->id,
                    'order_item_id' => $item->id,
                    'used_at' => $now,
                ]);
            }
        }

        $order->carts()->delete();
    }
}
