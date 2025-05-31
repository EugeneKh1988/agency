<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserUpdateController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\MessagesController;
use App\Http\Middleware\IsAdmin;

// get user data
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// update user data
Route::middleware(['auth:sanctum'])->post('/user', [UserUpdateController::class, 'update']);

// team data
Route::get('/teams', [TeamsController::class, 'index']);
Route::middleware(['auth:sanctum', isAdmin::class])->controller(TeamsController::class)->group(function () {
    Route::get('/teams/{id}', 'show')->where('id', '[0-9]+');
    Route::post('/teams', 'store');
    Route::put('/teams/{id}', 'edit')->where('id', '[0-9]+');
    Route::delete('/teams/{id}', 'delete')->where('id', '[0-9]+');
});

// messages data
Route::middleware(['auth:sanctum', isAdmin::class])->get('/messages', [MessagesController::class, 'index']);
Route::post('/messages', [MessagesController::class, 'store']);
Route::middleware(['auth:sanctum', isAdmin::class])->controller(MessagesController::class)->group(function () {
    Route::put('/messages/{id}', 'edit')->where('id', '[0-9]+');
    Route::delete('/messages/{id}', 'delete')->where('id', '[0-9]+');
});