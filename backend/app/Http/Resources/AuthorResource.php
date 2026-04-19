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
            'name' => $this->name,
            'biography_en' => $this->biography_en,
            'biography_hu' => $this->biography_hu,
            'books' => BookResource::collection($this->whenLoaded('books')),
        ];
    }
}
