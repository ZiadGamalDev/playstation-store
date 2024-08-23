<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'price', 'quantity', 'order_id', 'card_id', 'cart_id'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function cardCodes()
    {
        return $this->hasMany(CardCode::class);
    }
}
