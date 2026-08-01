<?php

namespace App\Http\Controllers\Api\V1\Tasks;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\Tasks\AssignTaskRequest;
use App\Http\Requests\Api\V1\Tasks\StoreSubmissionRequest;
use App\Http\Resources\Api\V1\Tasks\TaskAssignmentResource;
use App\Http\Resources\Api\V1\Tasks\TaskChecklistItemResource;
use App\Http\Resources\Api\V1\Tasks\TaskSubmissionResource;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\User;
use App\Services\Tasks\TaskAssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * The volunteer's side of a task.
 *
 *   GET  /tasks/{task}/assignments                    — who is on it
 *   POST /tasks/{task}/assignments                    — assign people (staff)
 *   POST /tasks/{task}/assignments/{assignment}/accept
 *   POST /tasks/{task}/assignments/{assignment}/decline
 *   POST /tasks/{task}/assignments/{assignment}/start
 *   POST /tasks/{task}/assignments/{assignment}/submit
 *   POST /tasks/{task}/assignments/{assignment}/reassign  — hand over (staff)
 *   GET  /tasks/{task}/assignments/{assignment}/checklist
 *   POST /tasks/{task}/assignments/{assignment}/checklist/{item}   — tick
 *   DELETE …/checklist/{item}                                      — untick
 *
 * Accept, start and submit are intentionally not available to staff acting on a
 * volunteer's behalf — see TaskAssignmentPolicy for why.
 */
class TaskAssignmentController extends ApiController
{
    public function __construct(
        private readonly TaskAssignmentService $service,
    ) {}

    /**
     * People this coordinator may hand work to.
     *
     * Exists so an admin can assign a task **from the phone** as well as from
     * the panel — without it the mobile assign screen would have no way to
     * choose anyone. Scoped to the volunteer role: staff accounts are not the
     * people who go into the field, and listing every user turns a picker into
     * a directory.
     */
    public function assignableUsers(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('assign_task'), 403);

        $search = trim((string) $request->query('search', ''));

        $users = User::query()
            ->role(User::VOLUNTEER_ROLE)
            ->when($search !== '', function ($query) use ($search) {
                $term = str_replace(['%', '_'], ['\%', '\_'], $search);

                $query->where(fn ($q) => $q
                    ->where('name', 'like', "%{$term}%")
                    ->orWhere('lastname', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%"));
            })
            ->orderBy('name')
            ->limit(50)
            ->get(['id', 'name', 'lastname', 'profile_image']);

        return $this->ok(
            $users->map(fn (User $user) => [
                'id' => $user->id,
                'name' => trim($user->name.' '.($user->lastname ?? '')),
                'avatar_url' => $user->profile_image
                    ? $request->getSchemeAndHttpHost().'/storage/'.ltrim($user->profile_image, '/')
                    : null,
            ]),
        );
    }

    public function index(Request $request, Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        $assignments = $task->assignments()
            ->with(['user:id,name,lastname,profile_image', 'assigner:id,name', 'latestSubmission', 'latestReview.reviewer:id,name'])
            ->withCount('submissions')
            ->get();

        return $this->ok(TaskAssignmentResource::collection($assignments));
    }

    public function store(AssignTaskRequest $request, Task $task): JsonResponse
    {
        $assignments = $this->service->assign(
            task: $task,
            userIds: $request->input('user_ids'),
            actor: $request->user(),
            role: $request->role(),
            primaryUserId: $request->input('primary_user_id'),
        );

        $assignments->load('user:id,name,lastname,profile_image');

        return $this->created(
            TaskAssignmentResource::collection($assignments),
            $assignments->count().' '.($assignments->count() === 1 ? 'person' : 'people').' assigned.',
        );
    }

    public function accept(Request $request, Task $task, TaskAssignment $assignment): JsonResponse
    {
        $this->authorize('accept', $assignment);

        $assignment = $this->service->accept($assignment, $request->user());

        return $this->ok(new TaskAssignmentResource($assignment), 'Task accepted.');
    }

    public function decline(Request $request, Task $task, TaskAssignment $assignment): JsonResponse
    {
        $this->authorize('decline', $assignment);

        $validated = $request->validate([
            // Optional but strongly wanted: the coordinator has to re-plan, and
            // "unavailable that week" changes what they do next.
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $assignment = $this->service->decline($assignment, $request->user(), $validated['reason'] ?? null);

        return $this->ok(new TaskAssignmentResource($assignment), 'Task declined.');
    }

    public function start(Request $request, Task $task, TaskAssignment $assignment): JsonResponse
    {
        $this->authorize('start', $assignment);

        $assignment = $this->service->start($assignment, $request->user());

        return $this->ok(new TaskAssignmentResource($assignment), 'Work started.');
    }

    public function submit(StoreSubmissionRequest $request, Task $task, TaskAssignment $assignment): JsonResponse
    {
        $submission = $this->service->submit(
            assignment: $assignment,
            actor: $request->user(),
            payload: $request->payload(),
            files: $request->uploadedFiles(),
            clientUuid: $request->input('client_uuid'),
        );

        $submission->load('attachments');

        return $this->created(
            new TaskSubmissionResource($submission),
            $task->requires_review ? 'Submitted for review.' : 'Task completed.',
        );
    }

    public function reassign(Request $request, Task $task, TaskAssignment $assignment): JsonResponse
    {
        $this->authorize('reassign', $assignment);

        $validated = $request->validate([
            'user_id' => [
                'required', 'integer', 'exists:users,id',
                // Handing a task to the person who already holds it is a no-op
                // that would still close their assignment as `reassigned` and
                // open a fresh one — losing their accepted/started timestamps.
                Rule::notIn([$assignment->user_id]),
            ],
            'reason' => ['nullable', 'string', 'max:255'],
        ], [
            'user_id.not_in' => 'That volunteer already holds this task.',
        ]);

        $newAssignment = $this->service->reassign(
            task: $task,
            from: $assignment->user,
            to: User::findOrFail($validated['user_id']),
            actor: $request->user(),
            reason: $validated['reason'] ?? null,
        );

        $newAssignment->load('user:id,name,lastname,profile_image');

        return $this->ok(new TaskAssignmentResource($newAssignment), 'Task reassigned.');
    }

    /**
     * The checklist, with this assignee's own ticks.
     *
     * Completions are eager-loaded once and the per-item answer resolved in
     * memory — the alternative is a query per step, on a screen that shows
     * every step.
     */
    public function checklist(Request $request, Task $task, TaskAssignment $assignment): JsonResponse
    {
        $this->authorize('view', $assignment);

        $items = $task->checklistItems()->with('completions')->get();

        return $this->ok(
            TaskChecklistItemResource::collection($items)->additional(['assignment' => $assignment])
        );
    }

    public function tickChecklistItem(Request $request, Task $task, TaskAssignment $assignment, int $item): JsonResponse
    {
        $this->authorize('submit', $assignment);

        $checklistItem = $task->checklistItems()->findOrFail($item);
        $checklistItem->completeFor($assignment, $request->user());

        return $this->ok(['progress' => (int) $task->fresh()->progress], 'Step completed.');
    }

    public function untickChecklistItem(Request $request, Task $task, TaskAssignment $assignment, int $item): JsonResponse
    {
        $this->authorize('submit', $assignment);

        $checklistItem = $task->checklistItems()->findOrFail($item);
        $checklistItem->uncompleteFor($assignment);

        return $this->ok(['progress' => (int) $task->fresh()->progress], 'Step reopened.');
    }
}
