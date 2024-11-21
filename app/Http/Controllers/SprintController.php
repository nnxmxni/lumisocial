<?php

namespace App\Http\Controllers;

use App\Models\Sprint;
use Illuminate\Http\Request;
use App\Services\SprintService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\SprintResource;

class SprintController extends Controller
{
    public function __construct(
        private readonly SprintService $sprintService
    ){}

    public function show(Sprint $sprint): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Sprint retrieved successfully',
            'data' => new SprintResource($sprint),
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $payload = $this->sprintService->index($request->user());

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

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable',
            'start_at' => 'required|date',
            'end_at' => 'required|date',
        ]);

        $payload = $this->sprintService->store($request->all(), $request->user());

        if ($payload->status === 201) {
            return response()->json([
                'status' => true,
                'message' => $payload->message,
                'data' => $payload->data,
            ], $payload->status);
        }

        return response()->json([
            'status' => false,
            'message' => $payload->message,
        ], $payload->status);

    }

    public function update(Request $request, Sprint $sprint): JsonResponse
    {
        $request->validate([
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
        ]);

        $payload = $this->sprintService->update($request->all(), $sprint, $request->user());

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

    public function sendInviteToUser(Sprint $sprint, string $username): JsonResponse
    {
        $payload = $this->sprintService->sendInviteToUser($sprint, $username);

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

    public function acceptInvitation(Request $request, Sprint $sprint, string $username ): JsonResponse
    {
        $payload = $this->sprintService->acceptInvitation($sprint, $request->user(), $username);

        if ($payload->status === 200) {
            return response()->json([
                'status' => true,
                'message' => $payload->message,
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => $payload->message,
        ], $payload->status);
    }

    public function removeMemberFromSprint(Request $request, Sprint $sprint, string $username ): JsonResponse
    {
        $payload = $this->sprintService->removeMemberFromSprint($sprint, $request->user(), $username);

        if ($payload->status === 200) {
            return response()->json([
                'status' => true,
                'message' => $payload->message,
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => $payload->message,
        ], $payload->status);
    }

    public function exit(Request $request, Sprint $sprint): JsonResponse
    {
        $payload = $this->sprintService->exit($sprint, $request->user());

        if ($payload->status === 200) {
            return response()->json([
                'status' => true,
                'message' => $payload->message,
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => $payload->message,
        ], $payload->status);
    }

    public function updateSprintCompletedStatus(Sprint $sprint): JsonResponse
    {
        $payload = $this->sprintService->updateSprintCompletedStatus($sprint);

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

    public function delete(Request $request, Sprint $sprint): JsonResponse
    {
        $payload = $this->sprintService->delete($sprint, $request->user());

        if ($payload->status === 200) {
            return response()->json([
                'status' => true,
                'message' => $payload->message,
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => $payload->message,
        ], $payload->status);
    }
}
