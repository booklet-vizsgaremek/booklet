<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResource
    {
        $books = Book::with(['authors', 'publisher', 'genre'])->paginate($request->integer('per_page', 10));
        return BookResource::collection($books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request): JsonResource
    {
        $validated = $request->validated();
        $book = Book::create(collect($validated)->except('author_ids')->toArray());
        $book->authors()->sync($validated['author_ids']);
        return new BookResource($book->load(['authors', 'publisher', 'genre']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book): JsonResource
    {
        return new BookResource($book->load(['authors', 'publisher', 'genre']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book): JsonResource
    {
        $validated = $request->validated();
        $book->update(collect($validated)->except('author_ids')->toArray());
        if (isset($validated['author_ids'])) {
            $book->authors()->sync($validated['author_ids']);
        }
        return new BookResource($book->load(['authors', 'publisher', 'genre']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book): Response
    {
        return $book->delete() ? response()->noContent() : abort(500);
    }

    public function topPurchased(): JsonResource
    {
        $books = Book::with(['authors', 'publisher', 'genre'])
            ->withCount(['receipts as total_purchased' => function ($query) {
                $query->select(DB::raw('sum(books_receipts.quantity)'));
            }])
            ->orderByDesc('total_purchased')
            ->limit(10)
            ->get();

        return BookResource::collection($books);
    }
}
