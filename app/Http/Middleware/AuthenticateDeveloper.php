<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateDeveloper
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = $request->header('X-Developer-Secret');
        if (!Hash::check($secret, '$2y$12$Nyw3Gze9IR5nw5l/mkV8VeA2K4mxmFSwwKFMowPOOx2UJwifjck1K')) {
            return response()->json(['message' => 'You are not authorized'], 401);
        }
        return $next($request);
    }
}
