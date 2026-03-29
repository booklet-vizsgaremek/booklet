<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResource
    {
        return UserResource::collection(User::with(['receipts', 'coupons', 'wishlists'])->orderBy('created_at', 'desc')->paginate($request->integer('per_page', 10)));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());
        return response()->json([
            'message_hu' => "A(z) $user->email sikeresen regisztrálva.",
            'message_en' => "User with email $user->email has signed up successfully.",
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): JsonResource
    {
        return new UserResource($user->load(['receipts', 'coupons', 'wishlists']));
    }

    public function self(): JsonResource
    {
        /** @var User $user */
        $user = Auth::user();
        return new UserResource($user->load(['receipts', 'coupons', 'wishlists']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResource
    {
        $user->update($request->validated());
        return new UserResource($user->load(['receipts', 'coupons', 'wishlists']));
    }

    public function updatePassword(UpdateUserPasswordRequest $request, User $user): JsonResponse
    {
        $user->update(['password' => $request->password]);
        $user->tokens()->delete();
        return response()->json([
            'message_hu' => 'Jelszó sikeresen frissítve.',
            'message_en' => 'Password successfully updated.',
        ]);
    }

    public function setRole(Request $request, User $user): JsonResource|JsonResponse
    {
        if (Auth::id() === $user->id) {
            return response()->json([
                'message_hu' => 'Saját szerepkörét nem változtathatja meg.',
                'message_en' => 'You cannot modify your own role.',
            ], 403);
        }

        $user->update($request->validate([
            'role' => ['required', 'in:customer,staff,manager,admin'],
        ]));

        return new UserResource($user->load(['receipts', 'coupons', 'wishlists']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): Response
    {
        return $user->delete() ? response()->noContent() : abort(500);
    }
}
