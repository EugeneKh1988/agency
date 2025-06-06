<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriberController;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/captcha', function () {
    return response()->json([
        'captcha' => captcha_img()
    ]);
});

Route::post('/subscriber', [SubscriberController::class, 'index']);
Route::get('/subscriber/verify/{token}/{email}', [SubscriberController::class, 'verify']);

require __DIR__.'/auth.php';
