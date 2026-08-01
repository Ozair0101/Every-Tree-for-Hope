<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    /**
     * Gate the dashboard route + its navigation item behind `view_dashboard`.
     * Filament calls this for both URL access and menu visibility.
     */
    public static function canAccess(): bool
    {
        return auth()->user()?->can('view_dashboard') ?? false;
    }

    public function getWidgets(): array
    {
        return [
            // Field operations first: the task board is what the office acts on
            // day to day, where the donation figures are reported on monthly.
            // Each widget gates itself with canView(), so a Viewer or a
            // finance-only account simply does not render the ones it may not
            // see — and never runs their queries either.
            \App\Filament\Widgets\TaskStatsWidget::class,
            \App\Filament\Widgets\TaskCompletionTrendChart::class,
            \App\Filament\Widgets\TaskStatusChart::class,
            \App\Filament\Widgets\RecentTaskActivityWidget::class,
            \App\Filament\Widgets\LatestTaskReviewsWidget::class,

            \App\Filament\Widgets\OverviewStatsWidget::class,
            \App\Filament\Widgets\RecentDonationsChart::class,
            \App\Filament\Widgets\TreePlantingProgressWidget::class,
            \App\Filament\Widgets\RecentActivityWidget::class,
            \App\Filament\Widgets\TopDonatorsWidget::class,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\TaskStatsWidget::class,
            \App\Filament\Widgets\OverviewStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Widgets\RecentTaskActivityWidget::class,
            \App\Filament\Widgets\LatestTaskReviewsWidget::class,
            \App\Filament\Widgets\RecentActivityWidget::class,
        ];
    }
}
