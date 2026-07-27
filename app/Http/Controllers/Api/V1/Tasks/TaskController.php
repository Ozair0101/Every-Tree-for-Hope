<?php

namespace App\Http\Controllers\Api\V1\Tasks;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\Tasks\IndexTaskRequest;
use App\Http\Requests\Api\V1\Tasks\StoreTaskRequest;
use App\Http\Requests\Api\V1\Tasks\UpdateTaskRequest;
use App\Http\Resources\Api\V1\Tasks\TaskListResource;
use App\Http\Resources\Api\V1\Tasks\TaskResource;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Services\Tasks\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Tasks — the field-operations work item.
 *
 *   GET    /tasks              — filtered, sorted, paginated list
 *   GET    /tasks/mine         — the caller's own assignments + summary
 *   GET    /tasks/map          — lean markers for the map screen
 *   POST   /tasks              — create (staff)
 *   GET    /tasks/{task}       — full detail
 *   PATCH  /tasks/{task}       — edit the brief (staff)
 *   POST   /tasks/{task}/publish — leave Draft
 *   POST   /tasks/{task}/cancel  — stop the work
 *   DELETE /tasks/{task}       — soft delete, only while nothing was submitted
 *
 * `{task}` binds on the UUID, never the integer id — see HasPublicUuid.
 *
 * The controller does three things and no more: authorise, delegate, respond.
 * Query construction lives in the repository, business rules in the service,
 * and shape in the resource. That is what keeps this file readable as the
 * module grows.
 */
class TaskController extends ApiController
{
    public function __construct(
        private readonly TaskRepositoryInterface $tasks,
        private readonly TaskService $service,
    ) {}

    /**
     * The task board.
     *
     * Staff see everything; a volunteer sees only their own work. The narrowing
     * happens here rather than in the policy because it is a *scope* rather than
     * a permission — a volunteer asking for the list is not doing anything
     * forbidden, they simply have a smaller list.
     */
    public function index(IndexTaskRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Task::class);

        $filters = $request->filters();

        if (! $request->user()->can('view_any_task')) {
            $filters = $filters->forAssignee($request->user()->id);
        }

        $tasks = $this->tasks->paginate($filters, $this->perPage($request, 20));

        return $this->paginated($tasks, TaskListResource::class, [
            'counts' => $this->tasks->statusCounts($filters),
        ]);
    }

    /** "My Tasks" — the app's home screen. */
    public function mine(IndexTaskRequest $request): JsonResponse
    {
        $user = $request->user();

        $tasks = $this->tasks->paginate(
            $request->filters()->forAssignee($user->id),
            $this->perPage($request, 20),
        );

        return $this->paginated($tasks, TaskListResource::class, [
            'summary' => $this->tasks->summaryForUser($user),
        ]);
    }

    /**
     * Markers for the map.
     *
     * Separate from index() so the map can load a wide area cheaply: no
     * pagination, no relations, and only the columns a pin needs.
     */
    public function map(IndexTaskRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Task::class);

        $filters = $request->filters();

        if (! $request->user()->can('view_any_task')) {
            $filters = $filters->forAssignee($request->user()->id);
        }

        $markers = $this->tasks->markers($filters)->map(fn (Task $task) => [
            'id' => $task->uuid,
            'reference' => $task->reference,
            'title' => $task->title,
            'status' => $task->status->value,
            'priority' => $task->priority->value,
            'color' => $task->priority->color(),
            'latitude' => (float) $task->latitude,
            'longitude' => (float) $task->longitude,
            'radius' => $task->radius,
            'due_date' => $task->due_date?->toIso8601String(),
        ]);

        return $this->ok(['markers' => $markers]);
    }

    public function show(Request $request, Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        // Re-read through the repository for the detail-shaped eager loading.
        // Route binding gives a bare model; rendering the detail screen off it
        // would lazy-load a dozen relations one query at a time.
        $task = $this->tasks->findForDetail($task->uuid) ?? $task;

        return $this->ok(new TaskResource($task));
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->service->create(
            attributes: $request->taskAttributes(),
            actor: $request->user(),
            checklist: $request->input('checklist', []),
            assigneeIds: $request->input('assignee_ids', []),
        );

        return $this->created(
            new TaskResource($this->tasks->findForDetail($task->uuid) ?? $task),
            'Task created.',
        );
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task = $this->service->update(
            task: $task,
            attributes: $request->taskAttributes(),
            actor: $request->user(),
            // `has()` not `filled()`: an empty checklist array is a meaningful
            // instruction ("remove every step"), and filled() would discard it.
            checklist: $request->has('checklist') ? $request->input('checklist', []) : null,
        );

        return $this->ok(
            new TaskResource($this->tasks->findForDetail($task->uuid) ?? $task),
            'Task updated.',
        );
    }

    public function publish(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $task = $this->service->publish($task, $request->user());

        return $this->ok(new TaskResource($task), 'Task published.');
    }

    public function cancel(Request $request, Task $task): JsonResponse
    {
        $this->authorize('cancel', $task);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $task = $this->service->cancel($task, $request->user(), $validated['reason']);

        return $this->ok(new TaskResource($task), 'Task cancelled.');
    }

    public function destroy(Request $request, Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $this->service->delete($task, $request->user());

        return $this->ok(null, 'Task deleted.');
    }
}
