<?php

namespace App\Notifications\Channels;

use App\Models\PushToken;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Delivers a notification to Expo's push service.
 *
 * Registered as the `expo` channel, so a notification opts in simply by adding
 * it to `via()`. The payload is taken from the notification's existing
 * `toArray()` — title, body and any extra data — which means the push and the
 * in-app database record can never drift apart in wording.
 *
 * Failure is always swallowed and logged. A push is a courtesy copy of a
 * notification that has already been written to the database; the user will see
 * it in the app regardless, so an unreachable Expo API must never bubble up
 * into the HTTP request that triggered it.
 */
class ExpoPushChannel
{
    /** Expo's public push endpoint. No API key needed for unauthenticated sends. */
    private const ENDPOINT = 'https://exp.host/--/api/v2/push/send';

    /** Expo accepts at most 100 messages per request. */
    private const CHUNK = 100;

    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toArray')) {
            return;
        }

        // Retired tokens are skipped rather than pruned — see pruneDeadTokens.
        $tokens = PushToken::query()
            ->where('user_id', $notifiable->getKey())
            ->active()
            ->pluck('token')
            ->filter(fn (string $t) => PushToken::looksValid($t))
            ->values();

        if ($tokens->isEmpty()) {
            return;
        }

        $payload = $notification->toArray($notifiable);

        $messages = $tokens->map(fn (string $token) => [
            'to' => $token,
            'title' => $payload['title'] ?? config('app.name'),
            'body' => $payload['body'] ?? '',
            // The OS notification sound. Expo maps 'default' to whatever the
            // device is configured to play for notifications, which is what a
            // user expects — a custom sound would override their silent mode
            // preferences on some Android versions.
            'sound' => 'default',
            // Echoed back to the app when the user taps the notification, so it
            // can open the right screen.
            'data' => $payload,
            'channelId' => 'default',
        ])->all();

        foreach (array_chunk($messages, self::CHUNK) as $chunk) {
            $this->post($chunk);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $messages
     */
    private function post(array $messages): void
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post(self::ENDPOINT, $messages);

            if ($response->failed()) {
                Log::warning('[expo-push] Expo rejected the request.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return;
            }

            $this->pruneDeadTokens($messages, $response->json('data') ?? []);
        } catch (\Throwable $e) {
            Log::warning('[expo-push] Could not reach Expo.', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Retire tokens Expo could not deliver to, and reset the failure counter on
     * the ones it accepted.
     *
     * Deactivates rather than deletes: `push_notifications` rows reference these
     * tokens, and a delivery history that disappears when someone wipes their
     * phone is not a delivery history. PushToken::recordFailure() decides when a
     * token is finished — immediately for a permanent error like
     * DeviceNotRegistered, or after FAILURE_THRESHOLD consecutive soft failures.
     *
     * @param  array<int, array<string, mixed>>  $messages
     * @param  array<int, array<string, mixed>>  $tickets
     */
    private function pruneDeadTokens(array $messages, array $tickets): void
    {
        foreach ($tickets as $i => $ticket) {
            $to = $messages[$i]['to'] ?? null;

            if ($to === null) {
                continue;
            }

            $token = PushToken::where('token', $to)->first();

            if (! $token) {
                continue;
            }

            if (($ticket['status'] ?? null) === 'error') {
                $token->recordFailure($ticket['details']['error'] ?? null);

                Log::info('[expo-push] Delivery failed.', [
                    'error' => $ticket['details']['error'] ?? 'unknown',
                    'retired' => ! $token->is_active,
                ]);

                continue;
            }

            $token->recordSuccess();
        }
    }
}
