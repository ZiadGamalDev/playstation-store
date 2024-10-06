<?php

namespace App\Http\Controllers;

use App\Http\Requests\CardFavoriteRequest;
use App\Models\CardFavorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardFavoriteController extends Controller
{
    private $user;

    public function __construct()
    {
        /** @var User */
        $this->user = Auth::user();
    }

    public function index()
    {
        $favorites = $this->user->favoriteCards()->with('card')->get();

        return $this->respondWithData('Favorite cards retrieved successfully', $favorites, 200);
    }

    public function store(CardFavoriteRequest $request)
    {
        $data = $request->validated();

        $favorite = CardFavorite::firstOrCreate([
            'user_id' => $this->user->id,
            'card_id' => $data['card_id'],
        ]);
        $favorite->load('card');

        return $this->respondWithData('Card added to favorites successfully', $favorite, 201);
    }

    public function destroy(CardFavorite $favorite)
    {
        $favorite->delete();

        return $this->successResponse('Card removed from favorites successfully', 200);
    }

    public function toggle(CardFavoriteRequest $request)
    {
        $data = $request->validated();

        $favorite = $this->user->favoriteCards()->where('card_id', $data['card_id'])->first();
        
        return $favorite ? $this->destroy($favorite) : $this->store($request);
    }
}
