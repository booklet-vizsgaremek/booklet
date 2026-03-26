<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceiptResource extends JsonResource
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
            'date' => $this->date,
            'user' => new UserResource($this->whenLoaded('user')),
            'books' => $this->whenLoaded('books', fn() => $this->books->map(fn($book) => [
                'id' => $book->id,
                'img_path' => $book->img_path,
                'title' => $book->title,
                'price' => $book->price,
                'pages' => $book->pages,
                'stock' => $book->stock,
                'quantity' => $book->pivot->quantity,
                'price_at_purchase' => $book->pivot->price_at_purchase,
                'authors' => AuthorResource::collection($book->authors),
                'publisher' => new PublisherResource($book->publisher),
                'genre' => new GenreResource($book->genre),
            ])),
            'coupons' => CouponResource::collection($this->whenLoaded('coupons')),
            'pickup' => new PickupResource($this->whenLoaded('pickup'))
        ];
    }
}
