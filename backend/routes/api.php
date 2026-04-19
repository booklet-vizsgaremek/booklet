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

Route::apiResource('authors', AuthorController::class)->only(['index', 'show']);
Route::apiResource('publishers', PublisherController::class)->only(['index', 'show']);
Route::apiResource('genres', GenreController::class)->only(['index', 'show']);
Route::get('books/top-purchased', [BookController::class, 'topPurchased']);
Route::get('books/random-category', [BookController::class, 'randomCategory']);
Route::get('books/discounted', [BookController::class, 'discounted']);
Route::apiResource('books', BookController::class)->only(['index', 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users/self', [UserController::class, 'self']);
    Route::apiResource('users', UserController::class)->except('store');
    Route::patch('/users/{user}/password', [UserController::class, 'updatePassword']);
    Route::patch('/users/{user}/role', [UserController::class, 'setRole']);
    Route::get('/coupons/validate', [CouponController::class, 'validate']);
    Route::apiResource('books', BookController::class)->except(['index', 'show']);
    Route::apiResource('authors', AuthorController::class)->except(['index', 'show']);
    Route::apiResource('publishers', PublisherController::class)->except(['index', 'show']);
    Route::apiResource('genres', GenreController::class)->except(['index', 'show']);
    Route::apiResource('coupons', CouponController::class)->except(['index', 'show']);
    Route::apiResource('receipts', ReceiptController::class);
    Route::apiResource('pickups', PickupController::class);
    Route::apiResource('wishlists', WishlistController::class);
});

Route::apiResource('coupons', CouponController::class)->only(['index', 'show']);;
Route::post('/auth/login', [AuthController::class, 'authenticate']);
Route::post('/auth/register', [UserController::class, 'store']);
