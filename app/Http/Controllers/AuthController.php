<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ){}

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6'
        ]);

        $payload = $this->authService->register($request->all());

        if ($payload->status === 201) {
            return response()->json([
                'status' => true,
                'message' => $payload->message,
                'token' => $payload->token,
                'user' => $payload->user,
            ], $payload->status);
        }

        return response()->json([
            'status' => false,
            'message' => $payload->message,
        ], $payload->status);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|exists:users,email',
            'password' => 'required|string|min:6'
        ]);

        $payload = $this->authService->login($request->all());

        if ($payload->status === 200) {
            return response()->json([
                'status' => true,
                'message' => $payload->message,
                'token' => $payload->token,
                'user' => $payload->user,
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => $payload->message,
        ], $payload->status);
    }
}
