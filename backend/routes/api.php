<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\PickupController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// TODO: Make sure if user can perform destructive/modifying actions

Route::apiResource('authors', AuthorController::class);
Route::apiResource('publishers', PublisherController::class);
Route::apiResource('genres', GenreController::class);
Route::get('books/top-purchased', [BookController::class, 'topPurchased']);
Route::apiResource('books', BookController::class);
Route::apiResource('wishlists', WishlistController::class);
Route::apiResource('receipts', ReceiptController::class);
Route::apiResource('coupons', CouponController::class);
Route::apiResource('pickups', PickupController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users/self', [UserController::class, 'self']);
    Route::apiResource('users', UserController::class)->except('store');
    Route::patch('/users/{user}/password', [UserController::class, 'updatePassword']);
    Route::patch('/users/{user}/role', [UserController::class, 'setRole']);
});

Route::post('/auth/login', [AuthController::class, 'authenticate']);
Route::post('/auth/register', [UserController::class, 'store']);
