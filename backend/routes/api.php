<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\PickupController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::apiResource('authors', AuthorController::class);
Route::apiResource('publishers', PublisherController::class);
Route::apiResource('genres', GenreController::class);
Route::apiResource('books', BookController::class);
Route::apiResource('wishlists', WishlistController::class);
Route::apiResource('receipts', ReceiptController::class);
Route::apiResource('coupons', CouponController::class);
Route::apiResource('pickups', PickupController::class);
