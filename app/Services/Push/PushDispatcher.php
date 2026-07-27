<?php

namespace App\Services\Push;

use App\Models\PushToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Picks the right transport for a device and sends one message to it.
 *
 * ── Why this exists ──────────────────────────────────────────────────────────
 * The requirement is FCM. The React Native client, as built today, registers
 * Expo tokens (`ExponentPushToken[…]`), and FCM cannot accept those — Expo runs
 * its own relay that ultimately hands the message to FCM and APNs. Sending an
 * Expo token to FCM produces an INVALID_ARGUMENT for every push, forever.
 *
 * So the token itself decides the route:
 *
 *   ExponentPushToken[…]   → Expo's push service
 *   anything else          → FCM HTTP v1
 *
 * Both paths return the same {@see PushResult}, so nothing upstream knows or
 * cares which was used. When the app moves to a bare workflow and starts
 * registering native FCM tokens, they will be routed to FCM automatically —
 * no deploy, no migration, and the two can coexist during the changeover while
 * some users are still on the old build.
 */
class PushDispatcher
{
    private const EXPO_ENDPOINT = 'https://exp.host/--/api/v2/push/send';

    public function __construct(
        private readonly FcmService $fcm,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function send(PushToken $token, string $title, string $body, array $data = []): PushResult
    {
        return PushToken::looksValid($token->token)
            ? $this->viaExpo($token->token, $title, $body, $data)
            : $this->fcm->send($token->token, $title, $body, $data);
    }

    /** Is any transport able to send at all? */
    public function isConfigured(): bool
    {
        // Expo needs no credentials, so a project with Expo tokens is always
        // deliverable. FCM only reports ready once a service account is present.
        return true;
    }

    public function fcmIsConfigured(): bool
    {
        return $this->fcm->isConfigured();
    }

    /**
     * Expo's push API.
     *
     * Kept here rather than reusing {@see \App\Notifications\Channels\ExpoPushChannel}
     * because that class sends to *every* device a user owns and swallows the
     * outcome. This module tracks delivery per device in `push_notifications`,
     * so it needs one token in and one verdict out.
     *
     * @param  array<string, mixed>  $data
     */
    private function viaExpo(string $token, string $title, string $body, array $data): PushResult
    {
        try {
            $response = Http::timeout((int) config('services.fcm.timeout', 10))
                ->acceptJson()
                ->post(self::EXPO_ENDPOINT, [[
                    'to' => $token,
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                    'data' => $data,
                    'channelId' => 'default',
                ]]);
        } catch (\Throwable $e) {
            return PushResult::retryable('NETWORK', $e->getMessage());
        }

        if ($response->failed()) {
            // Expo answers 4xx for a malformed request and 5xx when it is
            // struggling; only the latter is worth another attempt.
            return $response->status() >= 500
                ? PushResult::retryable("HTTP_{$response->status()}", $response->body())
                : PushResult::permanentFailure("HTTP_{$response->status()}", $response->body());
        }

        // Expo returns 200 with a per-message ticket, so a "successful" response
        // can still carry a failure. Reading only the status code here would
        // mark dead tokens as delivered forever.
        $ticket = $response->json('data.0') ?? [];

        if (($ticket['status'] ?? null) === 'error') {
            $code = $ticket['details']['error'] ?? 'UNKNOWN';

            Log::info('[push] Expo rejected a message.', ['error' => $code]);

            return $code === 'DeviceNotRegistered'
                ? PushResult::deadToken($code, $ticket['message'] ?? null)
                : PushResult::retryable($code, $ticket['message'] ?? null);
        }

        return PushResult::success($ticket['id'] ?? null);
    }
}
