<?php

namespace App\Http\Controllers\Api\V1\Tasks;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\Tasks\StoreProgressRequest;
use App\Http\Resources\Api\V1\Tasks\TaskProgressResource;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\TaskProgress;
use App\Services\Tasks\TaskProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Interim progress reports from the field.
 *
 *   GET  /tasks/{task}/progress                            — the whole task's trail
 *   GET  /tasks/{task}/assignments/{assignment}/progress   — one volunteer's trail
 *   POST /tasks/{task}/assignments/{assignment}/progress   — file a report
 *
 * Send `client_uuid` on the POST. A dropped response is retried by the app, and
 * the same key returns the original row instead of a duplicate that would skew
 * the task's progress percentage.
 */
class TaskProgressController extends ApiController
{
    public function __construct(
        private readonly TaskProgressService $service,
    ) {}

    /** Every assignee's reports on this task, newest first. */
    public function index(Request $request, Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        $progress = TaskProgress::query()
            ->where('task_id', $task->id)
            ->with('creator:id,name,lastname')
            ->latestFirst()
            ->paginate($this->perPage($request, 30));

        return $this->paginated($progress, TaskProgressResource::class, [
            'task_progress' => (int) $task->progress,
        ]);
    }

    /** One volunteer's trail — what the detail screen's timeline renders. */
    public function forAssignment(Request $request, Task $task, TaskAssignment $assignment): JsonResponse
    {
        $this->authorize('view', $assignment);

        $progress = $assignment->progressUpdates()
            ->with('creator:id,name,lastname')
            ->paginate($this->perPage($request, 30));

        return $this->paginated($progress, TaskProgressResource::class);
    }

    public function store(StoreProgressRequest $request, Task $task, TaskAssignment $assignment): JsonResponse
    {
        $progress = $this->service->report(
            assignment: $assignment,
            actor: $request->user(),
            percentage: (int) $request->input('progress_percentage'),
            note: $request->input('note'),
            latitude: $request->input('latitude'),
            longitude: $request->input('longitude'),
            files: $request->uploadedFiles(),
            clientUuid: $request->input('client_uuid'),
        );

        $progress->load('creator:id,name,lastname');

        return $this->created([
            'progress' => new TaskProgressResource($progress),
            // Returned so the client can update the task card without a second
            // request — the roll-up may differ from what was just reported when
            // the task is shared by a team.
            'task_progress' => (int) $task->fresh()->progress,
        ], 'Progress recorded.');
    }
}
