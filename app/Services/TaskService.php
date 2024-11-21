<?php

namespace App\Services;

use stdClass;
use Exception;
use App\Models\Task;
use App\Models\Sprint;
use App\Events\TaskCreatedEvent;
use App\Http\Resources\TaskResource;
use App\Exceptions\SprintCompletedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskService
{
    public function __construct(){}

    public function index(Sprint $sprint): stdClass
    {
        try {
            $data['tasks'] = TaskResource::collection($sprint->tasks);
            $message = 'Tasks retrieved successfully';
            $status = 200;

            return prepareSuccessPayload($message, $status, $data);

        } catch (Exception $exception) {
            return handleException($exception);
        }
    }

    public function store(array $payload, Sprint $sprint): stdClass
    {
        try {
            if ($sprint->is_completed)
                throw new SprintCompletedException('this is sprint has been completed. You cannot create a new task', 400);

            $task = $sprint->tasks()->create([
                'user_id' => auth()->user()->id,
                'title' => $payload['title'],
                'content' => $payload['content'],
            ]);

            TaskCreatedEvent::dispatch($sprint, $task);

            $data['task'] = new TaskResource($task);

            return prepareSuccessPayload('Task created successfully', 201, $data);

        } catch (Exception $exception) {
            return handleException($exception);
        }
    }

    public function update(array $payload, Sprint $sprint, Task $task): stdClass
    {
        try {
            if ($sprint->is_completed)
                throw new SprintCompletedException('this is sprint has been completed. You cannot update the task.', 400);

            $task = $sprint->tasks()->where('id', $task->id)->first();
            if (! $task) throw new ModelNotFoundException('task not found', 404);

            $task->update([
                'title' => $payload['title'] ?? $task->title,
                'content' => $payload['content'] ?? $task->content,
            ]);

            $data['task'] = new TaskResource($task);

            return prepareSuccessPayload('Task updated successfully', 200, $data);

        } catch (Exception $exception) {
            return handleException($exception);
        }
    }

    public function delete(Sprint $sprint, Task $task): stdClass
    {
        try {
            if ($sprint->is_completed)
                throw new SprintCompletedException('this is sprint has been completed. You cannot create a new task', 400);

            $task = $sprint->tasks()->find($task->id);
            if (! $task) throw new ModelNotFoundException('task not found', 404);

            $task->delete();

            return prepareSuccessPayload('Task deleted successfully', 200);

        } catch (Exception $exception) {
            return handleException($exception);
        }
    }
}
