<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function authenticate(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->validated())) {
            return response()->json([
                'message_hu' => 'Sikertelen bejelentkezés.',
                'message_en' => 'Login failed.',
            ], 401);
        }

        return response()->json([
            'token' => $request->user()->createToken('app')->plainTextToken,
        ]);
    }
}
