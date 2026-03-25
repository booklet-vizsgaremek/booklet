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
            'books' => BookResource::collection($this->whenLoaded('books')),
            'coupons' => CouponResource::collection($this->whenLoaded('coupons')),
            'pickup' => new PickupResource($this->whenLoaded('pickup'))
        ];
    }
}
