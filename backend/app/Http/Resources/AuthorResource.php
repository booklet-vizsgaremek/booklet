<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'biography' => $this->biography,
            'books' => $this->whenLoaded('books', fn() => $this->books->map(fn($book) => [
                'id' => $book->id,
                'img_path' => $book->img_path,
                'title' => $book->title,
                'price' => $book->price,
                'pages' => $book->pages,
                'stock' => $book->stock,
                'publisher' => new PublisherResource($book->publisher),
                'genre' => new GenreResource($book->genre),
            ]))
        ];
    }
}
