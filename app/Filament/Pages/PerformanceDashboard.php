<?php

namespace App\Filament\Pages;

use App\Services\Analytics\AnalyticsService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;

/**
 * The analytics screen.
 *
 * Every figure comes from {@see AnalyticsService}, which also feeds the exported
 * report — so a number printed in a PDF and the same number on screen cannot
 * disagree. That is the entire reason the queries do not live in this page.
 *
 * Computed once per render into `$data` rather than called per section: the
 * overview runs a dozen aggregates, and a Blade template that invoked the
 * service from six places would run them six times.
 */
class PerformanceDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = 'Analytics';

    protected static string|\UnitEnum|null $navigationGroup = 'Field Operations';

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'Performance & Analytics';

    protected string $view = 'filament.pages.performance-dashboard';

    /** @var array<string, mixed> */
    public array $data = [];

    /**
     * Gated on the activity log rather than on tasks.
     *
     * Analytics aggregates what everyone did — it is closer to reading the audit
     * trail than to reading a task, and a Viewer who may see one task should not
     * thereby see a league table of every volunteer's performance.
     */
    public static function canAccess(): bool
    {
        return auth()->user()?->can('view_any_task_activity_log') ?? false;
    }

    public function mount(AnalyticsService $analytics): void
    {
        $this->data = $analytics->overview();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->authorize('export_task')
                ->url(route('admin.analytics.export.excel'), shouldOpenInNewTab: true),

            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->authorize('export_task')
                ->url(route('admin.analytics.export.pdf'), shouldOpenInNewTab: true),
        ];
    }

    /**
     * Hours rendered the way a person would say them.
     *
     * "68.4 hours" is arithmetic; "2.9 days" is an answer. Below a day the hour
     * figure is the clearer of the two, so the switch happens at 24.
     */
    public function readableHours(?float $hours): string
    {
        if ($hours === null) {
            return '—';
        }

        if ($hours < 1) {
            return round($hours * 60).' min';
        }

        return $hours < 24
            ? round($hours, 1).' hrs'
            : round($hours / 24, 1).' days';
    }

    /**
     * Heat-map cell intensity, 0–4.
     *
     * Bucketed rather than a continuous opacity: five discrete steps are
     * distinguishable at a glance, where a smooth gradient across 200 cells is
     * not — the eye cannot rank two shades that differ by 3%.
     */
    public function intensity(int $value, int $peak): int
    {
        if ($value === 0 || $peak === 0) {
            return 0;
        }

        return max(1, (int) ceil(($value / $peak) * 4));
    }
}
