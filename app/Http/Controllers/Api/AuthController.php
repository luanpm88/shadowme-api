<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, AuthService $authService)
    {
        $result = $authService->register($request->validated());

        return response()->json([
            'user' => new UserResource($result['user']),
            'access_token' => $result['access_token'],
            'refresh_token' => $result['refresh_token'],
        ], 201);
    }

    public function login(LoginRequest $request, AuthService $authService)
    {
        $result = $authService->login(
            $request->validated('email'),
            $request->validated('password')
        );

        return response()->json([
            'user' => new UserResource($result['user']),
            'access_token' => $result['access_token'],
            'refresh_token' => $result['refresh_token'],
        ]);
    }

    public function refresh(Request $request, AuthService $authService)
    {
        $request->validate([
            'refresh_token' => ['required', 'string'],
        ]);

        $result = $authService->refresh($request->string('refresh_token')->toString());

        return response()->json([
            'user' => new UserResource($result['user']),
            'access_token' => $result['access_token'],
            'refresh_token' => $result['refresh_token'],
        ]);
    }

    public function logout(Request $request, AuthService $authService)
    {
        $authService->logout($request->user(), $request->user()?->currentAccessToken());

        return response()->json(['message' => 'Logged out']);
    }
}
