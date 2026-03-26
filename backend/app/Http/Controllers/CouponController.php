<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResource
    {
        $coupons = Coupon::with(['book', 'genre', 'user'])->get();
        return CouponResource::collection($coupons);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCouponRequest $request): JsonResource
    {
        $coupon = Coupon::create($request->validated())->load(['book', 'genre', 'user']);
        return new CouponResource($coupon);
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon): JsonResource
    {
        return new CouponResource($coupon->load(['book', 'genre', 'user']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon): JsonResource
    {
        $coupon->update($request->validated());
        return new CouponResource($coupon->load(['book', 'genre', 'user']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon): Response
    {
        return $coupon->delete() ? response()->noContent() : abort(500);
    }
}
