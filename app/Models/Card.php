<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'image', 'price', 'discount', 'quantity', 'country_id',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
}
