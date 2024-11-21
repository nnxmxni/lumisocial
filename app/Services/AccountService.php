<?php

namespace App\Services;

use stdClass;
use Throwable;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Onetimepassword;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ExpiredTokenException;
use App\Exceptions\InvalidTokenException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AccountService
{
    private User $user;

    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * @throws Throwable
     */
    public function verifyOTP(User $user, int $token): void
    {
        $token = Onetimepassword::where('user_id', $user->id)->where('token', $token)->first();

        if (is_null($token))
            throw new InvalidTokenException('Oops! The token provided is invalid. Please request a new token and try again.', 400);

        if (strtotime($token->expires_at) < strtotime(Carbon::now()))
            throw new ExpiredTokenException('Oops! Your token has expired. Please request a new token and try again.', 400);

        $token->delete();

        http_response_code(200);
    }

    public function prepassword(array $payload): stdClass
    {
        try {
            $user = $this->user->where('email', $payload['email'])->first();

            if (is_null($user))
                throw new ModelNotFoundException('The user with the specified phone number does not exist.', 404);

            $token = generateToken(6);

            DB::transaction(function () use ($user, $token) {
                $user->onetimepassword()->create(['token' => $token, 'expires_at' => now()->addSeconds(120)]);
            });

            $message = 'Success! Reset instructions have been sent to your phone number - if this account exists.';
            $data['token'] = $token;

            return prepareSuccessPayload($message, 200, $data);

        } catch (ModelNotFoundException|Exception|Throwable $exception) {
            return handleException($exception);
        }
    }

    public function postpassword(array $payload): stdClass
    {
        try {
            DB::transaction(function () use ($payload) {
                $user = $this->user->where('email', $payload['email'])->first();
                if (is_null($user))
                    throw new ModelNotFoundException('The user with the specified phone number does not exist.', 404);

                $this->verifyOTP($user, $payload['token']);
                $user->update(['password' => $payload['password']]);
            });

            return prepareSuccessPayload('Password reset successfully');

        } catch (ModelNotFoundException|InvalidTokenException|ExpiredTokenException|Exception|Throwable $exception) {
            return handleException($exception);
        }
    }

    public function logout(User $user): stdClass
    {
        try {
            $user->currentAccessToken()->delete();
            return prepareSuccessPayload('See you soon');
        } catch (Exception $exception) {
            return handleException($exception);
        }
    }
}
