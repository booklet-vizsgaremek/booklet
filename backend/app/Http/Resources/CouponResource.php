<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
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
            'book_id' => $this->book_id,
            'genre_id' => $this->genre_id,
            'user_id' => $this->user_id,
            'discount' => $this->discount,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'code' => $this->code,
            'book' => new BookResource($this->whenLoaded('book')),
            'genre' => new GenreResource($this->whenLoaded('genre')),
            'user' => new UserResource($this->whenLoaded('user'))
        ];
    }
}
