<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmailRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Http\Traits\ApiResponseTrait;

class AuthController extends Controller
{
    use ApiResponseTrait;
    
    public function __construct(protected VerificationEmailController $verificationEmailController) {}

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create($data);

        $token = auth()->login($user);

        return $this->respondWithToken('Register successfully', $user, $token);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        if (!$token = auth()->attempt($data)) {
            return $this->errorResponse('Email or password is incorrect', 401);
        }

        return $this->respondWithToken('Login successfully', auth()->user(), $token);
    }

    public function logout()
    {
        auth()->logout();

        return $this->successResponse('Logout successfully');
    }

    public function me()
    {
        return $this->respondWithData('Retrieved successfully', auth()->user());
    }
}
