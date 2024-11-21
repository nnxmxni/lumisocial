<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    public function __construct(
        private readonly SearchService $searchService
    ){}

    public function searchByUsername(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required'
        ]);

        $payload = $this->searchService->searchByUsername($request->all());

        if ($payload->status === 200) {
            return response()->json([
                'status' => true,
                'message' => $payload->message,
                'data' => $payload->data,
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => $payload->message,
        ], $payload->status);
    }
}
