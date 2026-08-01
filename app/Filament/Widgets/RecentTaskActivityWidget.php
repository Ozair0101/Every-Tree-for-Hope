<?php

namespace App\Filament\Widgets;

use App\Models\TaskActivityLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

/**
 * The live feed: everything anyone did, newest first.
 *
 * Reads `task_activity_logs`, which already stores a composed sentence per
 * entry — so the feed shows what was true when it happened, even if the task
 * has since been renamed or the user deleted.
 */
class RecentTaskActivityWidget extends BaseWidget
{
    protected static ?int $sort = 8;

    protected static ?string $heading = 'Recent task activity';

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        return auth()->user()?->can('view_any_task_activity_log') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TaskActivityLog::query()
                    // Only the columns the feed renders. The `meta` JSON and the
                    // description are the heavy parts; meta is not shown here.
                    ->with(['user:id,name,lastname', 'task:id,uuid,reference,title'])
                    ->latest()
                    ->limit(25)
            )
            ->columns([
                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->icon(fn ($state) => $state->icon())
                    ->color(fn ($state) => match (true) {
                        str_contains($state->value, 'approved') => 'success',
                        str_contains($state->value, 'rejected') => 'danger',
                        str_contains($state->value, 'cancelled') => 'gray',
                        default => 'info',
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('What happened')
                    ->wrap()
                    ->limit(90),
                Tables\Columns\TextColumn::make('task.reference')
                    ->label('Task')
                    ->placeholder('—')
                    ->url(fn (TaskActivityLog $r) => $r->task_id
                        ? route('filament.admin.resources.tasks.edit', ['record' => $r->task_id])
                        : null),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('By')
                    ->placeholder('System'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('When')
                    ->since()
                    ->tooltip(fn (TaskActivityLog $r) => $r->created_at?->format('d M Y, H:i')),
            ])
            ->paginated(false);
    }
}
