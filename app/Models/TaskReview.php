<?php

namespace App\Models;

use App\Enums\TaskReviewStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * An admin's evaluation of a volunteer's submitted work.
 *
 * A row per review event, not a set of columns on the submission: the same
 * attempt can be reviewed more than once — a second opinion, an appeal after the
 * volunteer sends better photos, a supervisor overruling a junior — and columns
 * could only ever remember the last opinion. The earlier one is precisely what
 * an appeal needs.
 *
 * `task_submissions.status` stays as the cache the review queue filters on;
 * this table is the record of who said what, and when.
 */
class TaskReview extends Model
{
    protected $fillable = [
        'task_assignment_id',
        'task_submission_id',
        'task_id',
        'score',
        'rating',
        'comments',
        'review_status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'rating' => 'integer',
        'review_status' => TaskReviewStatus::class,
        'reviewed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $review) {
            $review->reviewed_at ??= now();

            // 1–5 stars, or nothing. A 0 or a 9 is a client bug, and silently
            // storing it would poison every average built on this column.
            if ($review->rating !== null) {
                $review->rating = max(1, min(5, (int) $review->rating));
            }

            if ($review->score !== null) {
                $review->score = max(0, min(100, (float) $review->score));
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TaskAssignment::class, 'task_assignment_id');
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(TaskSubmission::class, 'task_submission_id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Recording a verdict
    |--------------------------------------------------------------------------
    */

    /**
     * Record a review and apply its consequences.
     *
     * One transaction covers the review row, the submission's cached status and
     * the assignee's own status, so a crash halfway cannot leave a submission
     * marked approved while the volunteer's assignment still says "submitted".
     * The assignment transition then rolls the task status up, which is how a
     * single reviewer action moves the whole tree.
     */
    public static function record(
        TaskAssignment $assignment,
        TaskReviewStatus $status,
        ?User $reviewer = null,
        ?TaskSubmission $submission = null,
        ?float $score = null,
        ?int $rating = null,
        ?string $comments = null,
    ): self {
        $submission ??= $assignment->latestSubmission;

        return DB::transaction(function () use ($assignment, $status, $reviewer, $submission, $score, $rating, $comments) {
            $review = static::create([
                'task_assignment_id' => $assignment->id,
                'task_submission_id' => $submission?->id,
                'task_id' => $assignment->task_id,
                'score' => $score,
                'rating' => $rating,
                'comments' => $comments,
                'review_status' => $status->value,
                'reviewed_by' => $reviewer?->id,
                'reviewed_at' => now(),
            ]);

            $submission?->forceFill(['status' => $status->submissionStatus()->value])->save();

            // Moves the assignee, which rolls the task up in turn. The comments
            // become the assignment's remarks so the volunteer sees why.
            $assignment->transitionTo($status->assignmentStatus(), $reviewer, $comments);

            return $review;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('review_status', TaskReviewStatus::APPROVED->value);
    }

    public function scopeNeedingRevision(Builder $query): Builder
    {
        return $query->where('review_status', TaskReviewStatus::NEEDS_REVISION->value);
    }

    public function scopeByReviewer(Builder $query, int $userId): Builder
    {
        return $query->where('reviewed_by', $userId);
    }

    /** Newest verdict first — the review history of an attempt. */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('reviewed_at');
    }
}
