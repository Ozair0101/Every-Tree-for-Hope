<?php

namespace App\Jobs\Push;

use App\Models\PushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

/**
 * Safety net for pushes whose send job never ran.
 *
 * The outbox and the queue are two different systems, and they can disagree: a
 * worker killed mid-job, a Redis flush, a deploy that cleared the queue table.
 * When that happens the row sits `queued` forever and the volunteer simply never
 * hears anything — a silent failure, which is the worst kind.
 *
 * This re-dispatches anything that has been waiting longer than it plausibly
 * should. It exists precisely because the outbox is the record of intent and the
 * queue is only the mechanism; the record is what must not be lost.
 */
class FlushStuckPushNotificationsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Grace period before a queued row is considered stranded.
     *
     * Long enough that a backed-up queue is not mistaken for a broken one —
     * re-dispatching a job that is merely waiting its turn would double-send.
     */
    private const STUCK_AFTER_MINUTES = 30;

    /** Ceiling per run, so a genuine outage does not produce a thundering herd. */
    private const BATCH = 500;

    public function __construct()
    {
        $this->onQueue('push');
    }

    public function handle(): void
    {
        $stranded = PushNotification::query()
            ->where('status', PushNotification::STATUS_QUEUED)
            ->where('created_at', '<', now()->subMinutes(self::STUCK_AFTER_MINUTES))
            // Rows that already burned their retries are failures, not strays;
            // re-dispatching them would loop forever.
            ->where('attempts', '<', 3)
            ->orderBy('id')
            ->limit(self::BATCH)
            ->pluck('id');

        if ($stranded->isEmpty()) {
            return;
        }

        foreach ($stranded as $id) {
            SendPushNotificationJob::dispatch($id);
        }

        Log::warning('[push] Re-dispatched stranded deliveries.', ['count' => $stranded->count()]);
    }
}
