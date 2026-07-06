<?php

namespace App\Filament\Pages;

use App\Models\Donator;
use App\Models\Event;
use App\Models\ContactMessage;
use App\Models\User;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\Widget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

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
            \App\Filament\Widgets\OverviewStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Widgets\RecentActivityWidget::class,
        ];
    }
}
