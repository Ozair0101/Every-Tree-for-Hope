<?php

namespace App\Filament\Resources\Tasks\Pages;

use App\Enums\TaskStatus;
use App\Filament\Resources\Tasks\TaskResource;
use App\Filament\Widgets\TaskStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
// Filament v5 moved Tab into the schemas package; the v3 path
// (Filament\Resources\Components\Tab) no longer exists.
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }

    /** The stats strip sits above the board, scoped by nothing — it is the whole picture. */
    protected function getHeaderWidgets(): array
    {
        return [TaskStatsWidget::class];
    }

    /**
     * Tabs for the states a coordinator switches between all day.
     *
     * Deliberately not one tab per status: eight tabs is a menu, not a
     * shortcut. These four are the questions actually asked — what needs my
     * sign-off, what is running, what is late, what is done.
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),

            'review' => Tab::make('Awaiting review')
                ->modifyQueryUsing(fn (Builder $query) => $query->awaitingReview())
                ->badge(fn () => TaskResource::getModel()::query()->awaitingReview()->count())
                ->badgeColor('warning'),

            'open' => Tab::make('In progress')
                ->modifyQueryUsing(fn (Builder $query) => $query->open())
                ->badge(fn () => TaskResource::getModel()::query()->open()->count()),

            'overdue' => Tab::make('Overdue')
                ->modifyQueryUsing(fn (Builder $query) => $query->overdue())
                ->badge(fn () => TaskResource::getModel()::query()->overdue()->count())
                ->badgeColor('danger'),

            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', TaskStatus::APPROVED->value)),
        ];
    }
}
