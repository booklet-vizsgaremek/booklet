<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublisherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'books' => $this->whenLoaded('books', fn() => $this->books->map(fn($book) => [
                'id' => $book->id,
                'img_path' => $book->img_path,
                'title' => $book->title,
                'price' => $book->price,
                'pages' => $book->pages,
                'stock' => $book->stock,
                'authors' => AuthorResource::collection($book->authors),
                'genre' => new GenreResource($book->genre),
            ]))
        ];
    }
}
