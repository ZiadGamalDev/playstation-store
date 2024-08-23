<?php

namespace App\Http\Middleware;

use App\Http\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthRole
{
    use ApiResponseTrait;

    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (($role == 'admin' && auth()->user()->is_admin)
            || ($role == 'user' && !auth()->user()->is_admin)
        ) {
            return $next($request);
        }

        return $this->errorResponse('You are not authorized', 401);
    }
}
