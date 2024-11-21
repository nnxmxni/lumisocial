<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Sprint;
use Illuminate\Http\Request;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService
    ){}

    public function show(Sprint $sprint, Task $task): JsonResponse
    {
        $data['task'] = new TaskResource($task);
        return response()->json([
            'status' => true,
            'message' => 'Task retrieved successfully',
            'data' => $data
        ]);
    }

    public function index(Sprint $sprint): JsonResponse
    {
        $payload = $this->taskService->index($sprint);

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

    public function store(Request $request, Sprint $sprint): JsonResponse
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'nullable|string',
        ]);

        $payload = $this->taskService->store($request->all(), $sprint);

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

    public function update(Request $request, Sprint $sprint, Task $task): JsonResponse
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'nullable|string'
        ]);

        $payload = $this->taskService->update($request->all(), $sprint, $task);

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

    public function delete(Sprint $sprint, Task $task): JsonResponse
    {
        $payload = $this->taskService->delete($sprint, $task);

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
