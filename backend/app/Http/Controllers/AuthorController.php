<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResource
    {
        $authors = Author::with(['books.publisher', 'books.genre'])->get();
        return AuthorResource::collection($authors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuthorRequest $request): JsonResource
    {
        $author = Author::create($request->validated())->load(['books.publisher', 'books.genre']);
        return new AuthorResource($author);
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author, Request $request): JsonResource
    {
        return new AuthorResource(
            $author->load([
                'books' => function ($query) use ($request) {
                    $query->limit($request->integer('limit', 3));
                },
                'books.publisher',
                'books.genre',
                'books.authors',
            ])
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthorRequest $request, Author $author): JsonResource
    {
        $author->update($request->validated());
        return new AuthorResource($author->load(['books.publisher', 'books.genre']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author): Response
    {
        return $author->delete() ? response()->noContent() : abort(500);
    }
}
