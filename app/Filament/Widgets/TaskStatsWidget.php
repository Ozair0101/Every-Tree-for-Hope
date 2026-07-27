<?php

namespace App\Filament\Widgets;

use App\Enums\TaskStatus;
use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

/**
 * The five numbers a coordinator checks first.
 *
 * All of them come from one grouped query rather than five COUNTs. On a
 * dashboard that also renders three charts and two tables, the difference
 * between one query and five is the difference between a page that opens and
 * one that feels slow.
 */
class TaskStatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        return auth()->user()?->can('view_any_task') ?? false;
    }

    protected function getStats(): array
    {
        $counts = $this->counts();

        $total = array_sum($counts);
        $completed = $counts[TaskStatus::APPROVED->value] ?? 0;
        $rejected = $counts[TaskStatus::REJECTED->value] ?? 0;

        // "Pending" as a coordinator means it — anything live and unfinished,
        // not the literal Draft status. Draft is excluded: nobody is waiting on
        // work that has not been handed out yet.
        $pending = ($counts[TaskStatus::ASSIGNED->value] ?? 0)
            + ($counts[TaskStatus::IN_PROGRESS->value] ?? 0)
            + ($counts[TaskStatus::SUBMITTED->value] ?? 0)
            + ($counts[TaskStatus::UNDER_REVIEW->value] ?? 0);

        $overdue = Task::query()->overdue()->count();

        $completionRate = $total > 0 ? round($completed / $total * 100) : 0;

        return [
            Stat::make('Total tasks', number_format($total))
                ->description('Across every status')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary')
                ->chart($this->weeklyTrend()),

            Stat::make('Completed', number_format($completed))
                ->description("{$completionRate}% of all tasks")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Pending', number_format($pending))
                ->description('Assigned, running or awaiting review')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pending > 0 ? 'info' : 'gray'),

            Stat::make('Rejected', number_format($rejected))
                ->description('Sent back to the volunteer')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($rejected > 0 ? 'warning' : 'gray'),

            Stat::make('Overdue', number_format($overdue))
                ->description($overdue > 0 ? 'Past the deadline — chase these' : 'Nothing is late')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($overdue > 0 ? 'danger' : 'success'),
        ];
    }

    /** @return array<string, int> status => count */
    private function counts(): array
    {
        return Task::query()
            ->select('status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->map(fn ($value) => (int) $value)
            ->all();
    }

    /**
     * Tasks created per day over the last week, for the sparkline.
     *
     * Zero-filled: a gap day would otherwise be drawn as a straight line
     * between its neighbours, implying activity that did not happen.
     *
     * @return array<int, int>
     */
    private function weeklyTrend(): array
    {
        $rows = Task::query()
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as aggregate'))
            ->groupBy('day')
            ->pluck('aggregate', 'day');

        $trend = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $trend[] = (int) ($rows[$day] ?? 0);
        }

        return $trend;
    }
}
