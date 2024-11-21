<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('task.created.{taskSlug}', function (User $user, string $taskSlug) {
    if ($user->isSprintMember($taskSlug)) {
        return ['id' => $user->id, 'name' => $user->name];
    }

    return false;
});
