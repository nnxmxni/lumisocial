<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    public function __construct(
        private readonly AccountService $accountService
    ){}

    public function prepassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|exists:users,email'
        ]);

        $payload = $this->accountService->prepassword($request->all());

        if ($payload->status === 200) {
            return response()->json([
                'status' => true,
                'message' => $payload->message,
                'data' => $payload->data
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => $payload->message,
        ], $payload->status);
    }

    public function postpassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|min:6|max:6',
            'email' => 'required|exists:users,email',
            'password' => 'required|string|min:6|max:255|confirmed',
            'password_confirmation' => 'required'
        ]);

        $payload = $this->accountService->postpassword($request->all());

        if ($payload->status === 200) {
            return response()->json([
                'status' => true,
                'message' => $payload->message
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => $payload->message
        ], $payload->status);
    }

    public function logout(Request $request): JsonResponse
    {
        $payload = $this->accountService->logout($request->user());

        if ($payload->status === 200) {
            return response()->json([
                'status' => true,
                'message' => $payload->message
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => $payload->message
        ], $payload->status);
    }
}
