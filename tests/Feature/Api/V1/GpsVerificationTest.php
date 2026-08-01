<?php

namespace Tests\Feature\Api\V1;

use App\Models\Task;
use App\Models\Tree;
use App\Models\User;
use App\Services\Tasks\GpsVerificationService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Phase 8 — GPS verification, the volunteer role, and before/after photography.
 */
class GpsVerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $volunteer;

    /** Qargha Lake, Kabul — the fixture site used throughout. */
    private const SITE_LAT = 34.5553;

    private const SITE_LNG = 69.0430;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Manager');

        $this->volunteer = User::factory()->create();
        $this->volunteer->assignRole(User::VOLUNTEER_ROLE);

        Storage::fake('public');
        Http::preventStrayRequests();
        Http::fake(['exp.host/*' => Http::response(['data' => [['status' => 'ok', 'id' => 't']]])]);
    }

    private function geofencedTask(array $overrides = []): Task
    {
        return Task::create(array_merge([
            'title' => 'Water the saplings at Qargha',
            'created_by' => $this->admin->id,
            'requires_photo' => false,
            'requires_geo_check' => true,
            'latitude' => self::SITE_LAT,
            'longitude' => self::SITE_LNG,
            'radius' => 150,
        ], $overrides));
    }

    /** Drive an assignment to the point where it can submit. */
    private function readyToSubmit(Task $task): array
    {
        $assignment = $task->assign($this->volunteer, $this->admin);

        Sanctum::actingAs($this->volunteer);
        $base = "/api/v1/tasks/{$task->uuid}/assignments/{$assignment->id}";
        $this->postJson("{$base}/start")->assertOk();

        return [$assignment, $base];
    }

    /* ══════════════ THE RADIUS IS ENFORCED ══════════════ */

    public function test_a_submission_from_inside_the_radius_is_accepted(): void
    {
        $task = $this->geofencedTask();
        [, $base] = $this->readyToSubmit($task);

        $this->postJson("{$base}/submit", [
            'note' => 'Done',
            'latitude' => 34.5554,   // ~15m away
            'longitude' => 69.0431,
            'gps_accuracy' => 8,
        ])
            ->assertCreated()
            ->assertJsonPath('data.location.is_within_geofence', true);
    }

    public function test_a_submission_from_outside_the_radius_is_refused(): void
    {
        $task = $this->geofencedTask();
        [$assignment, $base] = $this->readyToSubmit($task);

        $this->postJson("{$base}/submit", [
            'note' => 'Submitted from home',
            'latitude' => 34.5800,   // several km away
            'longitude' => 69.0900,
            'gps_accuracy' => 10,
        ])
            ->assertStatus(422)
            ->assertJsonPath('reason', 'outside_geofence');

        // Refused means refused: nothing was recorded.
        $this->assertSame(0, $assignment->fresh()->submissions()->count());
    }

    public function test_the_refusal_tells_the_volunteer_how_far_off_they_are(): void
    {
        $task = $this->geofencedTask();
        [, $base] = $this->readyToSubmit($task);

        $response = $this->postJson("{$base}/submit", [
            'note' => 'x', 'latitude' => 34.5800, 'longitude' => 69.0900,
        ])->assertStatus(422);

        // A generic "too far away" leaves them not knowing whether to walk ten
        // metres or ten kilometres.
        $this->assertStringContainsString('km', $response->json('message'));
        $this->assertStringContainsString('150m radius', $response->json('message'));
    }

    public function test_a_geofenced_task_refuses_a_submission_with_no_location(): void
    {
        $task = $this->geofencedTask();
        [, $base] = $this->readyToSubmit($task);

        $this->postJson("{$base}/submit", ['note' => 'No GPS'])
            ->assertStatus(422)
            ->assertJsonPath('reason', 'outside_geofence');
    }

    public function test_a_task_without_a_geo_check_still_accepts_a_distant_submission(): void
    {
        $task = $this->geofencedTask(['requires_geo_check' => false]);
        [, $base] = $this->readyToSubmit($task);

        // Recorded and flagged, but not refused — the task did not ask for proof.
        $this->postJson("{$base}/submit", [
            'note' => 'Done', 'latitude' => 34.5800, 'longitude' => 69.0900,
        ])
            ->assertCreated()
            ->assertJsonPath('data.location.is_within_geofence', false);
    }

    public function test_device_accuracy_is_allowed_on_top_of_the_radius(): void
    {
        $task = $this->geofencedTask();
        [, $base] = $this->readyToSubmit($task);

        // ~190m out — outside the 150m fence, but inside a ±60m margin.
        // Refusing this punishes the handset, not the person.
        $response = $this->postJson("{$base}/submit", [
            'note' => 'Just outside',
            'latitude' => 34.5570,
            'longitude' => 69.0430,
            'gps_accuracy' => 60,
        ])->assertCreated();

        // Accepted, but still reported as outside the true radius so the
        // reviewer sees it as borderline rather than clean.
        $this->assertFalse($response->json('data.location.is_within_geofence'));
    }

    public function test_a_wildly_optimistic_accuracy_cannot_defeat_the_fence(): void
    {
        $task = $this->geofencedTask();
        [, $base] = $this->readyToSubmit($task);

        // Claiming ±50km would otherwise let a device walk through any fence.
        $this->postJson("{$base}/submit", [
            'note' => 'x',
            'latitude' => 34.5800,
            'longitude' => 69.0900,
            'gps_accuracy' => 50000,
        ])->assertStatus(422);
    }

    /* ══════════════ WHAT IS STORED ══════════════ */

    public function test_the_full_reading_is_stored_including_address_and_capture_time(): void
    {
        $task = $this->geofencedTask();
        [$assignment, $base] = $this->readyToSubmit($task);

        $capturedAt = now()->subMinutes(4);

        $this->postJson("{$base}/submit", [
            'note' => 'Done',
            'latitude' => 34.5554,
            'longitude' => 69.0431,
            'gps_accuracy' => 7,
            'address' => 'Qargha Lake, Kabul, Afghanistan',
            'is_mocked' => false,
            'device_captured_at' => $capturedAt->toIso8601String(),
        ])->assertCreated();

        $submission = $assignment->fresh()->latestSubmission;

        $this->assertSame(34.5554, (float) $submission->latitude);
        $this->assertSame(69.0431, (float) $submission->longitude);
        $this->assertSame(7, $submission->gps_accuracy);
        $this->assertSame('Qargha Lake, Kabul, Afghanistan', $submission->address);
        $this->assertFalse($submission->is_mocked);
        $this->assertNotNull($submission->device_captured_at);
        $this->assertNotNull($submission->distance_meters);
    }

    /* ══════════════ FAKE GPS SIGNALS ══════════════ */

    public function test_a_mock_provider_flag_is_recorded_as_a_high_severity_finding(): void
    {
        $task = $this->geofencedTask();
        [$assignment, $base] = $this->readyToSubmit($task);

        $this->postJson("{$base}/submit", [
            'note' => 'Done',
            'latitude' => 34.5554,
            'longitude' => 69.0431,
            'gps_accuracy' => 8,
            'is_mocked' => true,
        ])->assertCreated();

        $submission = $assignment->fresh()->latestSubmission;

        // Flagged, not refused: the reviewer decides, because even this signal
        // can be wrong on a developer's own handset.
        $this->assertTrue($submission->wasMocked());
        $this->assertSame('high', $submission->verificationSeverity());
        $this->assertContains('mock_provider', array_column($submission->verification, 'code'));
    }

    public function test_an_impossibly_precise_reading_is_flagged(): void
    {
        $task = $this->geofencedTask();
        [$assignment, $base] = $this->readyToSubmit($task);

        $this->postJson("{$base}/submit", [
            'note' => 'Done', 'latitude' => 34.5554, 'longitude' => 69.0431, 'gps_accuracy' => 0,
        ])->assertCreated();

        $codes = array_column($assignment->fresh()->latestSubmission->verification, 'code');

        $this->assertContains('implausible_accuracy', $codes);
    }

    public function test_a_device_clock_far_from_server_time_is_flagged(): void
    {
        $task = $this->geofencedTask();
        [$assignment, $base] = $this->readyToSubmit($task);

        $this->postJson("{$base}/submit", [
            'note' => 'Done',
            'latitude' => 34.5554,
            'longitude' => 69.0431,
            'gps_accuracy' => 8,
            'device_captured_at' => now()->subDays(3)->toIso8601String(),
        ])->assertCreated();

        $codes = array_column($assignment->fresh()->latestSubmission->verification, 'code');

        $this->assertContains('clock_skew', $codes);
    }

    public function test_impossible_travel_between_two_submissions_is_flagged(): void
    {
        // First submission in Kabul.
        $first = $this->geofencedTask(['title' => 'Kabul site']);
        [$assignmentA, $baseA] = $this->readyToSubmit($first);
        $this->postJson("{$baseA}/submit", [
            'note' => 'Done', 'latitude' => 34.5554, 'longitude' => 69.0431, 'gps_accuracy' => 8,
        ])->assertCreated();

        // Backdated ten minutes. Without a real gap the check correctly declines
        // to judge: two readings in the same second divide by ~zero and produce
        // a nonsense speed, which is exactly the false positive the guard in
        // checkTravelSpeed() exists to prevent.
        $assignmentA->fresh()->latestSubmission
            ->forceFill(['created_at' => now()->subMinutes(10)])->save();

        // Ten minutes later, 600km away in Herat.
        $second = $this->geofencedTask([
            'title' => 'Herat site', 'latitude' => 34.3529, 'longitude' => 62.2040,
        ]);
        [$assignmentB, $baseB] = $this->readyToSubmit($second);

        $this->postJson("{$baseB}/submit", [
            'note' => 'Done', 'latitude' => 34.3529, 'longitude' => 62.2041, 'gps_accuracy' => 8,
        ])->assertCreated();

        $codes = array_column($assignmentB->fresh()->latestSubmission->verification, 'code');

        $this->assertContains('implausible_travel', $codes);
    }

    public function test_a_clean_reading_produces_no_findings(): void
    {
        $task = $this->geofencedTask();
        [$assignment, $base] = $this->readyToSubmit($task);

        $this->postJson("{$base}/submit", [
            'note' => 'Done',
            'latitude' => 34.5554,
            'longitude' => 69.0431,
            'gps_accuracy' => 9,
            'is_mocked' => false,
            'device_captured_at' => now()->subMinutes(2)->toIso8601String(),
        ])->assertCreated();

        $submission = $assignment->fresh()->latestSubmission;

        $this->assertNull($submission->verification);
        $this->assertNull($submission->verificationSeverity());
    }

    public function test_the_scrutiny_scope_finds_anything_doubtful(): void
    {
        $task = $this->geofencedTask();
        [$assignment, $base] = $this->readyToSubmit($task);

        $this->postJson("{$base}/submit", [
            'note' => 'Done', 'latitude' => 34.5554, 'longitude' => 69.0431,
            'gps_accuracy' => 8, 'is_mocked' => true,
        ])->assertCreated();

        // Inside the fence, so `suspicious` misses it — but the mock flag is
        // the more serious signal of the two.
        $this->assertSame(0, \App\Models\TaskSubmission::suspicious()->count());
        $this->assertSame(1, \App\Models\TaskSubmission::needsScrutiny()->count());
    }

    /* ══════════════ THE VOLUNTEER ROLE ══════════════ */

    public function test_registration_creates_a_volunteer(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Nasir',
            'lastname' => 'Ahmadi',
            'email' => 'nasir@example.test',
            'country' => 'Afghanistan',
            'address' => 'Kabul',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'device_name' => 'Pixel 7',
        ])->assertCreated();

        $user = User::firstWhere('email', 'nasir@example.test');

        $this->assertTrue($user->hasRole(User::VOLUNTEER_ROLE));
        $this->assertTrue($user->isVolunteer());
    }

    public function test_a_volunteer_cannot_enter_the_admin_panel(): void
    {
        // The whole reason canAccessPanel had to change alongside the new role:
        // before, any role at all opened the panel.
        $this->assertFalse($this->volunteer->canAccessPanel(app(\Filament\Panel::class)));

        $this->actingAs($this->volunteer)->get('/admin')->assertForbidden();
    }

    public function test_staff_can_still_enter_the_panel(): void
    {
        $this->actingAs($this->admin)->get('/admin/tasks')->assertOk();
    }

    public function test_the_volunteer_role_carries_no_permissions(): void
    {
        $this->assertFalse($this->volunteer->can('view_any_task'));
        $this->assertFalse($this->volunteer->can('assign_task'));
        $this->assertFalse($this->volunteer->can('review_task'));
    }

    /* ══════════════ ASSIGNING FROM THE MOBILE APP ══════════════ */

    public function test_an_admin_can_list_assignable_volunteers_from_the_app(): void
    {
        Sanctum::actingAs($this->admin);

        $this->getJson('/api/v1/tasks/assignable-users')
            ->assertOk()
            ->assertJsonFragment(['id' => $this->volunteer->id]);
    }

    public function test_the_assignable_list_excludes_staff(): void
    {
        Sanctum::actingAs($this->admin);

        $ids = collect($this->getJson('/api/v1/tasks/assignable-users')->json('data'))->pluck('id');

        // A picker that lists every account is a directory, not a picker.
        $this->assertFalse($ids->contains($this->admin->id));
    }

    public function test_a_volunteer_cannot_list_assignable_users(): void
    {
        Sanctum::actingAs($this->volunteer);

        $this->getJson('/api/v1/tasks/assignable-users')->assertForbidden();
    }

    public function test_an_admin_can_assign_a_task_from_the_app(): void
    {
        $task = $this->geofencedTask();

        Sanctum::actingAs($this->admin);

        $this->postJson("/api/v1/tasks/{$task->uuid}/assignments", [
            'user_ids' => [$this->volunteer->id],
        ])->assertCreated();

        $this->assertSame(1, $task->fresh()->assignees()->count());
    }

    /* ══════════════ TREE BEFORE / AFTER ══════════════ */

    private function tree(array $overrides = []): Tree
    {
        return Tree::create(array_merge([
            'user_id' => $this->volunteer->id,
            'species' => 'Chinar',
            'latitude' => self::SITE_LAT,
            'longitude' => self::SITE_LNG,
            'planted_on' => now()->subMonths(6)->toDateString(),
            'image_path' => 'trees/before.jpg',
            'status' => 'approved',
        ], $overrides));
    }

    public function test_a_planter_can_add_the_after_photo_to_the_same_record(): void
    {
        $tree = $this->tree();

        Sanctum::actingAs($this->volunteer);

        $this->post(
            "/api/v1/trees/{$tree->id}/after-image",
            ['image' => UploadedFile::fake()->image('after.jpg'), 'note' => 'Six months on.'],
            ['Accept' => 'application/json'],
        )->assertCreated();

        $tree->refresh();

        // Same row — that is the whole point.
        $this->assertNotNull($tree->after_image_path);
        $this->assertSame('trees/before.jpg', $tree->image_path);
        $this->assertSame('Six months on.', $tree->after_image_note);
        $this->assertTrue($tree->hasComparison());
        $this->assertSame(6 * 30, (int) round($tree->growthDays() / 30) * 30);
    }

    public function test_only_the_planter_may_add_the_after_photo(): void
    {
        $tree = $this->tree();
        $stranger = User::factory()->create();
        $stranger->assignRole(User::VOLUNTEER_ROLE);

        Sanctum::actingAs($stranger);

        $this->post(
            "/api/v1/trees/{$tree->id}/after-image",
            ['image' => UploadedFile::fake()->image('after.jpg')],
            ['Accept' => 'application/json'],
        )->assertForbidden();
    }

    public function test_a_tree_with_no_planting_photo_has_nothing_to_compare(): void
    {
        $tree = $this->tree(['image_path' => null]);

        Sanctum::actingAs($this->volunteer);

        $this->post(
            "/api/v1/trees/{$tree->id}/after-image",
            ['image' => UploadedFile::fake()->image('after.jpg')],
            ['Accept' => 'application/json'],
        )->assertStatus(422);
    }

    public function test_a_future_capture_date_is_clamped(): void
    {
        $tree = $this->tree();

        Sanctum::actingAs($this->volunteer);

        $this->post(
            "/api/v1/trees/{$tree->id}/after-image",
            [
                'image' => UploadedFile::fake()->image('after.jpg'),
                // A wrong phone clock must not produce a tree photographed
                // next year, which would make the growth caption absurd.
                'taken_at' => now()->addYear()->toDateTimeString(),
            ],
            ['Accept' => 'application/json'],
        )->assertCreated();

        $this->assertFalse($tree->fresh()->after_image_taken_at->isFuture());
    }

    public function test_replacing_the_after_photo_removes_the_old_file(): void
    {
        $tree = $this->tree();

        Sanctum::actingAs($this->volunteer);

        $this->post("/api/v1/trees/{$tree->id}/after-image",
            ['image' => UploadedFile::fake()->image('first.jpg')], ['Accept' => 'application/json']);

        $first = $tree->fresh()->after_image_path;
        Storage::disk('public')->assertExists($first);

        $this->post("/api/v1/trees/{$tree->id}/after-image",
            ['image' => UploadedFile::fake()->image('second.jpg')], ['Accept' => 'application/json']);

        // A record has exactly one "now" — replacing must not leak storage.
        Storage::disk('public')->assertMissing($first);
        $this->assertNotSame($first, $tree->fresh()->after_image_path);
    }

    public function test_the_comparison_scopes_split_trees_correctly(): void
    {
        $withAfter = $this->tree();
        $withoutAfter = $this->tree(['species' => 'Pine']);

        $withAfter->update(['after_image_path' => 'trees/after.jpg', 'after_image_taken_at' => now()]);

        $this->assertTrue(Tree::withComparison()->pluck('id')->contains($withAfter->id));
        $this->assertTrue(Tree::awaitingAfterImage()->pluck('id')->contains($withoutAfter->id));
        $this->assertFalse(Tree::awaitingAfterImage()->pluck('id')->contains($withAfter->id));
    }

    /* ══════════════ THE SERVICE IN ISOLATION ══════════════ */

    public function test_the_radius_check_ignores_tasks_that_do_not_ask_for_it(): void
    {
        $task = $this->geofencedTask(['requires_geo_check' => false]);

        $result = app(GpsVerificationService::class)->checkRadius($task, 0.0, 0.0, null);

        $this->assertTrue($result['allowed']);
        $this->assertNull($result['within']);
    }
}
