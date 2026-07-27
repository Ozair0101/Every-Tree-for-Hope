<?php

namespace App\Policies;

use App\Models\TaskNotification;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * An inbox belongs to exactly one person.
 *
 * No permission escape hatch, deliberately — not even for staff, and the
 * Gate::before() Super Admin bypass is the only thing that overrides it. There
 * is no legitimate reason for a coordinator to read a volunteer's notifications;
 * everything operationally interesting is already in the activity log, which is
 * built for exactly that purpose.
 */
class TaskNotificationPolicy
{
    use HandlesAuthorization;

    public function view(User $user, TaskNotification $notification): bool
    {
        return $notification->user_id === $user->id;
    }

    public function update(User $user, TaskNotification $notification): bool
    {
        return $notification->user_id === $user->id;
    }

    public function delete(User $user, TaskNotification $notification): bool
    {
        return $notification->user_id === $user->id;
    }
}
