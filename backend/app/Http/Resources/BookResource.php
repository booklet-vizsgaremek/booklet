<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
            'img_path' => $this->img_path,
            'title' => $this->title,
            'price' => $this->price,
            'pages' => $this->pages,
            'stock' => $this->stock,
            'authors' => AuthorResource::collection($this->whenLoaded('authors')),
            'publisher' => new PublisherResource($this->whenLoaded('publisher')),
            'genre' => new GenreResource($this->whenLoaded('genre'))
        ];
    }
}
