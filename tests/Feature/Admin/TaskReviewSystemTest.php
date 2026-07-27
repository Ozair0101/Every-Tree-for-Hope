<?php

namespace Tests\Feature\Admin;

use App\Enums\TaskAssignmentStatus;
use App\Enums\TaskReviewStatus;
use App\Enums\TaskStatus;
use App\Enums\TaskSubmissionStatus;
use App\Filament\Resources\TaskSubmissions\Pages\ListTaskSubmissions;
use App\Filament\Resources\TaskSubmissions\Pages\ReviewSubmission;
use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\TaskNotification;
use App\Models\TaskSubmission;
use App\Models\User;
use App\Services\Tasks\TaskAssignmentService;
use App\Services\Tasks\TaskCommentService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Phase 7 — the admin review system.
 *
 * Renders the real review page rather than calling the service directly: the
 * requirement is that an admin can *see* the evidence, and only rendering the
 * page proves the photos, GPS, timeline and comments actually reach the screen.
 */
class TaskReviewSystemTest extends TestCase
{
    use RefreshDatabase;

    private User $reviewer;

    private User $volunteer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->reviewer = User::factory()->create(['name' => 'Coordinator']);
        $this->reviewer->assignRole('Manager');

        $this->volunteer = User::factory()->create(['name' => 'Ahmad']);

