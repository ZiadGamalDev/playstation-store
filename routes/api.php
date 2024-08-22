<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetEmailController;
use App\Http\Controllers\Auth\VerificationEmailController;
use App\Http\Controllers\CardCodeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;
use App\Mail\Auth\VerificationEmail;
use App\Mail\CardCodeEmail;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('artisan', function (Request $request) {
    Artisan::call($request->command);
    return Artisan::output();
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::post('email/verification/create', [VerificationEmailController::class, 'create']);
Route::post('email/verification/store', [VerificationEmailController::class, 'store']);

Route::post('email/password-reset/create', [PasswordResetEmailController::class, 'create']);
Route::post('email/password-reset/store', [PasswordResetEmailController::class, 'store']);

Route::group(['middleware' => ['auth']], function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::post('password/confirm', [PasswordController::class, 'confirm']);
    Route::post('password/update', [PasswordController::class, 'update']);

    Route::get('carts', [CartController::class, 'index']);
    Route::get('carts/{cart}', [CartController::class, 'show']);

    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{order}', [OrderController::class, 'show']);

    Route::get('payments', [PaymentController::class, 'index']);
    Route::get('payments/{payment}', [PaymentController::class, 'show']);
    
    Route::group(['middleware' => ['auth.role:admin']], function () {
        Route::post('countries', [CountryController::class, 'store']);
        Route::put('countries/{country}', [CountryController::class, 'update']);
        Route::delete('countries/{country}', [CountryController::class, 'destroy']);

        Route::post('cards', [CardController::class, 'store']);
        Route::put('cards/{card}', [CardController::class, 'update']);
        Route::delete('cards/{card}', [CardController::class, 'destroy']);

        Route::get('card-codes', [CardCodeController::class, 'index']);
        Route::get('card-codes/{cardCode}', [CardCodeController::class, 'show']);
        Route::post('card-codes', [CardCodeController::class, 'store']);
    });

    Route::group(['middleware' => ['auth.role:user']], function () {        
        Route::post('carts', [CartController::class, 'store']);
        Route::put('carts/{cart}', [CartController::class, 'update']);
        Route::delete('carts/{cart}', [CartController::class, 'destroy']);

        Route::post('orders', [OrderController::class, 'store']);
        Route::put('orders/{order}', [OrderController::class, 'update']);
        Route::delete('orders/{order}', [OrderController::class, 'destroy']);
        
        Route::get('payments/checkout/{order}', [PaymentController::class, 'checkout'])->middleware('verified');
    });
});

Route::get('payments/success/{order}', [PaymentController::class, 'success'])->name('payment.success');
Route::get('payments/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');

Route::get('countries', [CountryController::class, 'index']);
Route::get('countries/{country}', [CountryController::class, 'show']);

Route::get('cards', [CardController::class, 'index']);
Route::get('cards/{card}', [CardController::class, 'show']);

# Test
Route::get('test', function () {
    return response()->json(['message' => 'testing...']);
});

Route::get('test/email/{email}', function ($email) {
    Mail::raw('This is a test email from Laravel using Hostinger SMTP.', function ($message) use ($email) {
        $message->to($email)->subject('Test Email');
    });

    return 'Email sent successfully!';
});

Route::get('test/card-code', function () {
    $user = User::where('email', 'zyadgamal450@gmail.com')->first();
    $cardCode = Str::random(20);
    Mail::to($user->email)->send(new CardCodeEmail($user->name, $cardCode));
});