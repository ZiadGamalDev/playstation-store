<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Models\Cart;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        if (auth()->user()->is_admin) {
            $carts = Cart::with('user')->get();
        } else {
            $carts = Cart::where('user_id', auth()->id())->get();
        }

        return $this->respondWithData('Carts retrieved successfully', $carts, 200);
    }

    public function store(CartRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $cart = Cart::where('user_id', $data['user_id'])->where('card_id', $data['card_id'])->first();
        if ($cart) {
            $cart->quantity += $data['quantity'] ?? 1;
            $cart->save();
        } else {
            $cart = Cart::create($data);
        }

        return $this->respondWithData('Cart created successfully', $cart, 201);
    }

    public function show(Cart $cart)
    {
        if (auth()->user()->is_admin) {
            $cart->load('user');
        } elseif ($cart->user_id != auth()->id()) {
            abort(404);
        }
        
        return $this->respondWithData('Cart retrieved successfully', $cart, 200);
    }

    public function update(CartRequest $request, Cart $cart)
    {
        $data = $request->validated();
        $cart->update($data);

        return $this->respondWithData('Cart updated successfully', $cart, 200);
    }

    public function destroy(Cart $cart)
    {
        $cart->delete();
        return $this->successResponse('Cart deleted successfully', 200);
    }
}
