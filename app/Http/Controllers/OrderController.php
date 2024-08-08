<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Requests\StripeRequest;
use App\Http\Services\StripeService;
use App\Models\Order;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Card;
use App\Models\Cart;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected StripeService $stripeService)
    {
    }

    public function index()
    {
        if (auth()->user()->is_admin) {
            $orders = Order::with('user')->get();
        } else {
            $orders = Order::where('user_id', auth()->id())->get();
        }

        return $this->respondWithData('Orders retrieved successfully', $orders, 200);
    }

    public function store(OrderRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => auth()->id(),
            ]);
            $total = 0;
            foreach ($data['cart_ids'] as $cart_id) {
                $cart = Cart::with('card')->find($cart_id);
                $cardPrice = ($cart->card->discount ?? $cart->card->price) * $cart->card->quantity;
                $total += $cardPrice * $cart->quantity;
                OrderItem::create([
                    'order_id' => $order->id,
                    'card_id' => $cart->card->id,
                    'quantity' => $cart->quantity,
                    'price' => $cardPrice,
                ]);
                $cart->delete();
            }

            $order->update(['total' => $total]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse('Order creation failed', 500);
        }

        return $this->respondWithData('Order created successfully', $order, 201);
    }

    public function show(Order $order)
    {
        if (auth()->user()->is_admin) {
            $order->load('user');
        } elseif ($order->user_id != auth()->id()) {
            abort(404);
        }

        return $this->respondWithData('Order retrieved successfully', $order, 200);
    }

    public function update(OrderRequest $request, Order $order)
    {
        $data = $request->validated();

        $order->update($data);

        return $this->respondWithData('Order updated successfully', $order, 200);
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return $this->successResponse('Order deleted successfully', 200);
    }

    public function checkout(StripeRequest $request, Order $order)
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
}
