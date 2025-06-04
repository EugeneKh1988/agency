<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/captcha', function () {
    return response()->json([
        'captcha' => captcha_img()
    ]);
});

require __DIR__.'/auth.php';
