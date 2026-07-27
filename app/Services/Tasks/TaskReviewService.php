<?php

namespace App\Services\Tasks;

use App\Enums\TaskReviewStatus;
use App\Models\TaskAssignment;
use App\Models\TaskReview;
use App\Models\User;
use App\Services\Tasks\Exceptions\TaskOperationException;

/**
 * The reviewer's side: judging submitted work.
 *
 * TaskReview::record() already does the atomic part — the review row, the
 * submission's cached status and the assignee's own status in one transaction.
 * This adds the guard (there must be something to review) and the wording,
 * which differs per verdict for a reason: "rejected" and "needs revision" read
 * very differently to someone who spent a morning in a field.
 */
class TaskReviewService
{
    public function __construct(
        private readonly TaskNotificationService $notifications,
    ) {}

    public function record(
        TaskAssignment $assignment,
        TaskReviewStatus $status,
        User $reviewer,
        ?float $score = null,
        ?int $rating = null,
        ?string $comments = null,
    ): TaskReview {
        $submission = $assignment->latestSubmission;

        // Reviewing an assignment nobody has submitted to would create a verdict
        // about nothing, and would move the volunteer's status behind their back.
        if ($submission === null) {
            throw TaskOperationException::nothingToReview($assignment->task);
        }

        $review = TaskReview::record(
            assignment: $assignment,
            status: $status,
            reviewer: $reviewer,
            submission: $submission,
            score: $score,
            rating: $rating,
            comments: $comments,
        );

        // The wording now lives in the notification classes, which keeps the
        // push and the inbox entry identical by construction.
        if ($assignment->user) {
            $status === TaskReviewStatus::APPROVED
                ? $this->notifications->taskApproved($assignment, $comments)
                : $this->notifications->taskRejected($assignment, $status, $comments);
        }

        return $review;
    }
}
