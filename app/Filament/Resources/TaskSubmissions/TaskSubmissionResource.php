<?php

namespace App\Filament\Resources\TaskSubmissions;

use App\Enums\TaskSubmissionStatus;
use App\Filament\Resources\TaskSubmissions\Pages\ListTaskSubmissions;
use App\Filament\Resources\TaskSubmissions\Pages\ReviewSubmission;
use App\Models\TaskSubmission;
use BackedEnum;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * The review queue.
 *
 * A separate resource from TaskResource because a reviewer's job is not to
 * browse tasks — it is to work through submitted evidence, oldest first, and
 * clear it. Making them find the task, open it, find the assignment and then
 * find the submission would put three navigations between them and the thing
 * they came to do.
 *
 * Read-only by design: there is no create or edit. A submission is a record of
 * what a volunteer turned in, and an admin editing it would destroy the very
 * evidence the review exists to judge. The only write is a verdict, which is a
 * new row in `task_reviews`.
 */
class TaskSubmissionResource extends Resource
{
    protected static ?string $model = TaskSubmission::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $navigationLabel = 'Review Queue';

    protected static string|\UnitEnum|null $navigationGroup = 'Field Operations';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Submission';

    /** Reviewing is its own permission — see TaskPolicy::review(). */
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('review_task') || auth()->user()?->can('view_any_task');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    /** How much work is waiting. Warning-coloured because a queue that ages is the failure mode. */
    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::query()
            ->where('status', TaskSubmissionStatus::PENDING->value)
            ->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('task.reference')
                    ->label('Ref')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('task.title')
                    ->label('Task')
                    ->searchable()
                    ->limit(38)
                    ->wrap()
                    ->description(fn (TaskSubmission $r) => 'Attempt '.$r->attempt),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Volunteer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (TaskSubmissionStatus $state) => $state->label())
                    ->color(fn (TaskSubmissionStatus $state) => match ($state) {
                        TaskSubmissionStatus::APPROVED => 'success',
                        TaskSubmissionStatus::REJECTED => 'danger',
                        TaskSubmissionStatus::NEEDS_REVISION => 'warning',
                        TaskSubmissionStatus::PENDING => 'info',
                    }),
                Tables\Columns\IconColumn::make('is_within_geofence')
                    ->label('On site')
                    // Three states, not two. Null means the task had no geofence
                    // at all, which is not the same as "was not there" — showing
                    // both as a red cross would accuse honest volunteers.
                    ->icon(fn ($state) => match ($state) {
                        true => 'heroicon-o-check-circle',
                        false => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-minus-circle',
                    })
                    ->color(fn ($state) => match ($state) {
                        true => 'success',
                        false => 'danger',
                        default => 'gray',
                    })
                    ->tooltip(fn (TaskSubmission $r) => $r->is_within_geofence === null
                        ? 'No geofence on this task'
                        : ($r->is_within_geofence
                            ? 'Submitted on site'
                            : "Submitted {$r->distance_meters}m away")),
                Tables\Columns\TextColumn::make('attachments_count')
                    ->counts('attachments')
                    ->label('Files')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->since()
                    ->sortable()
                    ->tooltip(fn (TaskSubmission $r) => $r->created_at?->format('d M Y, H:i')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(TaskSubmissionStatus::options())
                    ->default(TaskSubmissionStatus::PENDING->value),
                Tables\Filters\Filter::make('off_site')
                    ->label('Submitted off site')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->where('is_within_geofence', false)),
            ])
            ->recordActions([
                Actions\Action::make('review')
                    ->label('Review')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('primary')
                    ->url(fn (TaskSubmission $r) => ReviewSubmission::getUrl(['record' => $r])),
            ])
            // Oldest first: a review queue sorted newest-first quietly starves
            // the submissions that have been waiting longest.
            ->defaultSort('created_at', 'asc')
            ->persistFiltersInSession()
            ->striped();
    }

    /** Eager-load what the table renders — otherwise it is three queries per row. */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['task:id,uuid,reference,title,status', 'user:id,name,lastname']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTaskSubmissions::route('/'),
            'review' => ReviewSubmission::route('/{record}/review'),
        ];
    }
}
