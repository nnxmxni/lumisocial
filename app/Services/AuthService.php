<?php

namespace App\Services;

use stdClass;
use Exception;
use Throwable;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Exceptions\InvalidCredentialsException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthService
{
    private Role $role;

    private User $user;

    private stdClass $payload;

    public function __construct()
    {
        $this->user = new User();
        $this->role = new Role();
        $this->payload = new stdClass();
    }

    public function register(array $payload): stdClass
    {
        try {

            DB::transaction(function () use ($payload, &$user){
                $user = $this->user->create([
                    'name' => ucwords(strtolower(Str::of($payload['name'])->trim())),
                    'email' => strtolower(trim($payload['email'])),
                    'username' => strtolower(trim($payload['username'])),
                    'password' => Hash::make($payload['password'])
                ]);

                $role = $this->role->where('name', $payload['type'])->first();

                $user->roles()->attach($role->id);
            });

            $this->payload->token = $user->createToken('auth_token')->plainTextToken;
            $this->payload->user = new UserResource($this->user->find($user->id));
            $this->payload->message = "Registration successful";
            $this->payload->status = 201;

            return $this->payload;

        } catch (Exception $exception) {
            return handleException($exception);
        }
    }

    public function login(array $payload): stdClass
    {
        try {
            $user = $this->user->where('email', $payload['email'])->first();
            if (! $user) throw new ModelNotFoundException('the user with the specified email does not exist', 404);

            if (! Hash::check($payload['password'], $user->password)) throw new InvalidCredentialsException('the email and password is wrong', 400);

            $this->payload->token = $user->createToken('auth_token')->plainTextToken;
            $this->payload->user = new UserResource($user);
            $this->payload->message = 'welcome back';
            $this->payload->status = 200;

            return $this->payload;

        } catch (Exception|Throwable $exception) {
            return handleException($exception);
        }
    }
}
