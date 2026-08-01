<?php

namespace Tests\Feature\Admin;

use App\Enums\TaskAssignmentRole;
use App\Enums\TaskAssignmentStatus;
use App\Enums\TaskReviewStatus;
use App\Enums\TaskStatus;
use App\Enums\TaskSubmissionStatus;
use App\Filament\Pages\PerformanceDashboard;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\TaskReview;
use App\Models\TaskSubmission;
use App\Models\Tree;
use App\Models\User;
use App\Services\Analytics\AnalyticsService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Phase 10 — analytics.
 *
 * These tests exist mostly to pin down *definitions*. Every metric here is one
 * a person will later quote in a grant report, and the expensive kind of bug is
 * not a crash but a number that is quietly measuring the wrong thing — throughput
 * keyed on the creation date, a completion rate above 100% because a join fanned
 * out, a ranking topped by someone who did one easy task.
 *
 * They also render the real page and hit the real export routes, because a
 * dashboard that throws on render is the other failure mode that matters.
 */
class AnalyticsTest extends TestCase
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

    private function analytics(): AnalyticsService
    {
        return app(AnalyticsService::class);
    }

    private function task(array $attributes = []): Task
    {
        return Task::create(array_merge([
            'title' => 'Plant saplings at Qargha',
            'created_by' => $this->manager->id,
            'requires_photo' => false,
            'requires_geo_check' => false,
        ], $attributes));
    }

    /**
     * A finished piece of work, with its clock set explicitly.
     *
     * Written directly rather than through the services: these tests are about
     * what the aggregates say, and driving the whole state machine to place a
     * completion in a specific month would obscure that.
     */
    private function completedAssignment(User $user, Task $task, array $clock = []): TaskAssignment
    {
        return TaskAssignment::create(array_merge([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'role' => TaskAssignmentRole::ASSIGNEE->value,
            'status' => TaskAssignmentStatus::APPROVED->value,
            'assigned_at' => now()->subDays(3),
            'started_at' => now()->subDays(2),
            'submitted_at' => now()->subDay(),
            'completed_at' => now(),
            'assigned_by' => $this->manager->id,
        ], $clock));
    }

    /**
     * A pending submission, timestamped where the test needs it.
     *
     * Every submission needs an assignment behind it — the schema enforces
     * that, because a submission with no assignment is a photo nobody asked
     * for. The attempt counter is stepped so the (assignment, attempt) unique
     * index does not collide when a test files two.
     */
    private function submission(Task $task, array $overrides = []): TaskSubmission
    {
        $assignment = $this->completedAssignment($this->volunteer, $task, [
            'status' => TaskAssignmentStatus::SUBMITTED->value,
            'completed_at' => null,
        ]);

        $submission = TaskSubmission::create(array_merge([
            'task_id' => $task->id,
            'task_assignment_id' => $assignment->id,
            'user_id' => $this->volunteer->id,
            'status' => TaskSubmissionStatus::PENDING->value,
        ], array_diff_key($overrides, ['created_at' => null])));

        if (isset($overrides['created_at'])) {
            $submission->forceFill(['created_at' => $overrides['created_at']])->saveQuietly();
        }

        return $submission;
    }

    /** A recorded tree. Coordinates and a planting date are required by the schema. */
    private function tree(array $overrides = []): Tree
    {
        return Tree::create(array_merge([
            'user_id' => $this->volunteer->id,
            'species' => 'Chinar',
            'image_path' => 'trees/'.uniqid().'.jpg',
            'latitude' => 34.55,
            'longitude' => 69.05,
            'planted_on' => now()->subMonth(),
            'status' => 'approved',
        ], $overrides));
    }

    /* ══════════════ THROUGHPUT ══════════════ */

    public function test_monthly_throughput_is_keyed_on_completion_not_creation(): void
    {
        // Raised three months ago, finished today. That is *this* month's
        // throughput — crediting it to the month it was raised would make a
        // busy month look idle and an idle one look busy.
        $old = $this->task(['status' => TaskStatus::APPROVED->value]);
        $old->forceFill([
            'created_at' => now()->subMonths(3),
            'completed_at' => now(),
        ])->saveQuietly();

        $monthly = $this->analytics()->monthlyCompleted();
        $thisMonth = $monthly->last();
        $threeAgo = $monthly->firstWhere('period', now()->subMonths(3)->format('Y-m'));

        $this->assertSame(1, $thisMonth['completed']);
        $this->assertSame(0, $thisMonth['created']);
        $this->assertSame(0, $threeAgo['completed']);
        $this->assertSame(1, $threeAgo['created']);
    }

    public function test_months_with_no_activity_are_still_returned(): void
    {
        // A chart that silently drops empty months compresses the timeline and
        // makes a gap in the work invisible.
        $monthly = $this->analytics()->monthlyCompleted(6);

        $this->assertCount(6, $monthly);
        $this->assertSame(0, $monthly->sum('completed'));
    }

    public function test_only_approved_tasks_count_as_completed(): void
    {
        // Submitted is not done. Counting it would report work as delivered
        // that a reviewer may yet reject.
        $this->task(['status' => TaskStatus::SUBMITTED->value, 'completed_at' => now()]);

        $this->assertSame(0, $this->analytics()->monthlyCompleted()->sum('completed'));
    }

    /* ══════════════ COMPLETION TIME ══════════════ */

    public function test_completion_time_is_measured_from_the_start_not_the_assignment(): void
    {
        // Assigned a week early, started yesterday, finished today. The work
        // took a day; the six-day wait before it was picked up is a scheduling
        // problem and does not belong in "how long does this work take".
        $this->completedAssignment($this->volunteer, $this->task(), [
            'assigned_at' => now()->subDays(7),
            'started_at' => now()->subDay(),
            'completed_at' => now(),
        ]);

        $time = $this->analytics()->completionTime();

        $this->assertSame(1, $time['sample']);
        $this->assertEqualsWithDelta(24.0, $time['average_hours'], 0.5);
    }

    public function test_the_median_resists_a_single_abandoned_task(): void
    {
        $task = $this->task();

        foreach ([2, 3, 4] as $hours) {
            $this->completedAssignment(User::factory()->create(), $task, [
                'started_at' => now()->subHours($hours),
                'completed_at' => now(),
            ]);
        }

        // One task left running for three months. It should move the mean and
        // leave the median describing a typical job.
        $this->completedAssignment(User::factory()->create(), $task, [
            'started_at' => now()->subDays(90),
            'completed_at' => now(),
        ]);

        $time = $this->analytics()->completionTime();

        $this->assertGreaterThan(400, $time['average_hours']);
        $this->assertLessThan(5, $time['median_hours']);
    }

    public function test_negative_durations_are_discarded(): void
    {
        // A device with a wrong clock can report finishing before starting.
        // Averaging that in silently pulls the figure down.
        $this->completedAssignment($this->volunteer, $this->task(), [
            'started_at' => now(),
            'completed_at' => now()->subHours(5),
        ]);

        $this->assertSame(0, $this->analytics()->completionTime()['sample']);
        $this->assertNull($this->analytics()->completionTime()['average_hours']);
    }

    /* ══════════════ PERFORMERS ══════════════ */

    public function test_a_volunteer_below_the_minimum_sample_is_not_ranked(): void
    {
        // One flawless task is not evidence. Letting it top the league table is
        // how the whole screen loses its credibility.
        $this->completedAssignment($this->volunteer, $this->task());

        $this->assertCount(0, $this->analytics()->performers());
    }

    public function test_ranking_prefers_reliability_over_raw_volume(): void
    {
        $prolific = User::factory()->create(['name' => 'Prolific']);
        $reliable = User::factory()->create(['name' => 'Reliable']);

        // Ten taken on, three finished.
        for ($i = 0; $i < 10; $i++) {
            $this->completedAssignment($prolific, $this->task(), [
                'status' => $i < 3
                    ? TaskAssignmentStatus::APPROVED->value
                    : TaskAssignmentStatus::IN_PROGRESS->value,
                'completed_at' => $i < 3 ? now() : null,
            ]);
        }

        // Four taken on, four finished.
        for ($i = 0; $i < 4; $i++) {
            $this->completedAssignment($reliable, $this->task());
        }

        $ranked = $this->analytics()->performers();

        $this->assertSame('Reliable', $ranked->first()['name']);
        $this->assertSame(100, $ranked->first()['completion_rate']);
        $this->assertSame(30, $ranked->firstWhere('name', 'Prolific')['completion_rate']);
    }

    public function test_a_second_review_cannot_inflate_the_completion_rate(): void
    {
        // The left join to task_reviews yields one row per review. Counted
        // naively, an assignment rejected once and approved on appeal would be
        // counted twice — and a rate over 100% makes every other figure on the
        // page suspect.
        for ($i = 0; $i < 3; $i++) {
            $assignment = $this->completedAssignment($this->volunteer, $this->task());

            foreach ([TaskReviewStatus::REJECTED, TaskReviewStatus::APPROVED] as $verdict) {
                TaskReview::create([
                    'task_id' => $assignment->task_id,
                    'task_assignment_id' => $assignment->id,
                    'reviewed_by' => $this->manager->id,
                    'review_status' => $verdict->value,
                    'score' => 80,
                    'rating' => 4,
                    'reviewed_at' => now(),
                ]);
            }
        }

        $person = $this->analytics()->performers()->first();

        $this->assertSame(3, $person['assigned']);
        $this->assertSame(3, $person['completed']);
        $this->assertSame(100, $person['completion_rate']);
    }

    public function test_a_missing_deadline_does_not_count_against_anyone(): void
    {
        // No due date means the work cannot be late. Scoring the absence as a
        // miss would penalise a volunteer for how the task was written.
        for ($i = 0; $i < 3; $i++) {
            $this->completedAssignment($this->volunteer, $this->task(['due_date' => null]));
        }

        $person = $this->analytics()->performers()->first();

        $this->assertNull($person['punctuality']);
        // Completion alone, renormalised — not 50% of a possible 100.
        $this->assertSame(100.0, $person['score']);
    }

    public function test_the_lowest_performers_list_is_the_same_ranking_reversed(): void
    {
        $strong = User::factory()->create(['name' => 'Strong']);
        $weak = User::factory()->create(['name' => 'Weak']);

        for ($i = 0; $i < 3; $i++) {
            $this->completedAssignment($strong, $this->task());
            $this->completedAssignment($weak, $this->task(), [
                'status' => TaskAssignmentStatus::DECLINED->value,
                'completed_at' => null,
            ]);
        }

        $this->assertSame('Weak', $this->analytics()->performers(5, ascending: true)->first()['name']);
        $this->assertSame('Strong', $this->analytics()->performers(5)->first()['name']);
    }

    /* ══════════════ REVIEWS ══════════════ */

    public function test_review_quality_reports_the_spread_not_just_the_average(): void
    {
        $assignment = $this->completedAssignment($this->volunteer, $this->task());

        foreach ([20, 90, 100] as $score) {
            TaskReview::create([
                'task_id' => $assignment->task_id,
                'task_assignment_id' => $assignment->id,
                'reviewed_by' => $this->manager->id,
                'review_status' => TaskReviewStatus::APPROVED->value,
                'score' => $score,
                'rating' => 4,
                'reviewed_at' => now(),
            ]);
        }

        $quality = $this->analytics()->reviewQuality();

        $this->assertSame(70.0, $quality['average_score']);
        $this->assertSame(90.0, $quality['median_score']);
        $this->assertSame(1, $quality['distribution']['0–20']);
        $this->assertSame(2, $quality['distribution']['81–100']);
        $this->assertSame(100, $quality['first_time_approval']);
    }

    public function test_the_review_backlog_reports_its_age(): void
    {
        // Two separate tasks: one volunteer holds at most one assignee slot per
        // task, so filing two submissions means two jobs, not two attempts.
        //
        // A count alone hides the problem this measures: five waiting an hour is
        // healthy, five waiting three weeks is a programme losing its volunteers.
        $this->submission($this->task(), ['created_at' => now()->subDays(20)]);
        $this->submission($this->task());

        $pending = $this->analytics()->pendingReviews();

        $this->assertSame(2, $pending['count']);
        $this->assertSame(1, $pending['over_a_week']);
        $this->assertSame(20, $pending['oldest_days']);
    }

    /* ══════════════ TREES ══════════════ */

    public function test_the_follow_up_rate_measures_outcomes_not_inputs(): void
    {
        // Trees in the ground is an input. Trees photographed alive months
        // later is the outcome a donor is actually funding.
        $this->tree(['species' => 'Chinar', 'after_image_path' => 'trees/a-after.jpg']);
        $this->tree(['species' => 'Pine']);

        // Not yet moderated, so it counts toward the total but not the rate.
        $this->tree(['species' => 'Walnut', 'status' => 'pending']);

        $trees = $this->analytics()->treePlantation();

        $this->assertSame(3, $trees['total']);
        $this->assertSame(2, $trees['approved']);
        $this->assertSame(1, $trees['pending']);
        $this->assertSame(1, $trees['with_follow_up']);
        $this->assertSame(50, $trees['follow_up_rate']);
    }

    /* ══════════════ HEAT MAPS ══════════════ */

    public function test_the_activity_grid_is_zero_filled_and_uses_monday_as_day_zero(): void
    {
        $task = $this->task();

        // A Monday, 07:00 — a plausible planting hour.
        $this->submission($task, ['created_at' => now()->startOfWeek()->setTime(7, 0)]);

        $map = $this->analytics()->activityHeatMap();

        $this->assertCount(7, $map['grid']);
        $this->assertCount(24, $map['grid'][0]);
        $this->assertSame(1, $map['grid'][0][7]);
        $this->assertSame(1, $map['peak']);
        // Every other cell present and zero, so the grid never renders with holes.
        $this->assertSame(0, $map['grid'][3][14]);
    }

    public function test_location_hotspots_rank_places_by_volume(): void
    {
        foreach (range(1, 3) as $i) {
            $this->task([
                'location_name' => 'Qargha',
                'latitude' => 34.55,
                'longitude' => 69.05,
                'status' => $i === 1 ? TaskStatus::APPROVED->value : TaskStatus::ASSIGNED->value,
            ]);
        }

        $this->task(['location_name' => 'Paghman', 'latitude' => 34.58, 'longitude' => 68.95]);

        // Unnamed places cannot be acted on, so they are left out entirely.
        $this->task(['location_name' => null]);

        $spots = $this->analytics()->locationHotspots();

        $this->assertCount(2, $spots);
        $this->assertSame('Qargha', $spots->first()['location']);
        $this->assertSame(3, $spots->first()['tasks']);
        $this->assertSame(33, $spots->first()['completion_rate']);
    }

    /* ══════════════ EMPTY STATE ══════════════ */

    public function test_the_overview_survives_an_empty_database(): void
    {
        // The state the panel is in on its first day. Every one of these
        // metrics divides by something that is zero here.
        $overview = $this->analytics()->overview();

        $this->assertNull($overview['completion_time']['average_hours']);
        $this->assertNull($overview['review_quality']['average_score']);
        $this->assertNull($overview['pending_reviews']['oldest_days']);
        $this->assertNull($overview['trees']['follow_up_rate']);
        $this->assertSame(0, $overview['heat_map']['peak']);
        $this->assertCount(0, $overview['top_performers']);
    }

    /* ══════════════ THE PAGE AND ITS EXPORTS ══════════════ */

    public function test_the_dashboard_renders(): void
    {
        $this->completedAssignment($this->volunteer, $this->task(['location_name' => 'Qargha']));

        Livewire::actingAs($this->manager)
            ->test(PerformanceDashboard::class)
            ->assertOk();
    }

    public function test_a_volunteer_cannot_open_the_dashboard(): void
    {
        // Analytics is a league table of everyone's performance. Being allowed
        // to see a task does not imply being allowed to see that.
        $this->actingAs($this->volunteer)
            ->get('/admin/performance-dashboard')
            ->assertForbidden();
    }

    public function test_the_excel_report_downloads(): void
    {
        $this->completedAssignment($this->volunteer, $this->task(['location_name' => 'Qargha']));

        $response = $this->actingAs($this->manager)
            ->get(route('admin.analytics.export.excel'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // Streamed, so the body only exists once drained — and a valid xlsx is
        // a zip, which is the cheapest real assertion available here.
        $this->assertStringStartsWith('PK', $response->streamedContent());
    }

    public function test_the_printable_report_renders(): void
    {
        $this->actingAs($this->manager)
            ->get(route('admin.analytics.export.pdf'))
            ->assertOk()
            ->assertSee('Analytics Report');
    }

    public function test_a_volunteer_cannot_export_reports(): void
    {
        $this->actingAs($this->volunteer)
            ->get(route('admin.analytics.export.excel'))
            ->assertForbidden();

        $this->actingAs($this->volunteer)
            ->get(route('admin.analytics.export.pdf'))
            ->assertForbidden();
    }

    public function test_readable_hours_switches_units_where_a_person_would(): void
    {
        $page = new PerformanceDashboard;

        $this->assertSame('—', $page->readableHours(null));
        $this->assertSame('30 min', $page->readableHours(0.5));
        $this->assertSame('6.5 hrs', $page->readableHours(6.5));
        $this->assertSame('2.9 days', $page->readableHours(68.4));
    }

    public function test_heat_map_intensity_never_hides_a_nonzero_cell(): void
    {
        $page = new PerformanceDashboard;

        $this->assertSame(0, $page->intensity(0, 100));
        // One event against a peak of 100 still has to be visible — rounding it
        // to the empty bucket would erase real activity from the map.
        $this->assertSame(1, $page->intensity(1, 100));
        $this->assertSame(4, $page->intensity(100, 100));
        $this->assertSame(0, $page->intensity(0, 0));
    }
}
