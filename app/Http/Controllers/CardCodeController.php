<?php

namespace App\Http\Controllers;

use App\Http\Requests\CardCodeRequest;
use App\Models\CardCode;

class CardCodeController extends Controller
{
    public function index()
    {
        $cardCodes = CardCode::all();

        return $this->respondWithData('Card Codes retrieved successfully', $cardCodes, 200);
    }

    public function show(CardCode $cardCode)
    {
        return $this->respondWithData('Card Code retrieved successfully', $cardCode, 200);
    }

    public function store(CardCodeRequest $request)
    {
        $data = $request->validated();

        $cardCode = CardCode::create($data);

        return $this->respondWithData('Card Code created successfully', $cardCode, 201);
    }
}
