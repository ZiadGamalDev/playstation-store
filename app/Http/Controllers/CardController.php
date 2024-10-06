<?php

namespace App\Http\Controllers;

use App\Http\Requests\CardRequest;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CardController extends Controller
{
    public function index(Request $request)
    {
        if ($type = $request->type) {
            $cards = Card::where('type', $type)->get();
        } else {
            $cards = Card::all();
        }

        return $this->respondWithData('Cards retrieved successfully', $cards, 200);
    }

    public function store(CardRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images/cards', 'public');
        }
        $data['user_id'] = $request->user()->id;

        $card = Card::create($data);

        return $this->respondWithData('Card created successfully', $card, 201);
    }

    public function show(Card $card)
    {
        return $this->respondWithData('Card retrieved successfully', $card, 200);
    }

    public function update(CardRequest $request, Card $card)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            if ($card->image) {
                Storage::disk('public')->delete($card->image);
            }
            $data['image'] = $request->file('image')->store('images/cards', 'public');
        }

        $card->update($data);

        return $this->respondWithData('Card updated successfully', $card, 200);
    }

    public function destroy(Card $card)
    {
        if ($card->image) {
            Storage::disk('public')->delete($card->image);
        }
        $card->delete();

        return $this->successResponse('Card deleted successfully', 200);
    }
}
