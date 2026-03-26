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
        $pickup->update($request->validated());
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
