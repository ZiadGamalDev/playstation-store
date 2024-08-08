<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmailRequest;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Models\User;
use App\Http\Traits\ApiResponseTrait;
use App\Mail\Auth\PasswordResetEmail;
use Illuminate\Support\Facades\Mail;

class PasswordResetEmailController extends Controller
{
    use ApiResponseTrait;

    public function create(EmailRequest $request)
    {
        $otp = rand(100000, 999999);
        $user = User::where('email', $request->email)->first();
        $user->otp = $otp;
        $user->otp_sent_at = now();
        $user->save();

        Mail::to($user->email)->send(new PasswordResetEmail($user->name, $otp));

        return $this->successResponse('OTP sent successfully');
    }

    public function store(PasswordResetRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user->otp != $request->otp) {
            $this->errorResponse('Invalid OTP code', 422);
        } elseif ($user->otp_sent_at->addMinutes(10)->isPast()) {
            $this->errorResponse('OTP code expired', 422);
        }

        $user->otp = null;
        $user->otp_sent_at = null;
        $user->password = $request->password;
        $user->save();

        return $this->successResponse('Password reset successfully');
    }
}
