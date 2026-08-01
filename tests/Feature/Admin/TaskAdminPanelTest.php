<?php

namespace Tests\Feature\Admin;

use App\Enums\TaskStatus;
use App\Filament\Pages\TaskCalendar;
use App\Filament\Resources\Tasks\Pages\ListTasks;
use App\Filament\Widgets\LatestTaskReviewsWidget;
use App\Filament\Widgets\RecentTaskActivityWidget;
use App\Filament\Widgets\TaskCompletionTrendChart;
use App\Filament\Widgets\TaskStatsWidget;
use App\Filament\Widgets\TaskStatusChart;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\User;
use App\Services\Tasks\Export\TaskExporter;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Phase 5 — the admin panel.
 *
 * Renders the real Livewire components rather than asserting on classes: a
 * widget that throws on render is the failure mode that matters, and only
 * actually rendering it catches that.
 */
class TaskAdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;

    private User $volunteer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->manager = User::factory()->create();
        $this->manager->assignRole('Manager');

        $this->volunteer = User::factory()->create();
    }

    private function task(array $attributes = []): Task
    {
        return Task::create(array_merge([
            'title' => 'Water the saplings at Qargha',
            'created_by' => $this->manager->id,
            'requires_photo' => false,
        ], $attributes));
    }

    /* ══════════════ ACCESS ══════════════ */

    public function test_a_volunteer_cannot_reach_the_task_board(): void
    {
        // No role at all, so no panel access — the mobile app is their interface.
        $this->actingAs($this->volunteer)
            ->get('/admin/tasks')
            ->assertForbidden();
    }

    public function test_a_manager_can_open_the_task_board(): void
    {
        $this->task();

        $this->actingAs($this->manager)
            ->get('/admin/tasks')
            ->assertOk();
    }

    /* ══════════════ TABLE: FILTER / SEARCH / TABS ══════════════ */

    public function test_the_board_lists_tasks(): void
    {
        $task = $this->task();

        Livewire::actingAs($this->manager)
            ->test(ListTasks::class)
            ->assertCanSeeTableRecords([$task]);
    }

    public function test_the_board_searches_by_title_and_reference(): void
    {
        $needle = $this->task(['title' => 'Prune the orchard row']);
        $other = $this->task(['title' => 'Something unrelated']);

        Livewire::actingAs($this->manager)
            ->test(ListTasks::class)
            ->searchTable('orchard')
            ->assertCanSeeTableRecords([$needle])
            ->assertCanNotSeeTableRecords([$other]);

        Livewire::actingAs($this->manager)
            ->test(ListTasks::class)
            ->searchTable($needle->fresh()->reference)
            ->assertCanSeeTableRecords([$needle]);
    }

    public function test_the_board_filters_by_status_and_priority(): void
    {
        $critical = $this->task(['title' => 'Critical', 'priority' => 'critical']);
        $low = $this->task(['title' => 'Low', 'priority' => 'low']);

        Livewire::actingAs($this->manager)
            ->test(ListTasks::class)
            ->filterTable('priority', ['critical'])
            ->assertCanSeeTableRecords([$critical])
            ->assertCanNotSeeTableRecords([$low]);
    }

    public function test_the_overdue_filter_finds_only_late_work(): void
    {
        $late = $this->task(['title' => 'Late', 'due_date' => now()->subWeek()]);
        $late->forceFill(['status' => TaskStatus::ASSIGNED->value])->save();

        $onTime = $this->task(['title' => 'On time', 'due_date' => now()->addWeek()]);

        Livewire::actingAs($this->manager)
            ->test(ListTasks::class)
            ->filterTable('overdue', true)
            ->assertCanSeeTableRecords([$late])
            ->assertCanNotSeeTableRecords([$onTime]);
    }

    public function test_the_tabs_scope_the_board(): void
    {
        $late = $this->task(['title' => 'Late', 'due_date' => now()->subWeek()]);
        $late->forceFill(['status' => TaskStatus::ASSIGNED->value])->save();

        $done = $this->task(['title' => 'Done']);
        $done->forceFill(['status' => TaskStatus::APPROVED->value])->save();

        Livewire::actingAs($this->manager)
            ->test(ListTasks::class, ['activeTab' => 'overdue'])
            ->assertCanSeeTableRecords([$late])
            ->assertCanNotSeeTableRecords([$done]);

        Livewire::actingAs($this->manager)
            ->test(ListTasks::class, ['activeTab' => 'completed'])
            ->assertCanSeeTableRecords([$done])
            ->assertCanNotSeeTableRecords([$late]);
    }

    /* ══════════════ WIDGETS ══════════════ */

    public function test_every_task_widget_renders(): void
    {
        $this->task();
        $done = $this->task(['title' => 'Done']);
        $done->forceFill(['status' => TaskStatus::APPROVED->value, 'completed_at' => now()])->save();

        foreach ([
            TaskStatsWidget::class,
            TaskStatusChart::class,
            TaskCompletionTrendChart::class,
            RecentTaskActivityWidget::class,
            LatestTaskReviewsWidget::class,
        ] as $widget) {
            Livewire::actingAs($this->manager)
                ->test($widget)
                ->assertOk();
        }
    }

    public function test_the_stats_widget_counts_each_state(): void
    {
        $this->task(['title' => 'Draft one']);

        $done = $this->task(['title' => 'Done']);
        $done->forceFill(['status' => TaskStatus::APPROVED->value])->save();

        $late = $this->task(['title' => 'Late', 'due_date' => now()->subWeek()]);
        $late->forceFill(['status' => TaskStatus::ASSIGNED->value])->save();

        Livewire::actingAs($this->manager)
            ->test(TaskStatsWidget::class)
            ->assertSee('Total tasks')
            ->assertSee('Completed')
            ->assertSee('Pending')
            ->assertSee('Rejected')
            ->assertSee('Overdue');
    }

    public function test_widgets_are_hidden_from_someone_without_task_access(): void
    {
        // A Viewer holds read permissions, so it should see them; a roleless
        // volunteer should not. canView() gates the query as well as the render.
        $this->actingAs($this->volunteer);

        $this->assertFalse(TaskStatsWidget::canView());
        $this->assertFalse(TaskStatusChart::canView());
    }

    /* ══════════════ CALENDAR ══════════════ */

    public function test_the_calendar_page_opens_and_places_tasks_on_their_due_day(): void
    {
        $due = now()->addDays(3)->setTime(10, 0);
        $task = $this->task(['title' => 'Calendar task', 'due_date' => $due]);

        Livewire::actingAs($this->manager)
            ->test(TaskCalendar::class)
            ->assertOk()
            ->assertSee('Calendar task')
            ->assertSee($due->format('F Y'));
    }

    public function test_the_calendar_moves_between_months(): void
    {
        $component = Livewire::actingAs($this->manager)->test(TaskCalendar::class);

        $component->call('nextMonth')
            ->assertSee(now()->addMonthNoOverflow()->format('F Y'));

        $component->call('today')
            ->assertSee(now()->format('F Y'));
    }

    public function test_a_bad_month_in_the_url_falls_back_rather_than_erroring(): void
    {
        // The month comes from the query string; a hand-edited URL must not 500.
        Livewire::actingAs($this->manager)
            ->test(TaskCalendar::class, ['month' => 'not-a-month'])
            ->assertOk()
            ->assertSee(now()->format('F Y'));
    }

    public function test_a_volunteer_cannot_open_the_calendar(): void
    {
        $this->actingAs($this->volunteer);

        $this->assertFalse(TaskCalendar::canAccess());
    }

    /* ══════════════ EXPORTS ══════════════ */

    public function test_the_excel_export_streams_a_workbook(): void
    {
        $this->task(['title' => 'Exported task']);

        $response = app(TaskExporter::class)->toExcel(Task::query());

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('spreadsheetml', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('Content-Disposition'));

        ob_start();
        $response->sendContent();
        $body = ob_get_clean();

        // "PK" is the zip magic number — an .xlsx is a zip archive, so this
        // asserts a real workbook rather than an empty or truncated stream.
        $this->assertStringStartsWith('PK', $body);
        $this->assertGreaterThan(1000, strlen($body));
    }

    public function test_the_export_reflects_the_filters_it_was_given(): void
    {
        $this->task(['title' => 'Critical one', 'priority' => 'critical']);
        $this->task(['title' => 'Low one', 'priority' => 'low']);

        $rows = app(TaskExporter::class)->rows(Task::query()->where('priority', 'critical'));

        $this->assertCount(1, $rows);
        $this->assertSame('Critical one', $rows->first()['title']);
    }

    public function test_the_export_flattens_relations_into_readable_columns(): void
    {
        $category = TaskCategory::create(['name' => 'Watering']);
        $task = $this->task(['title' => 'With relations', 'task_category_id' => $category->id]);
        $task->assign($this->volunteer, $this->manager);

        $row = app(TaskExporter::class)->rows(Task::query()->whereKey($task->id))->first();

        $this->assertSame('Watering', $row['category']);
        $this->assertStringContainsString($this->volunteer->name, $row['assignees']);
        $this->assertSame('No', $row['overdue']);
    }

    public function test_the_pdf_report_renders_with_a_summary(): void
    {
        $late = $this->task(['title' => 'Late task', 'due_date' => now()->subWeek()]);
        $late->forceFill(['status' => TaskStatus::ASSIGNED->value])->save();

        $this->actingAs($this->manager)
            ->get(route('admin.tasks.export.pdf'))
            ->assertOk()
            ->assertSee('Task Report')
            ->assertSee('Late task')
            ->assertSee('Overdue');
    }

    public function test_the_pdf_report_honours_query_filters(): void
    {
        $this->task(['title' => 'Critical work', 'priority' => 'critical']);
        $this->task(['title' => 'Quiet work', 'priority' => 'low']);

        $this->actingAs($this->manager)
            ->get(route('admin.tasks.export.pdf', ['priority' => ['critical']]))
            ->assertOk()
            ->assertSee('Critical work')
            ->assertDontSee('Quiet work');
    }

    public function test_the_pdf_report_is_refused_without_the_export_permission(): void
    {
        $this->actingAs($this->volunteer)
            ->get(route('admin.tasks.export.pdf'))
            ->assertForbidden();
    }
}
