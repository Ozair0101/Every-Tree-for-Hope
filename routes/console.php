<?php

use App\Jobs\Push\FlushStuckPushNotificationsJob;
use App\Jobs\Tasks\SendDueTomorrowRemindersJob;
use App\Models\PushNotification;
use App\Models\TaskActivityLog;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Task management — scheduled work
|--------------------------------------------------------------------------
|
| Requires the scheduler to be running on the server:
|
|   * * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
|
| …and at least one queue worker, since everything below only enqueues:
|
|   php artisan queue:work --queue=push,notifications,default
|
| Queue order matters: `push` first, so a device delivery is never stuck behind
| a slow fan-out job.
|
*/

/*
 | Due-tomorrow reminders.
 |
 | 08:00 rather than midnight: a reminder that lands while the volunteer is
 | asleep is buried under everything that arrives after it, and the point is to
 | reach them at the start of the day *before* the deadline, while there is
 | still time to act on it.
 |
 | withoutOverlapping because the sweep can outlive a minute on a large table,
 | and onOneServer so a multi-server deployment does not send it twice.
 */
Schedule::job(new SendDueTomorrowRemindersJob)
    ->dailyAt('08:00')
    ->timezone(config('app.timezone'))
    ->withoutOverlapping()
    ->onOneServer()
    ->name('task-due-tomorrow-reminders');

/*
 | Safety net for deliveries whose send job was lost — a killed worker, a
 | flushed queue, a deploy mid-flight. Hourly is often enough to matter and rare
 | enough to cost nothing when there is nothing to do.
 */
Schedule::job(new FlushStuckPushNotificationsJob)
    ->hourly()
    ->onOneServer()
    ->name('flush-stuck-push-notifications');

/*
 | Retention. Both models declare their own windows and their own exclusions —
 | see TaskActivityLog::prunable() and PushNotification::prunable(). Run in the
 | small hours because a large prune holds row locks.
 */
Schedule::command('model:prune', [
    '--model' => [TaskActivityLog::class, PushNotification::class],
])->dailyAt('03:30')->onOneServer()->name('prune-task-logs');
