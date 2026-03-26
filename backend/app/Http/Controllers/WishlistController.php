<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWishlistRequest;
use App\Http\Requests\UpdateWishlistRequest;
use App\Http\Resources\WishlistResource;
use App\Models\Wishlist;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResource
    {
        $wishlists = Wishlist::with(['user', 'book'])->get();
        return WishlistResource::collection($wishlists);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWishlistRequest $request): JsonResource
    {
        $wishlist = Wishlist::create($request->validated())->load(['user', 'book']);
        return new WishlistResource($wishlist);
    }

    /**
     * Display the specified resource.
     */
    public function show(Wishlist $wishlist): JsonResource
    {
        return new WishlistResource($wishlist->load(['user', 'book']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWishlistRequest $request, Wishlist $wishlist): JsonResource
    {
        $wishlist->update($request->validated());
        return new WishlistResource($wishlist->load(['user', 'book']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wishlist $wishlist): Response
    {
        return $wishlist->delete() ? response()->noContent() : abort(500);
    }
}
