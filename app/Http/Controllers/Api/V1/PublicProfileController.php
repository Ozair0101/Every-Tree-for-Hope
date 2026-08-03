<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TaskAssignmentStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Api\V1\TreeResource;
use App\Models\Tree;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * What one member may see about another.
 *
 * Reached by tapping a name anywhere it appears — on a post, on a comment — so
 * "who planted this?" is answerable without leaving the app. Deliberately
 * narrow: a display name, an avatar, where they are, when they joined, and the
 * trees they have had approved. No email, no address, no post count that
 * includes work still in review.
 *
 * The task block is the one exception, and it is permission-gated: a
 * coordinator opening a volunteer's profile needs to see what that person is
 * carrying and what they have finished. Nobody without `view_any_task` is told
 * that tasks exist at all — the key is simply absent from the response rather
 * than present and empty, which would itself leak that there is something to
 * see.
 */
class PublicProfileController extends ApiController
{
    public function show(Request $request, User $user): JsonResponse
    {
        $trees = Tree::query()
            ->approved()
            ->where('user_id', $user->id)
            ->with(['user', 'beforeImages', 'afterImages'])
            ->withCount(['likes', 'comments'])
            ->newest()
            ->paginate($this->perPage($request, 12));

        $payload = [
            'user' => [
                'id' => $user->id,
                'name' => trim($user->name.' '.($user->lastname ?? '')),
                'country' => $user->country,
                'avatar_url' => $user->profile_image
                    ? $request->getSchemeAndHttpHost().'/storage/'.ltrim($user->profile_image, '/')
                    : null,
                'cover_url' => $user->cover_image
                    ? $request->getSchemeAndHttpHost().'/storage/'.ltrim($user->cover_image, '/')
                    : null,
                // A standing, not a job title. Someone with no assigned role is
                // a volunteer, which is worth saying rather than leaving blank.
                'roles' => $user->getRoleNames()->values(),
                'member_since' => $user->created_at?->toIso8601String(),
                'trees_count' => Tree::approved()->where('user_id', $user->id)->count(),
            ],
            'trees' => TreeResource::collection($trees)->resolve(),
            'meta' => $this->paginationMeta($trees),
        ];

        /*
         * Resolved through the sanctum guard by name, not `$request->user()`.
         *
         * This route is public — it carries no auth middleware — so the default
         * guard is `web`, which knows nothing about a bearer token and returns
         * null even for a signed-in coordinator. The effect was a permission
         * check that silently failed closed for everyone: the task block never
         * appeared, for anybody.
         */
        if ($request->user('sanctum')?->can('view_any_task')) {
            $payload['tasks'] = $this->taskSummary($user);
        }

        return $this->ok($payload);
    }

    /**
     * This person's workload, for a coordinator.
     *
     * Grouped by what a coordinator actually asks: what is finished, what is
     * being worked on now, and what has been handed over but not yet picked up.
     * The per-assignment status is the volunteer's own clock, not the task's
     * roll-up — a task shared by four people is "in progress" for one of them
     * and untouched by another, and only the assignment row knows which.
     *
     * @return array<string, mixed>
     */
    private function taskSummary(User $user): array
    {
        $assignments = $user->taskAssignments()
            ->with('task:id,uuid,reference,title,status,priority,due_date')
            ->latest()
            ->limit(50)
            ->get();

        $shape = fn ($assignment) => [
            'assignment_id' => $assignment->id,
            'status' => [
                'value' => $assignment->status->value,
                'label' => $assignment->status->label(),
            ],
            'task' => $assignment->task ? [
                'id' => $assignment->task->uuid,
                'reference' => $assignment->task->reference,
                'title' => $assignment->task->title,
                'due_date' => $assignment->task->due_date?->toIso8601String(),
            ] : null,
            'started_at' => $assignment->started_at?->toIso8601String(),
            'submitted_at' => $assignment->submitted_at?->toIso8601String(),
            'completed_at' => $assignment->completed_at?->toIso8601String(),
        ];

        $byStatus = fn (array $statuses) => $assignments
            ->filter(fn ($a) => in_array($a->status, $statuses, true))
            ->map($shape)
            ->values()
            ->all();

        return [
            'completed' => $byStatus([TaskAssignmentStatus::APPROVED]),
            'in_progress' => $byStatus([
                TaskAssignmentStatus::IN_PROGRESS,
                TaskAssignmentStatus::SUBMITTED,
            ]),
            'pending' => $byStatus([
                TaskAssignmentStatus::PENDING,
                TaskAssignmentStatus::ACCEPTED,
            ]),
            'counts' => [
                'total' => $assignments->count(),
                'completed' => $assignments
                    ->where('status', TaskAssignmentStatus::APPROVED)->count(),
                'in_progress' => $assignments
                    ->whereIn('status', [
                        TaskAssignmentStatus::IN_PROGRESS,
                        TaskAssignmentStatus::SUBMITTED,
                    ])->count(),
            ],
        ];
    }
}
