<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResource
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $books = $user->wishlists()->with(['authors', 'publisher', 'genre'])->get();
        return BookResource::collection($books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Response | JsonResource
    {
        $request->validate(['book_id' => ['required', 'uuid', 'exists:books,id']]);
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->wishlists()->where('book_id', $request->book_id)->exists()) {
            return response()->noContent();
        }

        $user->wishlists()->attach($request->book_id);
        $book = $user->wishlists()->with(['authors', 'publisher', 'genre'])
            ->where('book_id', $request->book_id)->first();

        return new BookResource($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $wishlist): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->wishlists()->detach($wishlist);
        return response()->noContent();
    }
}
