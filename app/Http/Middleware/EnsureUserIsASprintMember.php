<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\UserIsNotASprintMemberException;

class EnsureUserIsASprintMember
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     * @throws UserIsNotASprintMemberException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $sprint = $request->sprint;

        $sprintMember = $sprint->members()->where('id', $user->id)->first();

        if(! $sprintMember) throw new UserIsNotASprintMemberException('Unathorized! this user is not a member of the sprint', 403);

        return $next($request);
    }
}
