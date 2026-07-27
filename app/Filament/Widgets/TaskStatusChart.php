<?php

namespace App\Filament\Widgets;

use App\Enums\TaskStatus;
use App\Models\Task;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

/**
 * Where the work currently sits, as a doughnut.
 *
 * A shape question rather than a trend question: is the board clogged at
 * review, or is nothing being started? Colours come from the enum, so a chart
 * segment and a table badge for the same status can never disagree.
 */
class TaskStatusChart extends ChartWidget
{
    protected static ?int $sort = 6;

    protected ?string $heading = 'Tasks by status';

    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        return auth()->user()?->can('view_any_task') ?? false;
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $counts = Task::query()
            ->select('status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $labels = [];
        $values = [];
        $colors = [];

        foreach (TaskStatus::cases() as $status) {
            $count = (int) ($counts[$status->value] ?? 0);

            // Empty statuses are dropped rather than drawn as zero slices — a
            // legend of eight entries where five are invisible is noise.
            if ($count === 0) {
                continue;
            }

            $labels[] = $status->label();
            $values[] = $count;
            $colors[] = $status->color();
        }

        return [
            'datasets' => [[
                'label' => 'Tasks',
                'data' => $values,
                'backgroundColor' => $colors,
                'borderWidth' => 0,
            ]],
            'labels' => $labels,
        ];
    }
}