        Storage::fake('public');
        Http::preventStrayRequests();
        Http::fake(['exp.host/*' => Http::response(['data' => [['status' => 'ok', 'id' => 't']]])]);
    }

    /**
     * Drive a task all the way to a pending submission, the way a volunteer
     * actually would — so the fixture exercises the same code the app does.
     */
    private function submitted(array $taskAttributes = []): TaskSubmission
    {
        $task = Task::create(array_merge([
            'title' => 'Water the saplings at Qargha',
            'created_by' => $this->reviewer->id,
            'requires_photo' => false,
            'requires_review' => true,
            'latitude' => 34.5553,
            'longitude' => 69.0430,
            'radius' => 150,
        ], $taskAttributes));

        $assignment = $task->assign($this->volunteer, $this->reviewer);

        $service = app(TaskAssignmentService::class);
        $service->start($assignment, $this->volunteer);

        return $service->submit($assignment, $this->volunteer, [
            'note' => 'Watered rows A through D.',
            'hours_spent' => 3.5,
            'latitude' => 34.5554,
            'longitude' => 69.0431,
            'gps_accuracy' => 8,
        ]);
    }

    /* ══════════════ ACCESS ══════════════ */

    public function test_the_queue_is_closed_to_a_volunteer(): void
    {
        $this->actingAs($this->volunteer)
            ->get('/admin/task-submissions')
            ->assertForbidden();
    }

    public function test_a_reviewer_can_open_the_queue(): void
    {
        $this->submitted();

        $this->actingAs($this->reviewer)
            ->get('/admin/task-submissions')
            ->assertOk();
    }

    public function test_the_queue_shows_pending_work_oldest_first(): void
    {
        $first = $this->submitted(['title' => 'Older']);
        $second = $this->submitted(['title' => 'Newer']);

        $first->forceFill(['created_at' => now()->subDays(3)])->save();

        Livewire::actingAs($this->reviewer)
            ->test(ListTaskSubmissions::class)
            ->assertCanSeeTableRecords([$first, $second], inOrder: true);
    }

    /* ══════════════ THE EVIDENCE ══════════════ */

    public function test_the_review_page_shows_the_task_the_volunteer_and_the_note(): void
    {
        $submission = $this->submitted();

        Livewire::actingAs($this->reviewer)
            ->test(ReviewSubmission::class, ['record' => $submission->id])
            ->assertOk()
            ->assertSee('Water the saplings at Qargha')
            ->assertSee($submission->task->reference)
            ->assertSee('Ahmad')
            ->assertSee('Watered rows A through D.');
    }

    public function test_the_review_page_shows_the_gps_verdict(): void
    {
        $submission = $this->submitted();

        Livewire::actingAs($this->reviewer)
            ->test(ReviewSubmission::class, ['record' => $submission->id])
            ->assertSee('On site')
            ->assertSee('34.55540');
    }

    public function test_an_off_site_submission_is_flagged_without_accusing(): void
    {
        // Geo check off: this test is about how the review page *presents* an
        // off-site reading. A task that enforces the radius would never let one
        // be recorded in the first place — that path is GpsVerificationTest's.
        $task = Task::create([
            'title' => 'Far away', 'created_by' => $this->reviewer->id,
            'requires_photo' => false, 'requires_geo_check' => false,
            'latitude' => 34.5553, 'longitude' => 69.0430, 'radius' => 150,
        ]);

        $assignment = $task->assign($this->volunteer, $this->reviewer);
        $service = app(TaskAssignmentService::class);
        $service->start($assignment, $this->volunteer);

        $submission = $service->submit($assignment, $this->volunteer, [
            'note' => 'Done', 'latitude' => 34.5800, 'longitude' => 69.0900,
        ]);

        $this->assertFalse($submission->is_within_geofence);

        Livewire::actingAs($this->reviewer)
            ->test(ReviewSubmission::class, ['record' => $submission->id])
            ->assertSee('Off site')
            // The wording matters: GPS drift is real, and the page must not
            // present distance as proof of dishonesty.
            ->assertSee('GPS can drift');
    }

    public function test_the_review_page_shows_the_timeline(): void
    {
        $submission = $this->submitted();

        Livewire::actingAs($this->reviewer)
            ->test(ReviewSubmission::class, ['record' => $submission->id])
            ->assertSee('Timeline')
            ->assertSee('Assigned')
            ->assertSee('Started')
            ->assertSee('Submitted');
    }

    public function test_the_review_page_shows_every_kind_of_attachment(): void
    {
        $submission = $this->submitted();

        TaskAttachment::storeFor($submission, UploadedFile::fake()->image('proof.jpg'), $this->volunteer);
        TaskAttachment::storeFor($submission, UploadedFile::fake()->create('clip.mp4', 900, 'video/mp4'), $this->volunteer);
        TaskAttachment::storeFor($submission, UploadedFile::fake()->create('plan.pdf', 40, 'application/pdf'), $this->volunteer);
        TaskAttachment::storeFor($submission, UploadedFile::fake()->create('note.mp3', 60, 'audio/mpeg'), $this->volunteer);

        Livewire::actingAs($this->reviewer)
            ->test(ReviewSubmission::class, ['record' => $submission->id])
            ->assertSee('Photos (1)')
            ->assertSee('Videos (1)')
            ->assertSee('Documents (1)')
            ->assertSee('Audio (1)')
            ->assertSee('plan.pdf');
    }

    public function test_the_review_page_shows_progress_history(): void
    {
        $submission = $this->submitted();

        $submission->assignment->reportProgress(40, $this->volunteer, 'Half the row done');

        Livewire::actingAs($this->reviewer)
            ->test(ReviewSubmission::class, ['record' => $submission->id])
            ->assertSee('Progress history')
            ->assertSee('Half the row done');
    }

    public function test_the_review_page_shows_comments_including_internal_notes(): void
    {
        $submission = $this->submitted();
        $comments = app(TaskCommentService::class);

        $comments->post($submission->task, $this->volunteer, 'Which row did you mean?');
        $comments->post($submission->task, $this->reviewer, 'Weak photos, watch this one.', internal: true);

        Livewire::actingAs($this->reviewer)
            ->test(ReviewSubmission::class, ['record' => $submission->id])
            ->assertSee('Which row did you mean?')
            // Staff see internal notes; the API strips them for volunteers.
            ->assertSee('Weak photos, watch this one.')
            ->assertSee('staff only');
    }

    public function test_earlier_attempts_are_available_for_comparison(): void
    {
        $submission = $this->submitted();
        $assignment = $submission->assignment;

        app(\App\Services\Tasks\TaskReviewService::class)->record(
            assignment: $assignment,
            status: TaskReviewStatus::NEEDS_REVISION,
            reviewer: $this->reviewer,
            comments: 'Photos were too dark.',
        );

        $retry = app(TaskAssignmentService::class)->submit(
            $assignment->fresh(),
            $this->volunteer,
            // On site, as the fixture task requires — the resubmission has to
            // clear the geofence just as the first attempt did.
            ['note' => 'Re-shot in daylight.', 'latitude' => 34.5554, 'longitude' => 69.0431, 'gps_accuracy' => 8],
        );

        Livewire::actingAs($this->reviewer)
            ->test(ReviewSubmission::class, ['record' => $retry->id])
            ->assertSee('Earlier attempts')
            ->assertSee('Photos were too dark.');
    }

    /* ══════════════ THE VERDICT ══════════════ */

    public function test_approving_records_score_rating_and_comment_and_notifies(): void
    {
        $submission = $this->submitted();

        Livewire::actingAs($this->reviewer)
            ->test(ReviewSubmission::class, ['record' => $submission->id])
            ->callAction('approve', ['score' => 92.5, 'rating' => 5, 'comments' => 'Excellent work.']);

        $review = $submission->fresh()->reviews()->first();

        $this->assertNotNull($review);
        $this->assertSame(TaskReviewStatus::APPROVED, $review->review_status);
        $this->assertSame('92.50', (string) $review->score);
        $this->assertSame(5, $review->rating);
        $this->assertSame($this->reviewer->id, $review->reviewed_by);

        // The whole tree moved, not just the review row.
        $this->assertSame(TaskSubmissionStatus::APPROVED, $submission->fresh()->status);
        $this->assertSame(TaskAssignmentStatus::APPROVED, $submission->assignment->fresh()->status);
        $this->assertSame(TaskStatus::APPROVED, $submission->task->fresh()->status);

        $this->assertDatabaseHas('task_notifications', [
            'user_id' => $this->volunteer->id,
            'type' => 'task.approved',
        ]);
    }

    public function test_a_rejection_requires_an_explanation(): void
    {
        $submission = $this->submitted();

        Livewire::actingAs($this->reviewer)
            ->test(ReviewSubmission::class, ['record' => $submission->id])
            ->callAction('reject', ['score' => null, 'rating' => 1, 'comments' => ''])
            ->assertHasActionErrors(['comments' => 'required']);

        // Nothing was recorded by the refused attempt.
        $this->assertSame(0, $submission->fresh()->reviews()->count());
    }

    public function test_requesting_a_revision_is_distinct_from_rejecting(): void
    {
        $submission = $this->submitted();

        Livewire::actingAs($this->reviewer)
            ->test(ReviewSubmission::class, ['record' => $submission->id])
            ->callAction('request_revision', [
                'score' => 60,
                'rating' => 3,
                'comments' => 'Rows C and D were missed.',
            ]);

        $this->assertSame(TaskSubmissionStatus::NEEDS_REVISION, $submission->fresh()->status);

        // A revision request must not be filed, or notified, as a rejection.
        $this->assertDatabaseHas('task_notifications', [
            'user_id' => $this->volunteer->id,
            'type' => 'task.needs_revision',
        ]);
        $this->assertDatabaseMissing('task_notifications', [
            'user_id' => $this->volunteer->id,
            'type' => 'task.rejected',
        ]);

        $notification = TaskNotification::where('type', 'task.needs_revision')->first();
        $this->assertStringContainsString('Rows C and D were missed.', $notification->body);
    }

    public function test_rejecting_notifies_the_volunteer_with_the_reason(): void
    {
        $submission = $this->submitted();

        Livewire::actingAs($this->reviewer)
            ->test(ReviewSubmission::class, ['record' => $submission->id])
            ->callAction('reject', ['score' => 10, 'rating' => 1, 'comments' => 'The site was not visited.']);

        $this->assertSame(TaskSubmissionStatus::REJECTED, $submission->fresh()->status);

        $notification = TaskNotification::where('user_id', $this->volunteer->id)
            ->where('type', 'task.rejected')
            ->first();

        $this->assertNotNull($notification);
        $this->assertStringContainsString('The site was not visited.', $notification->body);
    }

    public function test_an_out_of_range_score_is_refused_by_the_form(): void
    {
        $submission = $this->submitted();

        // Two layers guard this. The form rejects it here, so the reviewer is
        // told rather than silently having their input rewritten; the model
        // clamp (covered in the model suite) is the second line of defence for
        // any caller that bypasses the form.
        Livewire::actingAs($this->reviewer)
            ->test(ReviewSubmission::class, ['record' => $submission->id])
            ->callAction('approve', ['score' => 250, 'rating' => 9, 'comments' => null])
            ->assertHasActionErrors(['score', 'rating']);

        $this->assertSame(0, $submission->fresh()->reviews()->count());
    }

    public function test_a_reviewed_submission_offers_no_further_verdict(): void
    {
        $submission = $this->submitted();

        Livewire::actingAs($this->reviewer)
            ->test(ReviewSubmission::class, ['record' => $submission->id])
            ->callAction('approve', ['score' => null, 'rating' => null, 'comments' => null]);

        Livewire::actingAs($this->reviewer)
            ->test(ReviewSubmission::class, ['record' => $submission->fresh()->id])
            ->assertActionHidden('approve')
            ->assertSee('Already reviewed');
    }

    /* ══════════════ COMMENTS ══════════════ */

    public function test_a_reviewer_can_post_a_comment_and_the_volunteer_is_told(): void
    {
        $submission = $this->submitted();

        Livewire::actingAs($this->reviewer)
            ->test(ReviewSubmission::class, ['record' => $submission->id])
            ->callAction('comment', ['body' => 'Which row is in the second photo?', 'internal' => false]);

        $this->assertDatabaseHas('task_comments', [
            'task_id' => $submission->task_id,
            'body' => 'Which row is in the second photo?',
            'is_internal' => false,
        ]);

        $this->assertDatabaseHas('task_notifications', [
            'user_id' => $this->volunteer->id,
            'type' => 'task.commented',
        ]);
    }

    public function test_an_internal_note_never_notifies_the_volunteer(): void
    {
        $submission = $this->submitted();

        Livewire::actingAs($this->reviewer)
            ->test(ReviewSubmission::class, ['record' => $submission->id])
            ->callAction('comment', ['body' => 'Second opinion needed.', 'internal' => true]);

        $this->assertDatabaseHas('task_comments', [
            'body' => 'Second opinion needed.',
            'is_internal' => true,
        ]);

        // The whole point of an internal note is that the volunteer never sees it.
        $this->assertDatabaseMissing('task_notifications', [
            'user_id' => $this->volunteer->id,
            'type' => 'task.commented',
        ]);
    }

    /* ══════════════ AUTHORISATION ══════════════ */

    public function test_a_volunteer_cannot_open_the_review_page_for_their_own_work(): void
    {
        $submission = $this->submitted();

        // Asserted over HTTP rather than through Livewire: the guard is an
        // abort() in mount(), and a real request is what proves the route is
        // actually closed rather than only the component.
        $this->actingAs($this->volunteer)
            ->get("/admin/task-submissions/{$submission->id}/review")
            ->assertForbidden();
    }
}
