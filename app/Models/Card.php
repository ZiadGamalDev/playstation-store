<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'image', 'price', 'discount', 'country_id', 'category_id',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function favorites()
    {
        return $this->hasMany(CardFavorite::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
