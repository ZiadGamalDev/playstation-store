<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\Cart;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        if (auth()->user()->is_admin) {
            $orders = Order::with('user')->get();
        } else {
            $orders = Order::where('user_id', auth()->id())->get();
        }

        return $this->respondWithData('Orders retrieved successfully', $orders, 200);
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

    public function store(OrderRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $order = Order::create(['user_id' => auth()->id()]);
            $order->total = 0;
            foreach ($data['cart_ids'] as $cart_id) {
                $cart = Cart::with('card')->findOrFail($cart_id);
                $card = $cart->card;
                $cardPrice = $card->discount ?? $card->price;
                $order->total += $cardPrice * $cart->quantity;
                OrderItem::create([
                    'order_id' => $order->id,
                    'card_id' => $card->id,
                    'cart_id' => $cart->id,
                    'quantity' => $cart->quantity,
                    'price' => $cardPrice,
                ]);
            }
            $order->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse('Order creation failed: ' . $e->getMessage(), 500);
        }

        return $this->respondWithData('Order created successfully', $order, 201);
    }
}
