<?php

namespace App\Services;

use App\Exceptions\UnauthorizedToDeleteSprintException;
use App\Exceptions\UnauthorizedToUpdateSprintException;
use Illuminate\Support\Str;
use stdClass;
use Throwable;
use Exception;
use App\Models\User;
use App\Models\Sprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Http\Resources\SprintResource;
use App\Exceptions\AlreadyAMemberException;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\UserIsNotSprintCreatorException;
use App\Exceptions\CannotRemoveSprintCreatorException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SprintService
{
    private User $user;

    private Sprint $sprint;

    public function __construct()
    {
        $this->user = new User();
        $this->sprint = new Sprint();
    }

    public function index(User $user): stdClass
    {
        try {
            $sprints = Sprint::whereHas('members', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();

            $data['sprint'] = SprintResource::collection($sprints);
            $message = 'Sprints retrieved successfully';
            $status = 200;

            return prepareSuccessPayload($message, $status, $data);

        } catch (Exception $exception) {
            return handleException($exception);
        }
    }

    public function store(array $payload, User $user): stdClass
    {
        try {
            DB::transaction(function () use ($payload, $user, &$sprint){

                $sprint = $this->sprint->create([
                    'name' => $payload['name'],
                    'description' => $payload['goal'],
                    'start_at' => $payload['start_at'],
                    'end_at' => $payload['end_at'],
                ]);

                $sprint->members()->attach($user->id, ['is_admin' => true, 'is_creator' => true]);
            });

            $data['sprint'] = new SprintResource($sprint);
            $message = 'Sprint created successfully';
            $status = 201;

            return prepareSuccessPayload($message, $status, $data);

        } catch (Exception $exception) {
            return handleException($exception);
        }
    }

    public function update(array $payload, Sprint $sprint, User $user): stdClass
    {
        try {
            if (! $sprint->isCreator($user)) {
                throw new UnauthorizedToUpdateSprintException('You are not authorized to update this sprint.', 403);
            }

            DB::transaction(function () use ($payload, $sprint) {
                $updates = [
                    'name' => $payload['name'] ?? $sprint->name,
                    'description' => $payload['goal'] ?? $sprint->description,
                    'start_at' => $payload['start_at'] ?? $sprint->start_at,
                    'end_at' => $payload['end_at'] ?? $sprint->end_at,
                ];

                if (array_key_exists('name', $payload) && $payload['name'] !== $sprint->name) {
                    $slug = Str::of($payload['name'])->slug();
                    $updates['slug'] = Sprint::whereSlug($slug)->exists() ? $slug->append('-', rand()) : $slug;
                }

                $sprint->update($updates);
            });

            $data['sprint'] = new SprintResource($sprint->fresh());
            $message = 'Sprint updated successfully';
            $status = 200;

            return prepareSuccessPayload($message, $status, $data);

        } catch (Exception $exception) {
            return handleException($exception);
        }
    }

    public function sendInviteToUser(Sprint $sprint, string $username): stdClass
    {
        try {
            $user = $this->user->where('username', $username)->first();
            $data['url'] = URL::temporarySignedRoute('invite.user', now()->addDay(), ['user' => $user->username, 'sprint' => $sprint->slug]);

            return prepareSuccessPayload('Invite sent successfully', 200, $data);

        } catch (Exception $exception) {
            return handleException($exception);
        }
    }

    public function acceptInvitation(Sprint $sprint, User $user, string $username): stdClass
    {
        try {
            $invitedUser = $this->user->where('username', $username)->first();

            if ($invitedUser->id !== $user->id)
                throw new InvalidCredentialsException('this invitation is invalid', 403);

            if ($sprint->members()->wherePivot('user_id', $invitedUser->id)->exists())
                throw new AlreadyAMemberException('You are already a member of this sprint', 400);

            $sprint->members()->attach($user->id, ['is_admin' => true]);

            return prepareSuccessPayload('Invite accepted successfully');

        } catch (Exception|Throwable $exception) {
            return handleException($exception);
        }
    }

    public function removeMemberFromSprint(Sprint $sprint, User $user, string $username): stdClass
    {
        try {
            if (! $sprint->isCreator($user))
                throw new UserIsNotSprintCreatorException('Unauthorized! you cannot remove a member.', 403);

            $affectedMember = $this->user->where('username', $username)->first();
            if (! $affectedMember)
                throw new ModelNotFoundException('the specifed user does not exist.', 404);

            if ($affectedMember->id === $user->id)
                throw new Exception('Unauthorized', 403);

            $sprint->members()->detach($affectedMember->id);

            return prepareSuccessPayload('Member removed successfully');

        } catch (Exception $exception) {
            return handleException($exception);
        }
    }

    public function exit(Sprint $sprint, User $user): stdClass
    {
        try {
            if ($sprint->isCreator($user)) {
                $randomMember = $sprint->members()->where('user_id', '!=', $user->id)->first();

                if ($randomMember) {
                    $sprint->members()->updateExistingPivot($randomMember->id, [
                        'is_creator' => true,
                    ]);
                } else {
                    throw new CannotRemoveSprintCreatorException('Cannot remove creator: No other members in the sprint.', 400);
                }
            }

            $sprint->members()->detach($user->id);

            return prepareSuccessPayload('Member removed successfully');

        } catch (Exception $exception) {
            return handleException($exception);
        }
    }

    public function delete(Sprint $sprint, User $user): stdClass
    {
        try {
            if (! $sprint->isCreator($user)) {
                throw new UnauthorizedToDeleteSprintException('You are not authorized to update this sprint.', 403);
            }

            DB::transaction(function () use ($sprint){
                $sprint->members()->detach();
                $sprint->delete();
            });

            return prepareSuccessPayload('Sprint removed successfully');

        } catch (Exception $exception) {
            return handleException($exception);
        }
    }

    public function updateSprintCompletedStatus(Sprint $sprint): stdClass
    {
        try {
            $value = true;

            if ($sprint->is_completed) {
                $value = false;
            }

            $sprint->update(['is_completed' => $value]);

            $data['sprint'] = new SprintResource($sprint);

            return prepareSuccessPayload('Sprint status updated successfully', 200, $data);

        } catch (Exception $exception) {
            return handleException($exception);
        }
    }
}
