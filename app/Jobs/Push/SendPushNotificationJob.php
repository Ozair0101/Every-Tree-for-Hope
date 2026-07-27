<?php

namespace App\Jobs\Push;

use App\Models\PushNotification;
use App\Services\Push\PushDispatcher;
use App\Services\Push\PushResult;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

/**
 * Delivers one queued push row to one device.
 *
 * One job per row rather than one per notification: a user with three phones
 * gets three independent attempts, so a single dead token cannot stop the other
 * two, and each retry only re-sends the message that actually failed.
 *
 * The job is deliberately dumb about *whether* to send. That decision — the
 * user's preferences, the duplicate guard — was made when the row was written.
 * By the time this runs the answer is already yes, which keeps the retry path
 * from silently re-evaluating a rule and dropping a message on attempt three.
 */
class SendPushNotificationJob implements ShouldQueue
{
    use Queueable;

    /**
     * Three attempts, backing off in minutes.
     *
     * A push whose moment has passed is worth less than the queue slot it
     * occupies, so this gives up rather than retrying for hours: the inbox entry
     * is already there, and the user will see it when they next open the app.
     */
    public int $tries = 3;

    /** @var array<int, int> seconds between attempts */
    public array $backoff = [60, 300];

    public function __construct(
        public readonly int $pushNotificationId,
    ) {
        $this->onQueue('push');
    }

    public function handle(PushDispatcher $dispatcher): void
    {
        $push = PushNotification::with('pushToken')->find($this->pushNotificationId);

        // The row can legitimately be gone: the task was purged, or the
        // notification was pruned while this sat in the queue.
        if ($push === null || $push->status !== PushNotification::STATUS_QUEUED) {
            return;
        }

        $token = $push->pushToken;

        if ($token === null || ! $token->is_active) {
            $push->forceFill([
                'status' => PushNotification::STATUS_SKIPPED,
                'error_code' => 'NO_ACTIVE_DEVICE',
            ])->save();

            return;
        }

        $result = $dispatcher->send(
            token: $token,
            title: $push->title,
            body: $push->body,
            data: $push->data ?? [],
        );

        $this->record($push, $token, $result);
    }

    private function record(PushNotification $push, $token, PushResult $result): void
    {
        if ($result->successful) {
            $push->markSent($result->messageId);
            $token->recordSuccess();

            return;
        }

        if ($result->tokenIsDead) {
            // Retire the device and stop. Retrying a token FCM has told us is
            // gone is a call that can only ever fail.
            $token->recordFailure($result->errorCode);
            $push->markFailed($result->errorCode, $result->errorMessage);

            return;
        }

        if ($result->retryable && $this->attempts() < $this->tries) {
            // Left `queued` on purpose: the row is still owed a delivery, and
            // leaving it in that state means the hourly sweep would pick it up
            // even if the queue itself were lost.
            $push->increment('attempts');

            $this->release($this->backoff[$this->attempts() - 1] ?? 300);

            return;
        }

        $token->recordFailure($result->errorCode);
        $push->markFailed($result->errorCode, $result->errorMessage);

        Log::info('[push] Giving up on a delivery.', [
            'push_notification_id' => $push->id,
            'error' => $result->errorCode,
        ]);
    }

    /** Fired when the queue gives up entirely — record it rather than lose it. */
    public function failed(\Throwable $exception): void
    {
        PushNotification::whereKey($this->pushNotificationId)
            ->where('status', PushNotification::STATUS_QUEUED)
            ->update([
                'status' => PushNotification::STATUS_FAILED,
                'error_code' => 'JOB_FAILED',
                'error_message' => $exception->getMessage(),
            ]);
    }
}
