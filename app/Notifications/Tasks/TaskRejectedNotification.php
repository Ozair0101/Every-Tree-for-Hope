<?php

namespace App\Notifications\Tasks;

use App\Enums\TaskActivityAction;
use App\Enums\TaskReviewStatus;

/**
 * Sent to the volunteer when work is refused or sent back.
 *
 * Carries the verdict so the wording can differ: "not accepted" and "nearly
 * there" are very different messages to someone who spent a morning in a field,
 * and collapsing them into one is how a volunteer system loses volunteers.
 */
class TaskRejectedNotification extends TaskEventNotification
{
    public function __construct(
        $task = null,
        $assignment = null,
        public readonly TaskReviewStatus $status = TaskReviewStatus::REJECTED,
        public readonly ?string $comments = null,
    ) {
        parent::__construct($task, $assignment);
    }

    public function eventKey(): string
    {
        return $this->status === TaskReviewStatus::NEEDS_REVISION
            ? TaskActivityAction::NEEDS_REVISION->value
            : TaskActivityAction::REJECTED->value;
    }

    public function title(object $notifiable): string
    {
        return $this->status === TaskReviewStatus::NEEDS_REVISION
            ? __('A small change is needed')
            : __('Your submission was not accepted');
    }

    public function body(object $notifiable): string
    {
        $task = $this->task?->title ?? __('your task');

        $lead = $this->status === TaskReviewStatus::NEEDS_REVISION
            ? __(':task is nearly there.', ['task' => $task])
            : __(':task was not accepted.', ['task' => $task]);

        // The comments are the actionable part — without them the volunteer
        // knows only that they failed, not what to do about it.
        return $this->comments ? "{$lead} {$this->comments}" : $lead;
    }
}
