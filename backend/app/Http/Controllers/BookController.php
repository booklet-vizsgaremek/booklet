<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResource
    {
        $books = Book::with(['authors', 'publisher', 'genre'])->get();
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
}
