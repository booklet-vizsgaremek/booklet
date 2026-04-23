<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GenreResource extends JsonResource
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
            'name_hu' => $this->name_hu,
            'name_en' => $this->name_en,
            'books' => BookResource::collection($this->whenLoaded('books'))
        ];
    }
}
