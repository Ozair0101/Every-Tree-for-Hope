<?php

namespace App\Filament\Widgets;

use App\Enums\TaskStatus;
use App\Models\Task;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

/**
 * Created versus completed, month by month.
 *
 * The one chart that answers "are we keeping up?". Two lines that track each
 * other mean throughput matches intake; a widening gap means the backlog is
 * growing, which no single number on the stats row would reveal.
 */
class TaskCompletionTrendChart extends ChartWidget
{
    protected static ?int $sort = 7;

    protected ?string $heading = 'Created vs completed (last 6 months)';

    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 2;

    public static function canView(): bool
    {
        return auth()->user()?->can('view_any_task') ?? false;
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $since = now()->subMonths(5)->startOfMonth();

        $created = $this->monthly('created_at', $since);
        // Completion is keyed on completed_at, not created_at: a task created
        // in March and finished in May belongs to May's throughput, and using
        // created_at would credit the wrong month entirely.
        $completed = $this->monthly('completed_at', $since, fn ($q) => $q
            ->where('status', TaskStatus::APPROVED->value));

        $labels = [];
        $createdSeries = [];
        $completedSeries = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $key = $month->format('Y-m');

            $labels[] = $month->format('M Y');
            $createdSeries[] = (int) ($created[$key] ?? 0);
            $completedSeries[] = (int) ($completed[$key] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Created',
                    'data' => $createdSeries,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Completed',
                    'data' => $completedSeries,
                    'borderColor' => '#059669',
                    'backgroundColor' => 'rgba(5, 150, 105, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    /** Monthly counts keyed Y-m. */
    private function monthly(string $column, $since, ?callable $constrain = null): \Illuminate\Support\Collection
    {
        $query = Task::query()
            ->whereNotNull($column)
            ->where($column, '>=', $since)
            ->select(DB::raw($this->monthExpression($column).' as period'), DB::raw('COUNT(*) as aggregate'))
            ->groupBy('period');

        if ($constrain) {
            $constrain($query);
        }

        return $query->pluck('aggregate', 'period');
    }

    /**
     * Truncate a timestamp to `YYYY-MM`, in the current driver's dialect.
     *
     * There is no portable way to do this in SQL. `DATE_FORMAT` is MySQL-only
     * and this widget was originally written with it — which worked in
     * production and failed outright under the SQLite the test suite runs on,
     * so the chart was effectively untestable. Naming each dialect here keeps
     * the whole thing one indexed query while still being runnable everywhere.
     *
     * `$column` is never caller-supplied — both call sites pass a literal — so
     * interpolating it carries no injection risk.
     */
    private function monthExpression(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', {$column})",
            'pgsql' => "to_char({$column}, 'YYYY-MM')",
            'sqlsrv' => "FORMAT({$column}, 'yyyy-MM')",
            default => "DATE_FORMAT({$column}, '%Y-%m')",
        };
    }
}
