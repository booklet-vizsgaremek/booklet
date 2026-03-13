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
            "id" => $this->id,
            "img" => $this->img,
            "name" => $this->name,
            "author" => $this->author,
            "price" => $this->price,
            "xp" => $this->xp,
            "status" => $this->status,
            "accepted_by" => $this->accepted_by,
            "genre_id" => $this->genre_id,
            "users" => UserResource::collection($this->whenLoaded("users")),

        ];
    }
}
