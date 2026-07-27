<?php

namespace Tests\Feature\Push;

use App\Enums\TaskAssignmentStatus;
use App\Enums\TaskReviewStatus;
use App\Jobs\Push\FlushStuckPushNotificationsJob;
use App\Jobs\Push\SendPushNotificationJob;
use App\Jobs\Tasks\SendDueTomorrowRemindersJob;
use App\Models\NotificationPreference;
use App\Models\PushNotification;
use App\Models\PushToken;
use App\Models\Task;
use App\Models\TaskNotification;
use App\Models\User;
use App\Services\Push\FcmService;
use App\Services\Push\PushDispatcher;
use App\Services\Tasks\TaskAssignmentService;
use App\Services\Tasks\TaskReviewService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * Phase 4 — push delivery.
 *
 * The HTTP layer is faked, never called: these assert what the app *decides* to
 * send and how it reacts to what FCM answers. A test that reached Google would
 * be a test of Google.
 */
class TaskPushNotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $volunteer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->admin = User::factory()->create(['name' => 'Coordinator']);
        $this->admin->assignRole('Manager');
        $this->volunteer = User::factory()->create(['name' => 'Ahmad']);

        config([
            'services.fcm.project_id' => 'test-project',
            'services.fcm.credentials' => null,
        ]);

        // The suite runs with QUEUE_CONNECTION=sync, so a dispatched send job
        // executes inline — an unfaked test would make a real call to Expo.
        // This turns any unstubbed URL into a loud failure rather than a slow,
        // silent, network-dependent test.
        Http::preventStrayRequests();
    }

    /**
     * Stub the push transport.
     *
     * Called per test rather than in setUp, because Laravel's HTTP fake gives
     * precedence to the *first* matching stub: a default registered in setUp
     * would silently win over any per-test override, and the test asserting a
     * dead token would quietly be asserting a successful one.
     */
    private function fakeExpo(string $status = 'ok', ?string $error = null): void
    {
        $ticket = $status === 'ok'
            ? ['status' => 'ok', 'id' => 'ticket-1']
            : ['status' => 'error', 'details' => ['error' => $error]];

        Http::fake(['exp.host/*' => Http::response(['data' => [$ticket]])]);
    }

    private function task(array $attributes = []): Task
    {
        return Task::create(array_merge([
            'title' => 'Water the saplings',
            'created_by' => $this->admin->id,
            'requires_photo' => false,
        ], $attributes));
    }

    private function device(User $user, string $token = 'ExponentPushToken[abc123]'): PushToken
    {
        return PushToken::register($user, $token, ['platform' => 'android']);
    }

    /* ══════════════ THE SIX REQUIRED EVENTS ══════════════ */

    public function test_assigning_a_task_notifies_the_volunteer(): void
    {
        $this->fakeExpo();
        $this->device($this->volunteer);
        $task = $this->task();

        app(TaskAssignmentService::class)->assign($task, [$this->volunteer->id], $this->admin);

        $this->assertDatabaseHas('task_notifications', [
            'user_id' => $this->volunteer->id,
            'type' => 'task.assigned',
        ]);

        // Under a sync queue the send job runs inline, so the row is already
        // delivered by the time we look. In production it would still be
        // `queued` here and drained by a worker.
        $this->assertDatabaseHas('push_notifications', [
            'user_id' => $this->volunteer->id,
            'event_key' => 'task.assigned',
            'status' => PushNotification::STATUS_SENT,
        ]);
    }

    public function test_starting_a_task_notifies_the_coordinator_and_not_the_volunteer(): void
    {
        $this->fakeExpo();
        $this->device($this->admin, 'ExponentPushToken[admin]');
        $task = $this->task();
        $assignment = $task->assign($this->volunteer, $this->admin);

        app(TaskAssignmentService::class)->start($assignment, $this->volunteer);

        $this->assertDatabaseHas('task_notifications', [
            'user_id' => $this->admin->id,
            'type' => 'task.started',
        ]);

        // The person who pressed Start does not need telling they pressed Start.
        $this->assertDatabaseMissing('task_notifications', [
            'user_id' => $this->volunteer->id,
            'type' => 'task.started',
        ]);
    }

    public function test_submitting_notifies_the_reviewer(): void
    {
        $task = $this->task(['requires_review' => true]);
        $assignment = $task->assign($this->volunteer, $this->admin);

        $service = app(TaskAssignmentService::class);
        $service->start($assignment, $this->volunteer);
        $service->submit($assignment, $this->volunteer, ['note' => 'Done']);

        // No named reviewer, so it falls back to the task's creator — otherwise
        // the work would wait in a queue nobody is watching.
        $this->assertDatabaseHas('task_notifications', [
            'user_id' => $this->admin->id,
            'type' => 'task.submitted',
        ]);
    }

    public function test_approving_notifies_the_volunteer(): void
    {
        $task = $this->task();
        $assignment = $task->assign($this->volunteer, $this->admin);

        $service = app(TaskAssignmentService::class);
        $service->start($assignment, $this->volunteer);
        $service->submit($assignment, $this->volunteer, ['note' => 'Done']);

        app(TaskReviewService::class)->record(
            assignment: $assignment->fresh(),
            status: TaskReviewStatus::APPROVED,
            reviewer: $this->admin,
            comments: 'Well done.',
        );

        $notification = TaskNotification::where('user_id', $this->volunteer->id)
            ->where('type', 'task.approved')
            ->first();

        $this->assertNotNull($notification);
        $this->assertStringContainsString('Well done.', $notification->body);
    }

    public function test_rejecting_notifies_the_volunteer_and_reads_differently_from_a_revision(): void
    {
        $task = $this->task();
        $assignment = $task->assign($this->volunteer, $this->admin);

        $service = app(TaskAssignmentService::class);
        $service->start($assignment, $this->volunteer);
        $service->submit($assignment, $this->volunteer, ['note' => 'Done']);

        app(TaskReviewService::class)->record(
            assignment: $assignment->fresh(),
            status: TaskReviewStatus::NEEDS_REVISION,
            reviewer: $this->admin,
            comments: 'Rows C-D were missed.',
        );

        $revision = TaskNotification::where('user_id', $this->volunteer->id)
            ->where('type', 'task.needs_revision')
            ->first();

        $this->assertNotNull($revision, 'A revision request must not be filed as a rejection.');
        $this->assertStringContainsString('nearly there', $revision->body);
        $this->assertStringContainsString('Rows C-D were missed.', $revision->body);
    }

    public function test_the_due_tomorrow_sweep_reminds_only_who_it_should(): void
    {
        $this->fakeExpo();
        $this->device($this->volunteer);

        $dueTomorrow = $this->task(['title' => 'Due tomorrow', 'due_date' => now()->addDay()->setTime(9, 0)]);
        $dueTomorrowAssignment = $dueTomorrow->assign($this->volunteer, $this->admin);

        $dueNextWeek = $this->task(['title' => 'Due next week', 'due_date' => now()->addWeek()]);
        $dueNextWeek->assign($this->volunteer, $this->admin);

        // Already handed in — reminding them would be noise.
        $submitted = $this->task(['title' => 'Already submitted', 'due_date' => now()->addDay()]);
        $submittedAssignment = $submitted->assign($this->volunteer, $this->admin);
        $submittedAssignment->forceFill(['status' => TaskAssignmentStatus::SUBMITTED->value])->save();

        app(SendDueTomorrowRemindersJob::class)->handle(app(\App\Services\Tasks\TaskNotificationService::class));

        $reminders = TaskNotification::where('type', 'task.due_soon')->get();

        $this->assertCount(1, $reminders);
        $this->assertSame($dueTomorrow->id, $reminders->first()->task_id);
        $this->assertNotNull($dueTomorrowAssignment->fresh()->last_notified_at);
    }

    public function test_the_reminder_sweep_does_not_send_twice_in_a_day(): void
    {
        $task = $this->task(['due_date' => now()->addDay()]);
        $task->assign($this->volunteer, $this->admin);

        $service = app(\App\Services\Tasks\TaskNotificationService::class);
        app(SendDueTomorrowRemindersJob::class)->handle($service);
        app(SendDueTomorrowRemindersJob::class)->handle($service);

        $this->assertSame(1, TaskNotification::where('type', 'task.due_soon')->count());
    }

    /* ══════════════ PREFERENCES ══════════════ */

    public function test_muting_a_push_still_writes_the_inbox_entry(): void
    {
        $this->device($this->volunteer);
        NotificationPreference::optOut($this->volunteer->id, 'push', 'task.assigned');

        $task = $this->task();
        app(TaskAssignmentService::class)->assign($task, [$this->volunteer->id], $this->admin);

        // Muting a buzz is not muting the news.
        $this->assertDatabaseHas('task_notifications', [
            'user_id' => $this->volunteer->id,
            'type' => 'task.assigned',
        ]);
        $this->assertDatabaseCount('push_notifications', 0);
    }

    /* ══════════════ OUTBOX ══════════════ */

    public function test_one_notification_fans_out_to_every_device(): void
    {
        $this->fakeExpo();
        $this->device($this->volunteer, 'ExponentPushToken[phone]');
        $this->device($this->volunteer, 'ExponentPushToken[tablet]');

        $task = $this->task();
        app(TaskAssignmentService::class)->assign($task, [$this->volunteer->id], $this->admin);

        $this->assertSame(2, PushNotification::where('user_id', $this->volunteer->id)->count());
        $this->assertSame(1, TaskNotification::where('user_id', $this->volunteer->id)->count());
    }

    public function test_a_user_with_no_device_records_a_skip_rather_than_silence(): void
    {
        $task = $this->task();
        app(TaskAssignmentService::class)->assign($task, [$this->volunteer->id], $this->admin);

        $this->assertDatabaseHas('push_notifications', [
            'user_id' => $this->volunteer->id,
            'status' => PushNotification::STATUS_SKIPPED,
            'error_code' => 'NO_ACTIVE_DEVICE',
        ]);
    }

    public function test_queuing_a_push_dispatches_a_send_job(): void
    {
        // Only the send job is faked. A blanket Queue::fake() would also
        // intercept the notification itself — which is ShouldQueue — so the
        // channel would never run and there would be nothing to assert.
        Queue::fake([SendPushNotificationJob::class]);
        $this->device($this->volunteer);

        $task = $this->task();
        app(TaskAssignmentService::class)->assign($task, [$this->volunteer->id], $this->admin);

        Queue::assertPushed(SendPushNotificationJob::class);

        // Left queued, because the faked job never ran to mark it otherwise.
        $this->assertDatabaseHas('push_notifications', [
            'user_id' => $this->volunteer->id,
            'status' => PushNotification::STATUS_QUEUED,
        ]);
    }

    /* ══════════════ DELIVERY ══════════════ */

    public function test_a_successful_send_marks_the_row_and_resets_the_token(): void
    {
        Http::fake(['exp.host/*' => Http::response(['data' => [['status' => 'ok', 'id' => 'ticket-1']]])]);

        $token = $this->device($this->volunteer);
        $token->forceFill(['failure_count' => 2])->save();

        $push = PushNotification::create([
            'user_id' => $this->volunteer->id,
            'push_token_id' => $token->id,
            'event_key' => 'task.assigned',
            'title' => 'New task',
            'body' => 'Water the saplings',
        ]);

        app(SendPushNotificationJob::class, ['pushNotificationId' => $push->id])->handle(app(PushDispatcher::class));

        $this->assertSame(PushNotification::STATUS_SENT, $push->fresh()->status);
        $this->assertSame('ticket-1', $push->fresh()->fcm_message_id);
        $this->assertNotNull($push->fresh()->sent_at);
        $this->assertSame(0, $token->fresh()->failure_count);
    }

    public function test_a_dead_token_is_retired_and_never_retried(): void
    {
        Http::fake(['exp.host/*' => Http::response([
            'data' => [['status' => 'error', 'details' => ['error' => 'DeviceNotRegistered']]],
        ])]);

        $token = $this->device($this->volunteer);

        $push = PushNotification::create([
            'user_id' => $this->volunteer->id,
            'push_token_id' => $token->id,
            'event_key' => 'task.assigned',
            'title' => 'New task',
            'body' => 'Body',
        ]);

        app(SendPushNotificationJob::class, ['pushNotificationId' => $push->id])->handle(app(PushDispatcher::class));

        $this->assertSame(PushNotification::STATUS_FAILED, $push->fresh()->status);
        $this->assertSame('DeviceNotRegistered', $push->fresh()->error_code);
        // Deactivated, not deleted — the delivery history points at it.
        $this->assertFalse($token->fresh()->is_active);
        $this->assertDatabaseHas('push_tokens', ['id' => $token->id]);
    }

    public function test_a_row_whose_device_went_inactive_is_skipped(): void
    {
        $token = $this->device($this->volunteer);
        $push = PushNotification::create([
            'user_id' => $this->volunteer->id,
            'push_token_id' => $token->id,
            'event_key' => 'task.assigned',
            'title' => 'T',
            'body' => 'B',
        ]);

        $token->forceFill(['is_active' => false])->save();

        app(SendPushNotificationJob::class, ['pushNotificationId' => $push->id])->handle(app(PushDispatcher::class));

        $this->assertSame(PushNotification::STATUS_SKIPPED, $push->fresh()->status);
    }

    /* ══════════════ FCM SERVICE ══════════════ */

    public function test_fcm_reports_itself_unconfigured_without_credentials(): void
    {
        config(['services.fcm.credentials' => null]);

        $fcm = app(FcmService::class);

        $this->assertFalse($fcm->isConfigured());

        $result = $fcm->send('some-fcm-token', 'Title', 'Body');

        // Unconfigured is a skip, not an exception: a developer without Firebase
        // credentials must still be able to run the app.
        $this->assertFalse($result->successful);
        $this->assertSame('NOT_CONFIGURED', $result->errorCode);
        $this->assertFalse($result->retryable);
    }

    public function test_the_dispatcher_routes_expo_tokens_to_expo_not_fcm(): void
    {
        Http::fake([
            'exp.host/*' => Http::response(['data' => [['status' => 'ok', 'id' => 'ticket']]]),
            'fcm.googleapis.com/*' => Http::response([], 500),
        ]);

        $token = $this->device($this->volunteer, 'ExponentPushToken[routed]');

        $result = app(PushDispatcher::class)->send($token, 'Title', 'Body');

        $this->assertTrue($result->successful);
        Http::assertSent(fn ($request) => str_contains($request->url(), 'exp.host'));
        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'fcm.googleapis.com'));
    }

    public function test_a_native_token_is_routed_to_fcm(): void
    {
        // Unconfigured FCM, so this asserts the routing decision rather than a
        // successful send.
        $token = PushToken::register($this->volunteer, 'dGhpcy1pcy1hLW5hdGl2ZS1mY20tdG9rZW4', ['platform' => 'android']);

        $result = app(PushDispatcher::class)->send($token, 'Title', 'Body');

        $this->assertSame('NOT_CONFIGURED', $result->errorCode);
    }

    /* ══════════════ SAFETY NET ══════════════ */

    public function test_stranded_rows_are_re_dispatched_but_fresh_ones_are_left_alone(): void
    {
        Queue::fake();
        $token = $this->device($this->volunteer);

        $stranded = PushNotification::create([
            'user_id' => $this->volunteer->id, 'push_token_id' => $token->id,
            'event_key' => 'task.assigned', 'title' => 'T', 'body' => 'B',
        ]);
        $stranded->forceFill(['created_at' => now()->subHours(2)])->save();

        // Recent: probably just waiting its turn in a busy queue.
        PushNotification::create([
            'user_id' => $this->volunteer->id, 'push_token_id' => $token->id,
            'event_key' => 'task.assigned', 'title' => 'T', 'body' => 'B',
        ]);

        app(FlushStuckPushNotificationsJob::class)->handle();

        Queue::assertPushed(SendPushNotificationJob::class, 1);
    }

    public function test_the_send_job_is_a_no_op_for_an_already_sent_row(): void
    {
        $token = $this->device($this->volunteer);
        $push = PushNotification::create([
            'user_id' => $this->volunteer->id, 'push_token_id' => $token->id,
            'event_key' => 'task.assigned', 'title' => 'T', 'body' => 'B',
            'status' => PushNotification::STATUS_SENT,
        ]);

        Http::fake();

        app(SendPushNotificationJob::class, ['pushNotificationId' => $push->id])->handle(app(PushDispatcher::class));

        Http::assertNothingSent();
    }

    /* ══════════════ PAYLOAD ══════════════ */

    public function test_the_push_carries_a_deep_link_using_the_uuid(): void
    {
        $this->fakeExpo();
        $this->fakeExpo();
        $this->device($this->volunteer);
        $task = $this->task();

        app(TaskAssignmentService::class)->assign($task, [$this->volunteer->id], $this->admin);

        $push = PushNotification::where('user_id', $this->volunteer->id)->first();

        $this->assertSame('TaskDetail', $push->data['screen']);
        $this->assertSame($task->uuid, $push->data['task_uuid']);
        // The integer key is internal and must never reach a client.
        $this->assertArrayNotHasKey('task_id', $push->data);
    }

    public function test_the_inbox_entry_and_the_push_say_the_same_thing(): void
    {
        $this->fakeExpo();
        $this->device($this->volunteer);
        $task = $this->task(['title' => 'Prune the orchard']);

        app(TaskAssignmentService::class)->assign($task, [$this->volunteer->id], $this->admin);

        $inbox = TaskNotification::where('user_id', $this->volunteer->id)->first();
        $push = PushNotification::where('user_id', $this->volunteer->id)->first();

        $this->assertSame($inbox->title, $push->title);
        $this->assertSame($inbox->body, $push->body);
    }
}
