<?php

namespace App\Http\Controllers\Api\V1\Tasks;

use App\Enums\TaskSubmissionStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\Tasks\StoreReviewRequest;
use App\Http\Resources\Api\V1\Tasks\TaskReviewResource;
use App\Http\Resources\Api\V1\Tasks\TaskSubmissionResource;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\TaskSubmission;
use App\Services\Tasks\TaskReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Reviewing submitted work.
 *
 *   GET  /tasks/reviews/queue                              — everything awaiting review
 *   GET  /tasks/{task}/assignments/{assignment}/reviews    — verdict history
 *   POST /tasks/{task}/assignments/{assignment}/reviews    — record a verdict
 *
 * The queue is a top-level route rather than a per-task one because that is how
 * a reviewer actually works: they open a list of everything waiting, not a task
 * they already had in mind.
 */
class TaskReviewController extends ApiController
{
    public function __construct(
        private readonly TaskReviewService $service,
    ) {}

    /**
     * The review queue — oldest first, so nothing ages out of sight.
     *
     * Scoped to what this reviewer may actually act on: holders of
     * `review_task` see everything, while someone named as reviewer on specific
     * tasks sees only those. Showing a reviewer work they cannot sign off is
     * just a queue that never empties.
     */
    public function queue(Request $request): JsonResponse
    {
        $user = $request->user();

        $submissions = TaskSubmission::query()
            ->where('status', TaskSubmissionStatus::PENDING->value)
            ->when(! $user->can('review_task'), fn ($q) => $q
                ->whereHas('task.reviewers', fn ($r) => $r->where('user_id', $user->id)))
            // Never queue your own work for your own review.
            ->where('user_id', '!=', $user->id)
            ->with([
                'task:id,uuid,reference,title,status,priority,due_date',
                'user:id,name,lastname,profile_image',
                'attachments',
                'assignment',
            ])
            ->oldest()
            ->paginate($this->perPage($request, 20));

        return $this->paginated($submissions, TaskSubmissionResource::class, [
            'summary' => [
                'pending' => $submissions->total(),
            ],
        ]);
    }

    /**
     * Every verdict passed on this assignment, newest first.
     *
     * Plural because the same attempt can be reviewed more than once — a second
     * opinion, or an appeal after better photos arrive.
     */
    public function index(Request $request, Task $task, TaskAssignment $assignment): JsonResponse
    {
        $this->authorize('view', $assignment);

        $reviews = $assignment->reviews()
            ->with(['reviewer:id,name,lastname', 'submission:id,attempt'])
            ->get();

        return $this->ok(TaskReviewResource::collection($reviews));
    }

    public function store(StoreReviewRequest $request, Task $task, TaskAssignment $assignment): JsonResponse
    {
        $review = $this->service->record(
            assignment: $assignment,
            status: $request->reviewStatus(),
            reviewer: $request->user(),
            score: $request->input('score'),
            rating: $request->input('rating'),
            comments: $request->input('comments'),
        );

        $review->load(['reviewer:id,name,lastname', 'submission:id,attempt']);

        return $this->created(
            new TaskReviewResource($review),
            'Review recorded: '.$request->reviewStatus()->label().'.',
        );
    }
}
