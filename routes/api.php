<?php

use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public routes
//Auth
Route::post('/login', [AuthController::class, 'login']);
//Blog
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{slug}', [PostController::class, 'show']);

// Protected routes using Sanctum
Route::middleware('auth:sanctum')->group(function () {
    //Auth
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    //Blog
    Route::post('/posts', [PostController::class, 'store']);
    Route::match(['put', 'patch'], '/posts/{slug}', [PostController::class, 'update']);
    Route::delete('/posts/{slug}', [PostController::class, 'destroy']);
});
