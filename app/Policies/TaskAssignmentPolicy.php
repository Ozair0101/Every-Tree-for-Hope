<?php

namespace App\Policies;

use App\Models\TaskAssignment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Authorisation for one volunteer's attachment to a task.
 *
 * Not a {@see BasePolicy} subclass: an assignment has no CRUD permissions of
 * its own and never appears as a Filament resource. Its rules are entirely
 * about ownership — you act on your own assignment, and staff act on anyone's.
 *
 * Note the deliberate asymmetry: staff may *view* and *cancel* an assignment
 * but may not accept, start or submit on a volunteer's behalf. Those record
 * that a specific person did something, and letting a coordinator fake them
 * would make the lifecycle timestamps worthless as evidence.
 */
class TaskAssignmentPolicy
{
    use HandlesAuthorization;

    public function view(User $user, TaskAssignment $assignment): bool
    {
        return $this->owns($user, $assignment)
            || $user->can('view_task')
            || $user->can('view_any_task');
    }

    public function accept(User $user, TaskAssignment $assignment): bool
    {
        return $this->owns($user, $assignment);
    }

    public function decline(User $user, TaskAssignment $assignment): bool
    {
        return $this->owns($user, $assignment);
    }

    public function start(User $user, TaskAssignment $assignment): bool
    {
        return $this->owns($user, $assignment);
    }

    public function submit(User $user, TaskAssignment $assignment): bool
    {
        return $this->owns($user, $assignment) && $assignment->role->canSubmit();
    }

    public function reportProgress(User $user, TaskAssignment $assignment): bool
    {
        return $this->owns($user, $assignment) && $assignment->role->canSubmit();
    }

    /** Taking a task off someone is a coordinator's call, never the volunteer's. */
    public function reassign(User $user, TaskAssignment $assignment): bool
    {
        return $user->can('assign_task');
    }

    private function owns(User $user, TaskAssignment $assignment): bool
    {
        return $assignment->user_id === $user->id;
    }
}
