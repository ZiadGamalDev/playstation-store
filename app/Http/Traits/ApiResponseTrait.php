<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Auth;

trait ApiResponseTrait
{
    protected function respondWithToken($message, $user, $token)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'data' => $user,
        ]);
    }

    protected function respondWithData($message, $data, $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
    
    protected function successResponse($message, $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
        ], $statusCode);
    }

    protected function errorResponse($message, $statusCode = 404)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }
}
