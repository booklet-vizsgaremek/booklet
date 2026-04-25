<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePickupRequest;
use App\Http\Requests\UpdatePickupRequest;
use App\Http\Resources\PickupResource;
use App\Models\Pickup;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class PickupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResource
    {
        $pickups = Pickup::with('receipt')->get();
        return PickupResource::collection($pickups);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePickupRequest $request): JsonResource
    {
        $pickup = Pickup::create($request->validated())->load('receipt');
        return new PickupResource($pickup);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pickup $pickup): JsonResource
    {
        return new PickupResource($pickup->load('receipt'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePickupRequest $request, Pickup $pickup): JsonResource
    {
        $oldStatus = $pickup->status;
        $newStatus = $request->input('status', $oldStatus);

        $pickup->update($request->validated());

        if ($oldStatus !== $newStatus) {
            $receipt = $pickup->receipt()->with('books')->first();

            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                foreach ($receipt->books as $book) {
                    $book->increment('stock', $book->pivot->quantity);
                }
            } elseif ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
                foreach ($receipt->books as $book) {
                    $book->decrement('stock', $book->pivot->quantity);
                }
            }
        }

        return new PickupResource($pickup->load('receipt'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pickup $pickup): Response
    {
        return $pickup->delete() ? response()->noContent() : abort(500);
    }
}
