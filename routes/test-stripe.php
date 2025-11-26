<?php

use Illuminate\Support\Facades\Route;

Route::get('/test-stripe', function () {
    $stripeKey = config('cashier.key');
    $stripeSecret = config('cashier.secret');
    
    return response()->json([
        'stripe_key_configured' => !empty($stripeKey),
        'stripe_secret_configured' => !empty($stripeSecret),
        'stripe_key_prefix' => substr($stripeKey, 0, 7),
        'user_has_billable_trait' => method_exists(auth()->user(), 'createPaymentIntent'),
    ]);
})->middleware('auth');
