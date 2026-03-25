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
            'name' => $this->name,
            'price' => $this->price,
            'pages' => $this->pages,
            'stock' => $this->stock,
            'author' => new AuthorResource($this->whenLoaded('author')),
            'publisher' => new PublisherResource($this->whenLoaded('publisher')),
            'genre' => new GenreResource($this->whenLoaded('genre'))
        ];
    }
}
