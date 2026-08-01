<?php

namespace App\Services\Tasks;

use App\Enums\TaskActivityAction;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use App\Notifications\Tasks\GenericTaskNotification;
use Illuminate\Support\Facades\DB;

/**
 * Discussion on a task.
 *
 * Small, but worth its own service for one reason: posting a comment is three
 * writes — the comment, the activity entry, and the notification — and a
 * reviewer typing "which row did you mean?" expects the volunteer to actually
 * receive it.
 */
class TaskCommentService
{
    public function __construct(
        private readonly TaskNotificationService $notifications,
    ) {}

    /**
     * Post a comment.
     *
     * `internal` marks a staff-only note. Those are never notified to the
     * volunteer and never leave the panel — the whole point is somewhere for
     * reviewers to discuss weak work without the person who did it reading the
     * discussion.
     */
    public function post(
        Task $task,
        User $author,
        string $body,
        bool $internal = false,
        ?int $parentId = null,
    ): TaskComment {
        return DB::transaction(function () use ($task, $author, $body, $internal, $parentId) {
            $comment = $task->comments()->create([
                'user_id' => $author->id,
                'parent_id' => $parentId,
                'body' => trim($body),
                'is_internal' => $internal,
            ]);

            $task->logActivity(
                TaskActivityAction::COMMENTED,
                $author,
                meta: ['internal' => $internal],
            );

            if (! $internal) {
                $this->notifications->notifyTaskParticipants(
                    task: $task,
                    notification: new GenericTaskNotification(
                        action: TaskActivityAction::COMMENTED,
                        title: __('New comment on your task'),
                        body: __(':who: :comment', [
                            'who' => $author->name,
                            'comment' => \Illuminate\Support\Str::limit(trim($body), 120),
                        ]),
                        task: $task,
                    ),
                    except: $author,
                );
            }

            return $comment;
        });
    }
}
