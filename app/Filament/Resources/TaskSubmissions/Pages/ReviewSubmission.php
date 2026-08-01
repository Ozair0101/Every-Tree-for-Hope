<?php

namespace App\Filament\Resources\TaskSubmissions\Pages;

use App\Enums\TaskReviewStatus;
use App\Filament\Resources\TaskSubmissions\TaskSubmissionResource;
use App\Models\TaskProgress;
use App\Models\TaskSubmission;
use App\Services\Tasks\TaskCommentService;
use App\Services\Tasks\TaskReviewService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;

/**
 * The review workspace: one submission, everything about it, and the verdict.
 *
 * Built as a custom page rather than a Filament infolist because the reviewer
 * needs to look at photographs and a map alongside a form, and judge them
 * together. An infolist would stack those into a column and put the evidence
 * off-screen from the decision — which is precisely when reviews get rubber
 * stamped.
 *
 * Everything the requirement lists is loaded in one pass in mount(): the task,
 * the volunteer, the GPS reading, the assignment timeline, every attachment
 * split by kind, the progress history and the discussion.
 */
class ReviewSubmission extends Page
{
    use InteractsWithRecord;

    protected static string $resource = TaskSubmissionResource::class;

    protected string $view = 'filament.pages.review-submission';

    public function getTitle(): string
    {
        return "Review · {$this->record->task?->reference}";
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        // Authorised against the *task*, not the submission: TaskPolicy::review()
        // knows that a named reviewer may review without holding the global
        // permission, and that nobody reviews their own work.
        abort_unless(auth()->user()?->can('review', $this->record->task), 403);

        // Loaded here rather than lazily in the view, where each `@foreach`
        // would become its own query on a page that renders six collections.
        $this->record->load([
            'task.category',
            'task.creator',
            'user',
            'assignment.user',
            'assignment.assigner',
            'attachments.uploader',
            'reviews.reviewer',
        ]);
    }

    /* ══════════════ Evidence, for the view ══════════════ */

    /** @return Collection<int, \App\Models\TaskAttachment> */
    public function attachmentsOfKind(string $kind): Collection
    {
        return $this->record->attachments->filter(fn ($file) => $file->file_type->value === $kind)->values();
    }

    /** Every progress report this volunteer filed, newest first. */
    public function progressHistory(): Collection
    {
        return TaskProgress::query()
            ->where('task_assignment_id', $this->record->task_assignment_id)
            ->with('creator:id,name,lastname')
            ->latestFirst()
            ->get();
    }

    /**
     * The discussion.
     *
     * Internal notes are included — this is the staff side, and hiding them
     * from the person deciding the outcome would defeat their purpose. The API
     * strips them for volunteers; see TaskCommentResource on that side.
     */
    public function comments(): Collection
    {
        return $this->record->task
            ->comments()
            ->with('user:id,name,lastname')
            ->get();
    }

    /** Earlier attempts, so a reviewer can see what changed. */
    public function earlierAttempts(): Collection
    {
        return TaskSubmission::query()
            ->where('task_assignment_id', $this->record->task_assignment_id)
            ->where('attempt', '<', $this->record->attempt)
            ->with(['attachments', 'reviews.reviewer:id,name'])
            ->orderByDesc('attempt')
            ->get();
    }

    /* ══════════════ Verdicts ══════════════ */

    protected function getHeaderActions(): array
    {
        return [
            $this->verdictAction(
                'approve',
                TaskReviewStatus::APPROVED,
                'Approve',
                'heroicon-o-check-badge',
                'success',
            ),
            $this->verdictAction(
                'request_revision',
                TaskReviewStatus::NEEDS_REVISION,
                'Needs revision',
                'heroicon-o-arrow-path',
                'warning',
            ),
            $this->verdictAction(
                'reject',
                TaskReviewStatus::REJECTED,
                'Reject',
                'heroicon-o-x-circle',
                'danger',
            ),
            $this->commentAction(),
        ];
    }

    /**
     * One builder for all three verdicts.
     *
     * They differ only in wording and in whether the comment is compulsory —
     * writing them out three times would be three places for the scoring rules
     * to drift apart.
     */
    private function verdictAction(
        string $name,
        TaskReviewStatus $status,
        string $label,
        string $icon,
        string $colour,
    ): Action {
        $explanationRequired = $status !== TaskReviewStatus::APPROVED;

        return Action::make($name)
            ->label($label)
            ->icon($icon)
            ->color($colour)
            ->visible(fn () => $this->record->status->value === 'pending')
            ->schema([
                TextInput::make('score')
                    ->label('Score')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('/ 100')
                    ->helperText('Objective marks — for example 38 of 40 saplings alive.'),
                TextInput::make('rating')
                    ->label('Rating')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->suffix('★')
                    ->helperText('How well the work was done, 1 to 5.'),
                Textarea::make('comments')
                    ->label($explanationRequired ? 'What needs fixing' : 'Comment (optional)')
                    ->rows(4)
                    ->maxLength(5000)
                    // A rejection with no reason is the single most common
                    // complaint volunteers have: they cannot fix what nobody
                    // told them was wrong.
                    ->required($explanationRequired)
                    ->helperText($explanationRequired
                        ? 'The volunteer sees this. Be specific enough to act on.'
                        : 'Sent to the volunteer with the approval.'),
            ])
            ->modalHeading("{$label} — {$this->record->task?->reference}")
            ->modalSubmitActionLabel($label)
            ->action(function (array $data) use ($status) {
                app(TaskReviewService::class)->record(
                    assignment: $this->record->assignment,
                    status: $status,
                    reviewer: auth()->user(),
                    score: $data['score'] !== null && $data['score'] !== '' ? (float) $data['score'] : null,
                    rating: $data['rating'] !== null && $data['rating'] !== '' ? (int) $data['rating'] : null,
                    comments: $data['comments'] ?? null,
                );

                Notification::make()
                    ->title("Review recorded: {$status->label()}")
                    ->body('The volunteer has been notified.')
                    ->success()
                    ->send();

                // Straight back to the queue: a reviewer clearing a backlog
                // wants the next item, not the one they just finished.
                $this->redirect(TaskSubmissionResource::getUrl('index'));
            });
    }

    private function commentAction(): Action
    {
        return Action::make('comment')
            ->label('Comment')
            ->icon('heroicon-o-chat-bubble-left')
            ->color('gray')
            ->schema([
                Textarea::make('body')
                    ->label('Message')
                    ->rows(4)
                    ->required()
                    ->maxLength(5000),
                Toggle::make('internal')
                    ->label('Staff only')
                    ->helperText('Keeps this off the volunteer’s screen and sends no notification.')
                    ->default(false),
            ])
            ->action(function (array $data) {
                app(TaskCommentService::class)->post(
                    task: $this->record->task,
                    author: auth()->user(),
                    body: $data['body'],
                    internal: (bool) ($data['internal'] ?? false),
                );

                Notification::make()->title('Comment posted')->success()->send();
            });
    }
}
