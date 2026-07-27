<?php

namespace Tests\Feature\Api\V1;

use App\Enums\TaskAssignmentStatus;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskNotification;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * End-to-end coverage of the task API.
 *
 * Exercises the real stack — routes, form requests, policies, services,
 * repositories and resources — rather than calling the services directly. The
 * things most likely to break here are the seams between those layers, and only
 * an HTTP-level test crosses them.
 */
class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $volunteer;

    private User $stranger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Manager');

        // Volunteers hold no role and no permissions at all — which is exactly
        // what makes the policies interesting.
        $this->volunteer = User::factory()->create();
        $this->stranger = User::factory()->create();
    }

    private function task(array $attributes = []): Task
    {
        return Task::create(array_merge([
            'title' => 'Water the saplings at Qargha',
            'created_by' => $this->admin->id,
            'requires_photo' => false,
        ], $attributes));
    }

    /* ══════════════ AUTH ══════════════ */

    public function test_the_api_is_closed_to_anonymous_callers(): void
    {
        $this->getJson('/api/v1/tasks')->assertUnauthorized();
        $this->postJson('/api/v1/tasks', [])->assertUnauthorized();
    }

    /* ══════════════ INDEX: FILTER / SORT / SEARCH / PAGINATE ══════════════ */

    public function test_index_returns_the_standard_envelope_with_counts(): void
    {
        Sanctum::actingAs($this->admin);
        $this->task();

        $this->getJson('/api/v1/tasks')
            ->assertOk()
            ->assertJsonStructure([
                'success', 'message',
                'data' => [['id', 'reference', 'title', 'status' => ['value', 'label'], 'priority', 'progress']],
                'counts', 'meta' => ['current_page', 'last_page', 'per_page', 'total', 'has_more'],
            ])
            ->assertJsonPath('success', true);
    }

    public function test_index_exposes_the_uuid_and_never_the_integer_id(): void
    {
        Sanctum::actingAs($this->admin);
        $task = $this->task();

        $id = $this->getJson('/api/v1/tasks')->json('data.0.id');

        $this->assertSame($task->uuid, $id);
        $this->assertNotSame((string) $task->id, $id);
    }

    public function test_index_filters_by_status_and_priority(): void
    {
        Sanctum::actingAs($this->admin);
        $this->task(['title' => 'Low draft', 'priority' => 'low']);
        $this->task(['title' => 'Critical draft', 'priority' => 'critical']);

        $this->getJson('/api/v1/tasks?priority=critical')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Critical draft');

        $this->getJson('/api/v1/tasks?status=draft')->assertOk()->assertJsonCount(2, 'data');
        $this->getJson('/api/v1/tasks?status=approved')->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_index_accepts_comma_separated_and_repeated_filter_values(): void
    {
        Sanctum::actingAs($this->admin);
        $this->task(['priority' => 'low']);
        $this->task(['priority' => 'high']);
        $this->task(['priority' => 'critical']);

        $this->getJson('/api/v1/tasks?priority=low,high')->assertOk()->assertJsonCount(2, 'data');
        $this->getJson('/api/v1/tasks?priority[]=low&priority[]=critical')->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_index_searches_title_and_matches_reference_exactly(): void
    {
        Sanctum::actingAs($this->admin);
        $needle = $this->task(['title' => 'Prune the orchard row']);
        $this->task(['title' => 'Something else entirely']);

        $this->getJson('/api/v1/tasks?search=orchard')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/tasks?search='.$needle->fresh()->reference)
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $needle->uuid);
    }

    public function test_index_sorts_on_an_allow_listed_column_and_rejects_anything_else(): void
    {
        Sanctum::actingAs($this->admin);
        $this->task(['title' => 'Bravo']);
        $this->task(['title' => 'Alpha']);

        $this->getJson('/api/v1/tasks?sort=title&direction=asc')
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Alpha');

        // An unlisted column is a 422, not a silently ignored parameter and not
        // an ORDER BY injection.
        $this->getJson('/api/v1/tasks?sort=created_by;DROP TABLE tasks')
            ->assertStatus(422)
            ->assertJsonValidationErrors('sort');
    }

    public function test_index_paginates_and_clamps_the_page_size(): void
    {
        Sanctum::actingAs($this->admin);
        for ($i = 0; $i < 8; $i++) {
            $this->task(['title' => "Bulk {$i}"]);
        }

        $this->getJson('/api/v1/tasks?per_page=3')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('meta.total', 8)
            ->assertJsonPath('meta.has_more', true);

        // Above the ceiling is a validation error rather than a request for
        // everything in the table.
        $this->getJson('/api/v1/tasks?per_page=5000')->assertStatus(422);
    }

    public function test_index_rejects_a_partial_geo_filter_and_an_oversized_radius(): void
    {
        Sanctum::actingAs($this->admin);

        $this->getJson('/api/v1/tasks?latitude=34.55')->assertStatus(422);
        $this->getJson('/api/v1/tasks?latitude=34.55&longitude=69.04&radius_km=9000')
            ->assertStatus(422)
            ->assertJsonValidationErrors('radius_km');
    }

    public function test_index_filters_by_bounding_box(): void
    {
        Sanctum::actingAs($this->admin);
        $this->task(['title' => 'Kabul', 'latitude' => 34.5553, 'longitude' => 69.0430]);
        $this->task(['title' => 'Far away', 'latitude' => 12.0, 'longitude' => 20.0]);

        $this->getJson('/api/v1/tasks?latitude=34.5553&longitude=69.0430&radius_km=25')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Kabul');
    }

    /* ══════════════ SCOPING: STAFF vs VOLUNTEER ══════════════ */

    public function test_a_volunteer_only_sees_tasks_they_are_assigned_to(): void
    {
        $mine = $this->task(['title' => 'Mine']);
        $mine->assign($this->volunteer, $this->admin);
        $this->task(['title' => 'Not mine']);

        Sanctum::actingAs($this->volunteer);

        $this->getJson('/api/v1/tasks')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Mine');
    }

    public function test_staff_see_every_task(): void
    {
        $this->task(['title' => 'A']);
        $this->task(['title' => 'B']);

        Sanctum::actingAs($this->admin);
        $this->getJson('/api/v1/tasks')->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_mine_returns_a_workload_summary(): void
    {
        $task = $this->task();
        $task->assign($this->volunteer, $this->admin);

        Sanctum::actingAs($this->volunteer);

        $this->getJson('/api/v1/tasks/mine')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure(['summary' => ['total', 'open', 'awaiting_review', 'approved', 'overdue']])
            ->assertJsonPath('summary.total', 1)
            ->assertJsonPath('summary.open', 1);
    }

    /* ══════════════ SHOW ══════════════ */

    public function test_show_is_refused_to_someone_with_no_connection_to_the_task(): void
    {
        $task = $this->task();

        Sanctum::actingAs($this->stranger);
        $this->getJson("/api/v1/tasks/{$task->uuid}")->assertForbidden();
    }

    public function test_an_assignee_may_view_their_task_despite_holding_no_permissions(): void
    {
        $task = $this->task();
        $task->assign($this->volunteer, $this->admin);

        Sanctum::actingAs($this->volunteer);

        $this->getJson("/api/v1/tasks/{$task->uuid}")
            ->assertOk()
            ->assertJsonPath('data.id', $task->uuid)
            ->assertJsonPath('data.abilities.submit', true)
            ->assertJsonPath('data.abilities.update', false)
            ->assertJsonPath('data.abilities.review', false);
    }

    public function test_an_integer_id_in_the_url_is_a_404(): void
    {
        $task = $this->task();

        Sanctum::actingAs($this->admin);
        $this->getJson("/api/v1/tasks/{$task->id}")->assertNotFound();
    }

    /* ══════════════ STORE / UPDATE ══════════════ */

    public function test_a_volunteer_cannot_create_a_task(): void
    {
        Sanctum::actingAs($this->volunteer);
        $this->postJson('/api/v1/tasks', ['title' => 'Sneaky'])->assertForbidden();
    }

    public function test_store_validates_and_creates_with_checklist_and_assignees(): void
    {
        Sanctum::actingAs($this->admin);
        $category = TaskCategory::create(['name' => 'Watering']);

        $this->postJson('/api/v1/tasks', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('title');

        $response = $this->postJson('/api/v1/tasks', [
            'title' => 'Water 40 saplings',
            'task_category_id' => $category->id,
            'priority' => 'high',
            'due_date' => now()->addDays(3)->toDateTimeString(),
            'latitude' => 34.5553,
            'longitude' => 69.0430,
            'radius' => 150,
            'checklist' => [
                ['title' => 'Fill the tank'],
                ['title' => 'Photograph row A', 'requires_photo' => true],
            ],
            'assignee_ids' => [$this->volunteer->id],
        ])->assertCreated();

        $task = Task::firstWhere('title', 'Water 40 saplings');

        $this->assertSame(2, $task->checklistItems()->count());
        $this->assertSame(1, $task->assignees()->count());
        // Assigning during creation publishes it out of Draft.
        $this->assertSame(TaskStatus::ASSIGNED, $task->status);
        $response->assertJsonPath('data.id', $task->uuid);

        // And the assignee was told.
        $this->assertDatabaseHas('task_notifications', [
            'user_id' => $this->volunteer->id,
            'type' => 'task.assigned',
        ]);
    }

    public function test_store_rejects_a_due_date_before_the_start_date(): void
    {
        Sanctum::actingAs($this->admin);

        $this->postJson('/api/v1/tasks', [
            'title' => 'Backwards',
            'start_date' => now()->addWeek()->toDateTimeString(),
            'due_date' => now()->toDateTimeString(),
        ])->assertStatus(422)->assertJsonValidationErrors('due_date');
    }

    public function test_store_rejects_a_geo_check_with_no_location(): void
    {
        Sanctum::actingAs($this->admin);

        $this->postJson('/api/v1/tasks', [
            'title' => 'Nowhere',
            'requires_geo_check' => true,
        ])->assertStatus(422)->assertJsonValidationErrors('requires_geo_check');
    }

    public function test_update_is_a_patch_and_leaves_omitted_fields_alone(): void
    {
        Sanctum::actingAs($this->admin);
        $task = $this->task(['description' => 'Keep me']);

        $this->patchJson("/api/v1/tasks/{$task->uuid}", ['title' => 'Renamed'])->assertOk();

        $task->refresh();
        $this->assertSame('Renamed', $task->title);
        $this->assertSame('Keep me', $task->description);
    }

    public function test_a_finished_task_cannot_be_edited(): void
    {
        Sanctum::actingAs($this->admin);
        $task = $this->task();
        $task->forceFill(['status' => TaskStatus::APPROVED->value])->save();

        $this->patchJson("/api/v1/tasks/{$task->uuid}", ['title' => 'Rewriting history'])
            ->assertStatus(422)
            ->assertJsonPath('reason', 'task_closed');
    }

    /* ══════════════ ASSIGNMENT LIFECYCLE ══════════════ */

    public function test_the_full_volunteer_journey(): void
    {
        $task = $this->task(['requires_review' => true]);

        Sanctum::actingAs($this->admin);
        $this->postJson("/api/v1/tasks/{$task->uuid}/assignments", [
            'user_ids' => [$this->volunteer->id],
        ])->assertCreated();

        $assignment = $task->assignees()->first();

        Sanctum::actingAs($this->volunteer);
        $base = "/api/v1/tasks/{$task->uuid}/assignments/{$assignment->id}";

        $this->postJson("{$base}/accept")->assertOk()->assertJsonPath('data.status.value', 'accepted');
        $this->postJson("{$base}/start")->assertOk()->assertJsonPath('data.status.value', 'in_progress');

        $this->postJson("{$base}/progress", ['progress_percentage' => 50, 'note' => 'Halfway'])
            ->assertCreated()
            ->assertJsonPath('data.progress.progress_percentage', 50);

        $this->postJson("{$base}/submit", ['note' => 'Finished'])
            ->assertCreated()
            ->assertJsonPath('data.attempt', 1);

        $this->assertSame(TaskAssignmentStatus::SUBMITTED, $assignment->fresh()->status);
        $this->assertSame(TaskStatus::SUBMITTED, $task->fresh()->status);

        // The reviewer was told, and the work is in the queue.
        Sanctum::actingAs($this->admin);
        $this->getJson('/api/v1/tasks/reviews/queue')->assertOk()->assertJsonCount(1, 'data');

        $this->postJson("{$base}/reviews", [
            'review_status' => 'approved',
            'score' => 92.5,
            'rating' => 5,
            'comments' => 'Well done.',
        ])->assertCreated()->assertJsonPath('data.status.value', 'approved');

        $this->assertSame(TaskStatus::APPROVED, $task->fresh()->status);
    }

    public function test_a_volunteer_cannot_act_on_someone_elses_assignment(): void
    {
        $task = $this->task();
        $assignment = $task->assign($this->volunteer, $this->admin);

        Sanctum::actingAs($this->stranger);

        $this->postJson("/api/v1/tasks/{$task->uuid}/assignments/{$assignment->id}/accept")
            ->assertForbidden();
    }

    public function test_staff_cannot_accept_on_a_volunteers_behalf(): void
    {
        $task = $this->task();
        $assignment = $task->assign($this->volunteer, $this->admin);

        Sanctum::actingAs($this->admin);

        // Faking a volunteer's acceptance would make the lifecycle timestamps
        // worthless as evidence.
        $this->postJson("/api/v1/tasks/{$task->uuid}/assignments/{$assignment->id}/accept")
            ->assertForbidden();
    }

    public function test_the_single_assignee_limit_is_enforced_through_the_api(): void
    {
        Sanctum::actingAs($this->admin);
        $task = $this->task(['max_assignees' => 1]);
        $task->assign($this->volunteer, $this->admin);

        $this->postJson("/api/v1/tasks/{$task->uuid}/assignments", [
            'user_ids' => [$this->stranger->id],
        ])
            ->assertStatus(422)
            ->assertJsonPath('reason', 'assignment_refused');
    }

    public function test_submitting_is_blocked_until_required_checklist_steps_are_done(): void
    {
        $task = $this->task();
        $task->checklistItems()->create(['title' => 'Dig the pit', 'is_required' => true]);
        $assignment = $task->assign($this->volunteer, $this->admin);

        Sanctum::actingAs($this->volunteer);
        $base = "/api/v1/tasks/{$task->uuid}/assignments/{$assignment->id}";

        $this->postJson("{$base}/start")->assertOk();
        $this->postJson("{$base}/submit", ['note' => 'Done'])
            ->assertStatus(422)
            ->assertJsonPath('reason', 'checklist_incomplete');

        $item = $task->checklistItems()->first();
        $this->postJson("{$base}/checklist/{$item->id}")->assertOk()->assertJsonPath('data.progress', 100);

        $this->postJson("{$base}/submit", ['note' => 'Done'])->assertCreated();
    }

    public function test_a_task_requiring_a_photo_refuses_a_submission_without_one(): void
    {
        Storage::fake('public');

        $task = $this->task(['requires_photo' => true]);
        $assignment = $task->assign($this->volunteer, $this->admin);

        Sanctum::actingAs($this->volunteer);
        $base = "/api/v1/tasks/{$task->uuid}/assignments/{$assignment->id}";
        $this->postJson("{$base}/start")->assertOk();

        $this->postJson("{$base}/submit", ['note' => 'No photo'])
            ->assertStatus(422)
            ->assertJsonPath('reason', 'photo_required');

        $this->post("{$base}/submit", [
            'note' => 'With photo',
            'files' => [UploadedFile::fake()->image('proof.jpg')],
        ], ['Accept' => 'application/json'])->assertCreated();
    }

    public function test_a_submission_records_its_distance_from_the_task(): void
    {
        // `requires_geo_check` off: this asserts that distance is measured and
        // flagged — the behaviour for a task that wants a reading but does not
        // insist on presence. Enforcement of the radius is a separate contract,
        // covered in GpsVerificationTest.
        $task = $this->task([
            'latitude' => 34.5553,
            'longitude' => 69.0430,
            'radius' => 150,
            'requires_geo_check' => false,
        ]);
        $assignment = $task->assign($this->volunteer, $this->admin);

        Sanctum::actingAs($this->volunteer);
        $base = "/api/v1/tasks/{$task->uuid}/assignments/{$assignment->id}";
        $this->postJson("{$base}/start")->assertOk();

        $this->postJson("{$base}/submit", [
            'note' => 'Submitted from far away',
            'latitude' => 34.5800,
            'longitude' => 69.0900,
        ])
            ->assertCreated()
            ->assertJsonPath('data.location.is_within_geofence', false);
    }

    /* ══════════════ IDEMPOTENCY ══════════════ */

    public function test_a_retried_progress_report_does_not_duplicate(): void
    {
        $task = $this->task();
        $assignment = $task->assign($this->volunteer, $this->admin);

        Sanctum::actingAs($this->volunteer);
        $url = "/api/v1/tasks/{$task->uuid}/assignments/{$assignment->id}/progress";
        $key = '3f2504e0-4f89-41d3-9a0c-0305e82c3301';

        $first = $this->postJson($url, ['progress_percentage' => 30, 'client_uuid' => $key])->assertCreated();
        $second = $this->postJson($url, ['progress_percentage' => 30, 'client_uuid' => $key])->assertCreated();

        $this->assertSame($first->json('data.progress.id'), $second->json('data.progress.id'));
        $this->assertSame(1, $assignment->progressUpdates()->count());
    }

    /* ══════════════ REVIEW ══════════════ */

    public function test_a_rejection_must_explain_itself(): void
    {
        $task = $this->task();
        $assignment = $task->assign($this->volunteer, $this->admin);

        Sanctum::actingAs($this->volunteer);
        $base = "/api/v1/tasks/{$task->uuid}/assignments/{$assignment->id}";
        $this->postJson("{$base}/start")->assertOk();
        $this->postJson("{$base}/submit", ['note' => 'Done'])->assertCreated();

        Sanctum::actingAs($this->admin);
        $this->postJson("{$base}/reviews", ['review_status' => 'rejected'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('comments');
    }

    public function test_reviewing_work_with_no_submission_is_refused(): void
    {
        $task = $this->task();
        $assignment = $task->assign($this->volunteer, $this->admin);

        Sanctum::actingAs($this->admin);

        $this->postJson("/api/v1/tasks/{$task->uuid}/assignments/{$assignment->id}/reviews", [
            'review_status' => 'approved',
        ])->assertStatus(422)->assertJsonPath('reason', 'nothing_to_review');
    }

    public function test_a_volunteer_cannot_review_their_own_work(): void
    {
        $task = $this->task();
        $assignment = $task->assign($this->volunteer, $this->admin);

        Sanctum::actingAs($this->volunteer);
        $base = "/api/v1/tasks/{$task->uuid}/assignments/{$assignment->id}";
        $this->postJson("{$base}/start")->assertOk();
        $this->postJson("{$base}/submit", ['note' => 'Done'])->assertCreated();

        $this->postJson("{$base}/reviews", ['review_status' => 'approved'])->assertForbidden();
    }

    /* ══════════════ NOTIFICATIONS ══════════════ */

    public function test_the_inbox_is_scoped_to_the_caller(): void
    {
        TaskNotification::notify($this->volunteer, 'task.assigned', 'Yours', 'body');
        $theirs = TaskNotification::notify($this->stranger, 'task.assigned', 'Theirs', 'body');

        Sanctum::actingAs($this->volunteer);

        $this->getJson('/api/v1/tasks/notifications')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Yours')
            ->assertJsonPath('unread', 1);

        // Another user's notification is a 404, not a 403 — confirming it exists
        // would itself be a leak.
        $this->postJson("/api/v1/tasks/notifications/{$theirs->id}/read")->assertNotFound();
        $this->assertFalse($theirs->fresh()->is_read);
    }

    public function test_marking_read_and_unread_moves_the_badge(): void
    {
        $notification = TaskNotification::notify($this->volunteer, 'task.assigned', 'T', 'B');

        Sanctum::actingAs($this->volunteer);

        $this->getJson('/api/v1/tasks/notifications/unread-count')->assertOk()->assertJsonPath('data.count', 1);
        $this->postJson("/api/v1/tasks/notifications/{$notification->id}/read")->assertOk()->assertJsonPath('data.unread', 0);
        $this->postJson("/api/v1/tasks/notifications/{$notification->id}/unread")->assertOk()->assertJsonPath('data.unread', 1);
        $this->postJson('/api/v1/tasks/notifications/read-all')->assertOk()->assertJsonPath('data.marked', 1);
        $this->getJson('/api/v1/tasks/notifications/unread-count')->assertOk()->assertJsonPath('data.count', 0);
    }

    public function test_preferences_default_to_enabled_and_persist_an_opt_out(): void
    {
        Sanctum::actingAs($this->volunteer);

        $this->getJson('/api/v1/tasks/notifications/preferences')
            ->assertOk()
            ->assertJsonPath('data.preferences.0.enabled', true);

        $this->putJson('/api/v1/tasks/notifications/preferences', [
            'preferences' => [['event_key' => 'task.due_soon', 'enabled' => false]],
        ])->assertOk();

        $this->assertDatabaseHas('notification_preferences', [
            'user_id' => $this->volunteer->id,
            'event_key' => 'task.due_soon',
            'enabled' => false,
        ]);

        // Opting back in deletes the row rather than storing "true".
        $this->putJson('/api/v1/tasks/notifications/preferences', [
            'preferences' => [['event_key' => 'task.due_soon', 'enabled' => true]],
        ])->assertOk();

        $this->assertDatabaseCount('notification_preferences', 0);
    }

    public function test_an_unknown_preference_key_is_rejected(): void
    {
        Sanctum::actingAs($this->volunteer);

        $this->putJson('/api/v1/tasks/notifications/preferences', [
            'preferences' => [['event_key' => 'task.not_a_thing', 'enabled' => false]],
        ])->assertStatus(422);
    }

    /* ══════════════ N+1 GUARD ══════════════ */

    public function test_the_list_endpoint_does_not_scale_queries_with_rows(): void
    {
        Sanctum::actingAs($this->admin);

        for ($i = 0; $i < 10; $i++) {
            $this->task(['title' => "Task {$i}"])->assign($this->volunteer, $this->admin);
        }

        $queries = 0;
        \DB::listen(function () use (&$queries) {
            $queries++;
        });

        $this->getJson('/api/v1/tasks?per_page=10')->assertOk()->assertJsonCount(10, 'data');

        // Auth, count, page, category, assignees, users, status counts — a fixed
        // set. Ten rows must not mean thirty queries.
        $this->assertLessThan(15, $queries, "The list endpoint ran {$queries} queries for 10 rows.");
    }
}
