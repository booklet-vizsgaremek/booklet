<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResource
    {
        $this->authorize('admin', $request->user());

        $query = User::with(['receipts', 'coupons', 'wishlists']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                    ->orWhere('last_name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('role')) $query->where('role', $request->role);

        if ($request->filled('order_name')) {
            $direction = $request->order_name === 'desc' ? 'desc' : 'asc';
            if ($request->header('X-Locale') === 'hu') $query->orderBy('last_name', $direction)->orderBy('first_name', $direction);
            else $query->orderBy('first_name', $direction)->orderBy('last_name', $direction);
        } elseif ($request->filled('order_email')) {
            $query->orderBy('email', $request->order_email);
        } elseif ($request->filled('order_role')) {
            $query->orderBy('role', $request->order_role);
        } elseif ($request->filled('order_receipts')) {
            $query->withCount('receipts')
                ->orderBy('receipts_count', $request->order_receipts);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return UserResource::collection(
            $query->paginate($request->integer('per_page', 10))
        );
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
    public function show(User $user, Request $request): JsonResource
    {
        $this->authorize('admin', $request->user());
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
        $authUser = Auth::user();

        if ($authUser->id !== $user->id) {
            return response()->json([
                'message_hu' => 'Csak a saját jelszavát változtathatja meg.',
                'message_en' => 'You can only change your own password.',
            ], 403);
        }

        $user->update(['password' => $request->password]);
        $user->tokens()->delete();
        return response()->json([
            'message_hu' => 'Jelszó sikeresen frissítve.',
            'message_en' => 'Password successfully updated.',
        ]);
    }

    public function updateRole(UpdateUserRoleRequest $request, User $user): JsonResource|JsonResponse
    {
        if (Auth::id() === $user->id) {
            return response()->json([
                'message_hu' => 'Saját szerepkörét nem változtathatja meg.',
                'message_en' => 'You cannot modify your own role.',
            ], 403);
        }

        $user->update($request->validated());

        return new UserResource($user->load(['receipts', 'coupons', 'wishlists']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): Response|JsonResponse
    {
        /** @var User $authUser */
        $authUser = Auth::user();

        if (($authUser->id !== $user->id && $authUser->role !== 'admin') || ($authUser->id === $user->id && $authUser->role === 'admin')) {
            return response()->json([
                'message_hu' => 'Nincs jogosultsága törölni ezt a felhasználót.',
                'message_en' => 'You are not authorized to delete this user.',
            ], 403);
        }

        return $user->delete() ? response()->noContent() : abort(500);
    }
}
