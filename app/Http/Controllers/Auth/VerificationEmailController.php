<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmailRequest;
use App\Http\Requests\Auth\VerificationRequest;
use App\Models\Otp;
use App\Models\User;
use App\Http\Traits\ApiResponseTrait;
use App\Mail\Auth\VerificationEmail;
use Illuminate\Support\Facades\Mail;

class VerificationEmailController extends Controller
{
    use ApiResponseTrait;

    public function create(EmailRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return $this->successResponse('Email already verified');
        }

        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_sent_at = now();
        $user->save();

        Mail::to($user->email)->send(new VerificationEmail($user->name, $otp));

        return $this->successResponse('OTP sent successfully');
    }

    public function store(VerificationRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return $this->successResponse('Email already verified');
        } elseif ($user->otp != $request->otp) {
            return $this->errorResponse('Invalid OTP code', 401);
        } elseif ($user->otp_sent_at->addMinutes(10)->isPast()) {
            return $this->errorResponse('OTP code expired', 401);
        }

        $user->otp = null;
        $user->otp_sent_at = null;
        $user->save();
        $user->markEmailAsVerified();

        return $this->successResponse('Email verified successfully');
    }
}
