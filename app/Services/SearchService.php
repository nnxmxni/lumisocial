<?php

namespace App\Services;

use stdClass;
use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use App\Http\Resources\UserResource;

class SearchService
{
    public function searchByUsername(array $payload): stdClass
    {
        try {
            $users = User::where('username', 'like', '%' . Str::lower($payload['username']) . '%')
                ->get();

            $message = 'search result retrieved';

            if (count($users) < 1 ) $message = 'no result found';

            $data['users'] = UserResource::collection($users);
            $status = 200;

            return prepareSuccessPayload($message, $status, $data);

        } catch (Exception $exception) {
            return handleException($exception);
        }
    }

}
