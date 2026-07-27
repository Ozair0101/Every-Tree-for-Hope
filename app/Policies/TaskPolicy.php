<?php

namespace App\Policies;

use App\Enums\TaskAssignmentRole;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Authorisation for tasks.
 *
 * Unlike every other policy in this app, this one cannot be pure
 * {@see BasePolicy}. The others guard the Filament panel, whose users all hold
 * a role; this one also guards the mobile API, whose users are volunteers with
 * **no role and no permissions at all**. A straight `view_task` check would
 * lock every volunteer out of the task they were just assigned.
 *
 * So each ability is "staff permission OR standing on this particular task".
 * The permission side keeps the admin panel working exactly as the other
 * modules do; the relationship side is what makes the app usable.
 *
 * Super Admin bypasses all of this via Gate::before() in AuthServiceProvider.
 */
class TaskPolicy extends BasePolicy
{
    protected string $prefix = 'task';

    /**
     * Everyone signed in may ask for a task list.
     *
     * The list itself is scoped in the controller: staff get the whole board,
     * volunteers get their own assignments. Refusing here instead would mean a
     * volunteer could not open the app's main screen.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Model $task): bool
    {
        return parent::view($user, $task) || $this->isAttached($user, $task);
    }

    /**
     * Editing the brief is a staff act.
     *
     * A volunteer changing the instructions of their own task could quietly
     * redefine the job to match whatever they did.
     */
    public function update(User $user, Model $task): bool
    {
        return $this->allows($user, 'update');
    }

    /** Hand the task to volunteers. Separate from `update` on purpose. */
    public function assign(User $user, Task $task): bool
    {
        return $user->can('assign_task');
    }

    /**
     * Judge submitted work.
     *
     * A named reviewer on the task may review it without holding the global
     * permission — that is the point of the reviewer role. Nobody reviews their
     * own submission, whatever they hold.
     */
    public function review(User $user, Task $task): bool
    {
        if ($this->isAssignee($user, $task)) {
            return false;
        }

        return $user->can('review_task') || $this->isReviewer($user, $task);
    }

    public function cancel(User $user, Task $task): bool
    {
        return $user->can('cancel_task') || $user->can('delete_task');
    }

    /** Turn work in — assignees only, never reviewers or watchers. */
    public function submit(User $user, Task $task): bool
    {
        return $this->isAssignee($user, $task);
    }

    public function reportProgress(User $user, Task $task): bool
    {
        return $this->isAssignee($user, $task);
    }

    /** Read the discussion. Internal notes are filtered separately, in the resource. */
    public function comment(User $user, Task $task): bool
    {
        return $this->view($user, $task);
    }

    /** May this user see staff-only comments? */
    public function viewInternalNotes(User $user, Task $task): bool
    {
        return $user->can('update_task') || $this->isReviewer($user, $task);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationship checks
    |--------------------------------------------------------------------------
    |
    | `exists()` rather than loading the collection: these run on every request
    | that touches a task, and the (task_id, role, status) index answers them
    | without hydrating a single model.
    */

    private function isAttached(User $user, Task $task): bool
    {
        return $task->assignments()->where('user_id', $user->id)->exists();
    }

    private function isAssignee(User $user, Task $task): bool
    {
        return $task->assignments()
            ->where('user_id', $user->id)
            ->where('role', TaskAssignmentRole::ASSIGNEE->value)
            ->exists();
    }

    private function isReviewer(User $user, Task $task): bool
    {
        return $task->assignments()
            ->where('user_id', $user->id)
            ->where('role', TaskAssignmentRole::REVIEWER->value)
            ->exists();
    }
}
