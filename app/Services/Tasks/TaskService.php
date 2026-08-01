<?php

namespace App\Services\Tasks;

use App\Enums\TaskActivityAction;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\TaskTemplate;
use App\Models\User;
use App\Notifications\Tasks\GenericTaskNotification;
use App\Notifications\Tasks\TaskAssignedNotification;
use App\Services\Tasks\Exceptions\TaskOperationException;
use Illuminate\Support\Facades\DB;

/**
 * Write-side orchestration for the task itself.
 *
 * The models already own their invariants — the state machine lives in
 * TaskStatus, the assignment cap in Task::assign(). What the service adds is
 * the *use case*: a create is a task plus its checklist plus its first
 * assignees plus the notifications that follow, and either all of that happens
 * or none of it does.
 *
 * Controllers call this. Nothing here knows about HTTP.
 */
class TaskService
{
    public function __construct(
        private readonly TaskNotificationService $notifications,
    ) {}

    /**
     * Create a task, with its checklist and initial assignees.
     *
     * @param  array<string, mixed>  $attributes
     * @param  array<int, array{title: string, description?: string, is_required?: bool, requires_photo?: bool}>  $checklist
     * @param  array<int, int>  $assigneeIds
     */
    public function create(array $attributes, User $actor, array $checklist = [], array $assigneeIds = []): Task
    {
        return DB::transaction(function () use ($attributes, $actor, $checklist, $assigneeIds) {
            $task = Task::create($attributes + ['created_by' => $actor->id]);

            $this->syncChecklist($task, $checklist);

            if ($assigneeIds !== []) {
                $users = User::query()->whereIn('id', $assigneeIds)->get();
                $task->assignMany($users, $actor);

                $this->notifications->taskAssigned($task, $users);
            }

            return $task->refresh();
        });
    }

    /**
     * Update a task's definition.
     *
     * Refuses once the work is finished. Editing the brief of an approved task
     * would silently rewrite what the volunteer was actually asked to do, and
     * every photo and signature attached to it would then be evidence for a
     * different job.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function update(Task $task, array $attributes, User $actor, ?array $checklist = null): Task
    {
        if ($task->status->isTerminal()) {
            throw TaskOperationException::closed($task);
        }

        return DB::transaction(function () use ($task, $attributes, $actor, $checklist) {
            $task->fill($attributes)->save();

            if ($checklist !== null) {
                $this->syncChecklist($task, $checklist, replace: true);
            }

            // Only tell people if something they act on actually moved.
            $material = array_intersect(
                array_keys($task->getChanges()),
                ['title', 'instructions', 'due_date', 'start_date', 'priority', 'latitude', 'longitude', 'radius'],
            );

            if ($material !== [] && $task->status !== TaskStatus::DRAFT) {
                $this->notifications->notifyTaskParticipants(
                    task: $task,
                    notification: new GenericTaskNotification(
                        action: TaskActivityAction::UPDATED,
                        title: __('Task updated'),
                        body: __(':task has changed. Please review the details.', ['task' => $task->title]),
                        task: $task,
                    ),
                    except: $actor,
                );
            }

            return $task->refresh();
        });
    }

    /** Publish a draft so its assignees can see it. */
    public function publish(Task $task, User $actor): Task
    {
        if (! $task->transitionTo(TaskStatus::ASSIGNED, $actor)) {
            throw TaskOperationException::illegalTransition($task, TaskStatus::ASSIGNED);
        }

        $this->notifications->notifyTaskParticipants(
            task: $task,
            notification: new TaskAssignedNotification($task),
            except: $actor,
        );

        return $task->refresh();
    }

    public function cancel(Task $task, User $actor, string $reason): Task
    {
        if (! $task->transitionTo(TaskStatus::CANCELLED, $actor, $reason)) {
            throw TaskOperationException::illegalTransition($task, TaskStatus::CANCELLED);
        }

        $this->notifications->notifyTaskParticipants(
            task: $task,
            notification: new GenericTaskNotification(
                action: TaskActivityAction::CANCELLED,
                title: __('Task cancelled'),
                body: __(':task was cancelled: :reason', ['task' => $task->title, 'reason' => $reason]),
                task: $task,
            ),
            except: $actor,
        );

        return $task->refresh();
    }

    /**
     * Soft delete.
     *
     * Refused once anyone has submitted work: the submissions are evidence that
     * something happened in the field, and hiding the task hides them too.
     * Cancelling is the correct move for work that should stop.
     */
    public function delete(Task $task, User $actor): void
    {
        if ($task->submissions()->exists()) {
            throw TaskOperationException::hasSubmissions($task);
        }

        DB::transaction(function () use ($task, $actor) {
            $task->logActivity(TaskActivityAction::DELETED, $actor);
            $task->delete();
        });
    }

    /** Stamp a task out of a template, checklist and all. */
    public function createFromTemplate(TaskTemplate $template, \DateTimeInterface $startDate, User $actor, array $overrides = []): Task
    {
        return DB::transaction(function () use ($template, $startDate, $actor, $overrides) {
            $task = Task::create(
                $template->toTaskAttributes($startDate) + $overrides + ['created_by' => $actor->id]
            );

            foreach ($template->items as $item) {
                $task->checklistItems()->create($item->toChecklistAttributes());
            }

            return $task;
        });
    }

    /**
     * @param  array<int, array<string, mixed>>  $checklist
     */
    private function syncChecklist(Task $task, array $checklist, bool $replace = false): void
    {
        if ($replace) {
            // Replaced wholesale rather than diffed: an item's identity is its
            // text, and a "changed" item is a different instruction. Completions
            // cascade away with it, which is correct — a tick against wording
            // nobody can read any more proves nothing.
            $task->checklistItems()->delete();
        }

        foreach (array_values($checklist) as $position => $item) {
            $task->checklistItems()->create([
                'title' => $item['title'],
                'description' => $item['description'] ?? null,
                'is_required' => $item['is_required'] ?? true,
                'requires_photo' => $item['requires_photo'] ?? false,
                'position' => $position,
            ]);
        }
    }
}
