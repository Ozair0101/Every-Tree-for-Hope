<?php

namespace App\Notifications\Tasks;

use App\Enums\TaskActivityAction;

/**
 * Sent to whoever is accountable for a task when a volunteer files progress.
 *
 * Distinct from TaskSubmittedNotification: this is work still in flight, not
 * work waiting on a decision. A coordinator reads it to know the job is moving
 * — which is the only way to tell "nobody has started" from "someone is
 * halfway" without opening every task in turn.
 */
class TaskProgressReportedNotification extends TaskEventNotification
{
    public function __construct(
        \App\Models\Task $task,
        \App\Models\TaskAssignment $assignment,
        private readonly int $percentage = 0,
        private readonly ?string $note = null,
    ) {
        parent::__construct($task, $assignment);
    }

    public function eventKey(): string
    {
        return TaskActivityAction::PROGRESS_UPDATED->value;
    }

    public function title(object $notifiable): string
    {
        return __('Progress on :task', ['task' => $this->task?->title ?? __('a task')]);
    }

    public function body(object $notifiable): string
    {
        $who = $this->assignment?->user?->name ?? __('A volunteer');

        $line = __(':who reported :percent% complete.', [
            'who' => $who,
            'percent' => $this->percentage,
        ]);

        // The volunteer's own words matter more than the number, so they are
        // carried through rather than left for whoever opens the task.
        return $this->note ? $line.' '.\Illuminate\Support\Str::limit($this->note, 140) : $line;
    }
}
