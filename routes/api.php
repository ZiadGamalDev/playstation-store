<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetEmailController;
use App\Http\Controllers\Auth\VerificationEmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Artisan;

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

    Route::get('countries', [CountryController::class, 'index']);
    Route::get('countries/{country}', [CountryController::class, 'show']);
    
    Route::get('cards', [CardController::class, 'index']);
    Route::get('cards/{card}', [CardController::class, 'show']);
    
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
    });

    Route::group(['middleware' => ['auth.role:user']], function () {        
        Route::post('carts', [CartController::class, 'store']);
        Route::put('carts/{cart}', [CartController::class, 'update']);
        Route::delete('carts/{cart}', [CartController::class, 'destroy']);

        Route::post('orders', [OrderController::class, 'store']);
        Route::put('orders/{order}', [OrderController::class, 'update']);
        Route::delete('orders/{order}', [OrderController::class, 'destroy']);
        Route::post('orders/{order}/checkout', [OrderController::class, 'checkout']);
    });
});

# Test
Route::get('test', function () {
    return response()->json(['message' => 'test']);
});
