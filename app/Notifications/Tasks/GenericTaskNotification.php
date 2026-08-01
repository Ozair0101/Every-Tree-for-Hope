<?php

namespace App\Notifications\Tasks;

use App\Enums\TaskActivityAction;
use App\Models\Task;
use App\Models\TaskAssignment;

/**
 * A task notification whose wording is supplied by the caller.
 *
 * The six events the requirements name each have their own class, because their
 * wording is worth owning in one place and getting right. This covers the rest —
 * updated, cancelled, declined, reassigned — where a dedicated class per event
 * would be five files that each hold one sentence.
 *
 * The line between the two is deliberate: if one of these ever grows a rule
 * about *how* it is worded, it should graduate to its own class rather than
 * accumulate conditionals here.
 */
class GenericTaskNotification extends TaskEventNotification
{
    public function __construct(
        private readonly TaskActivityAction $action,
        private readonly string $title,
        private readonly string $body,
        ?Task $task = null,
        ?TaskAssignment $assignment = null,
    ) {
        parent::__construct($task, $assignment);
    }

    public function eventKey(): string
    {
        return $this->action->value;
    }

    public function title(object $notifiable): string
    {
        return $this->title;
    }

    public function body(object $notifiable): string
    {
        return $this->body;
    }
}
