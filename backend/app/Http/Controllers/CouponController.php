<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResource
    {
        $userId = auth('sanctum')->id();

        $coupons = Coupon::with(['book', 'genre', 'user'])
            ->whereNull('code')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->where(function ($query) use ($userId) {
                $query->whereNull('user_id');
                if ($userId) {
                    $query->orWhere('user_id', $userId);
                }
            })
            ->get();

        return CouponResource::collection($coupons);
    }

    public function validate(Request $request): JsonResource|JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string']
        ]);

        $coupon = Coupon::with(['book', 'genre', 'user'])
            ->where('code', $request->code)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->first();

        if (!$coupon) {
            return response()->json(['message_en' => 'Invalid or expired coupon code.', 'message_hu' => 'Érvénytelen vagy lejárt kuponkód.'], 404);
        }

        if ($coupon->user_id && $coupon->user_id !== Auth::id()) {
            return response()->json(['message_en' => 'This coupon is not valid for your account.', 'message_hu' => 'Ezt a kupont nem használhatja fel.'], 403);
        }

        return new CouponResource($coupon);
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
