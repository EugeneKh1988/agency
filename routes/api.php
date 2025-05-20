<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserUpdateController;

// get user data
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// update user data
Route::middleware(['auth:sanctum'])->post('/user', [UserUpdateController::class, 'update']);
