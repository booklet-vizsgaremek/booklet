<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use App\Http\Resources\GenreResource;
use App\Models\Genre;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResource
    {
        $genres = Genre::with(['books', 'coupons'])->get();
        return GenreResource::collection($genres);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGenreRequest $request): JsonResource
    {
        $genre = Genre::create($request->validated())->load(['books', 'coupons']);
        return new GenreResource($genre);
    }

    /**
     * Display the specified resource.
     */
    public function show(Genre $genre): JsonResource
    {
        return new GenreResource($genre->load(['books', 'coupons']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGenreRequest $request, Genre $genre): JsonResource
    {
        $genre->update($request->validated());
        return new GenreResource($genre->load(['books', 'coupons']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Genre $genre): Response
    {
        return $genre->delete() ? response()->noContent() : abort(500);
    }
}
