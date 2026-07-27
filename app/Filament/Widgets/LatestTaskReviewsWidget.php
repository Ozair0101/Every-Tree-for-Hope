<?php

namespace App\Filament\Widgets;

use App\Enums\TaskReviewStatus;
use App\Models\TaskReview;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

/**
 * The most recent verdicts, with their scores.
 *
 * Separate from the activity feed because it answers a different question: not
 * "what happened" but "what standard is being applied, and by whom". A run of
 * low scores from one reviewer, or a run of rejections on one category, is
 * visible here and invisible in a chronological feed.
 */
class LatestTaskReviewsWidget extends BaseWidget
{
    protected static ?int $sort = 9;

    protected static ?string $heading = 'Latest reviews';

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        return auth()->user()?->can('review_task') || auth()->user()?->can('view_any_task');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TaskReview::query()
                    ->with([
                        'task:id,uuid,reference,title',
                        'reviewer:id,name,lastname',
                        'assignment.user:id,name,lastname',
                    ])
                    ->orderByDesc('reviewed_at')
                    ->limit(15)
            )
            ->columns([
                Tables\Columns\TextColumn::make('task.title')
                    ->label('Task')
                    ->limit(35)
                    ->description(fn (TaskReview $r) => $r->task?->reference)
                    ->url(fn (TaskReview $r) => $r->task_id
                        ? route('filament.admin.resources.tasks.edit', ['record' => $r->task_id])
                        : null),
                Tables\Columns\TextColumn::make('assignment.user.name')
                    ->label('Volunteer')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('review_status')
                    ->label('Verdict')
                    ->badge()
                    ->formatStateUsing(fn (TaskReviewStatus $state) => $state->label())
                    ->color(fn (TaskReviewStatus $state) => match ($state) {
                        TaskReviewStatus::APPROVED => 'success',
                        TaskReviewStatus::REJECTED => 'danger',
                        TaskReviewStatus::NEEDS_REVISION => 'warning',
                    }),
                Tables\Columns\TextColumn::make('score')
                    ->placeholder('—')
                    ->formatStateUsing(fn ($state) => $state === null ? '—' : rtrim(rtrim((string) $state, '0'), '.').'/100'),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->placeholder('—')
                    // Stars read faster than a number on a dashboard skimmed
                    // rather than studied.
                    ->formatStateUsing(fn ($state) => $state === null
                        ? '—'
                        : str_repeat('★', (int) $state).str_repeat('☆', 5 - (int) $state)),
                Tables\Columns\TextColumn::make('comments')
                    ->limit(45)
                    ->wrap()
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('reviewer.name')
                    ->label('Reviewed by')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('When')
                    ->since(),
            ])
            ->paginated(false);
    }
}
