<?php

namespace App\Services\Push;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Firebase Cloud Messaging, HTTP v1.
 *
 * A reusable transport: it knows how to authenticate with Google and how to put
 * one message on one device. It has no idea what a task is, does not touch the
 * database, and never decides whether a push *should* be sent — that belongs to
 * the notification layer. Anything in the app can use it.
 *
 * ── Authentication ───────────────────────────────────────────────────────────
 * The legacy `Authorization: key=AAAA…` server key was switched off by Google in
 * 2024, so v1 requires an OAuth2 bearer token obtained by signing a JWT with the
 * service account's private key. That flow is implemented here directly with
 * `openssl_sign` rather than by pulling in google/auth: it is about thirty lines,
 * and one fewer dependency in a project that has to be deployable by hand.
 *
 * Access tokens are cached for just under their hour-long life, so a burst of
 * fifty pushes costs one token request rather than fifty.
 *
 * ── Failure ──────────────────────────────────────────────────────────────────
 * Nothing here throws for a delivery problem. Every outcome comes back as a
 * {@see PushResult} the caller can act on, because the three kinds of failure
 * need three different responses: retire the token, retry later, or fix the bug.
 * A push is a courtesy copy of something already written to the user's inbox —
 * it must never take down the request that triggered it.
 */
class FcmService
{
    private const SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';

    private const TOKEN_ENDPOINT = 'https://oauth2.googleapis.com/token';

    private const CACHE_KEY = 'fcm:access_token';

    /**
     * Whether this instance can send at all.
     *
     * Checked by callers before queuing work, so an unconfigured environment
     * records "skipped, not configured" rather than filling the failed-jobs
     * table with something no retry can fix.
     */
    public function isConfigured(): bool
    {
        $credentials = config('services.fcm.credentials');

        return filled(config('services.fcm.project_id'))
            && filled($credentials)
            && is_readable($credentials);
    }

    /**
     * Send one notification to one device token.
     *
     * @param  array<string, mixed>  $data  echoed back to the app on tap
     */
    public function send(string $token, string $title, string $body, array $data = []): PushResult
    {
        if (! $this->isConfigured()) {
            return PushResult::skipped('FCM is not configured (services.fcm.project_id / credentials).');
        }

        $accessToken = $this->accessToken();

        if ($accessToken === null) {
            return PushResult::retryable('AUTH_FAILED', 'Could not obtain an FCM access token.');
        }

        try {
            $response = Http::withToken($accessToken)
                ->timeout((int) config('services.fcm.timeout', 10))
                ->acceptJson()
                ->post($this->endpoint(), ['message' => $this->message($token, $title, $body, $data)]);
        } catch (\Throwable $e) {
            // A dropped connection says nothing about the payload, so it is
            // always worth another attempt.
            return PushResult::retryable('NETWORK', $e->getMessage());
        }

        if ($response->successful()) {
            return PushResult::success($response->json('name'));
        }

        return $this->interpretError($response->status(), $response->json() ?? []);
    }

    /**
     * Send the same notification to many tokens.
     *
     * FCM v1 removed the multicast endpoint that the legacy API had, so this is
     * a loop by necessity. Kept as a method anyway: callers get per-token
     * results in one call, and if Google ever restores batching the change is
     * confined to this method.
     *
     * @param  array<int, string>  $tokens
     * @param  array<string, mixed>  $data
     * @return array<string, PushResult> token => result
     */
    public function sendMany(array $tokens, string $title, string $body, array $data = []): array
    {
        $results = [];

        foreach (array_unique($tokens) as $token) {
            $results[$token] = $this->send($token, $title, $body, $data);
        }

        return $results;
    }

    /*
    |--------------------------------------------------------------------------
    | Payload
    |--------------------------------------------------------------------------
    */

    /**
     * Build the v1 message body.
     *
     * Two deliberate choices:
     *
     * `data` values are all cast to strings. FCM rejects a data payload
     * containing numbers or booleans with a 400 that reads like a schema error,
     * which is a genuinely confusing hour to lose.
     *
     * Both `notification` and `data` are sent. `notification` is what the OS
     * draws while the app is backgrounded; `data` is what the app reads to deep
     * link on tap. Sending only one of them means either no banner or no
     * navigation.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function message(string $token, string $title, string $body, array $data): array
    {
        $stringData = [];

        foreach ($data as $key => $value) {
            if ($value === null) {
                continue;
            }

            $stringData[(string) $key] = is_scalar($value) ? (string) $value : json_encode($value);
        }

        return [
            'token' => $token,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'data' => $stringData,
            'android' => [
                // Task pushes are actionable but not emergencies; `high` wakes
                // the device without the restrictions Android puts on urgent
                // messages sent too often.
                'priority' => 'high',
                'notification' => [
                    'channel_id' => (string) config('services.fcm.android_channel', 'default'),
                    'sound' => 'default',
                    // Collapses older pushes about the same task into one, so a
                    // volunteer who was offline for a day does not wake to
                    // fifteen banners for the same job.
                    'tag' => (string) ($data['task_uuid'] ?? $data['type'] ?? 'task'),
                ],
            ],
            'apns' => [
                'payload' => [
                    'aps' => [
                        'sound' => 'default',
                        // iOS shows no badge unless a number is sent. 1 means
                        // "something is waiting" without the server having to
                        // track an exact per-device count it cannot know.
                        'badge' => 1,
                        // Required for the app to run code on receipt while
                        // backgrounded, which is how the inbox stays in sync.
                        'content-available' => 1,
                    ],
                ],
            ],
        ];
    }

    private function endpoint(): string
    {
        return sprintf(
            'https://fcm.googleapis.com/v1/projects/%s/messages:send',
            config('services.fcm.project_id'),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Error interpretation
    |--------------------------------------------------------------------------
    */

    /**
     * Turn an FCM error into something the caller can act on.
     *
     * @param  array<string, mixed>  $body
     */
    private function interpretError(int $status, array $body): PushResult
    {
        // v1 nests the useful code in error.details[].errorCode; error.status is
        // the coarse gRPC name and is the reliable fallback.
        $code = 'UNKNOWN';

        foreach ($body['error']['details'] ?? [] as $detail) {
            if (isset($detail['errorCode'])) {
                $code = $detail['errorCode'];
                break;
            }
        }

        if ($code === 'UNKNOWN') {
            $code = $body['error']['status'] ?? "HTTP_{$status}";
        }

        $message = $body['error']['message'] ?? null;

        return match (true) {
            // The device is gone. Retiring the token is the only correct action.
            in_array($code, ['UNREGISTERED', 'NOT_FOUND'], true) => PushResult::deadToken($code, $message),
            $status === 404 => PushResult::deadToken('UNREGISTERED', $message),

            // Rate limited or FCM having a bad day: the payload is fine.
            in_array($code, ['QUOTA_EXCEEDED', 'UNAVAILABLE', 'INTERNAL'], true) => PushResult::retryable($code, $message),
            $status === 429 || $status >= 500 => PushResult::retryable($code, $message),

            // Bad credentials are worth one retry — the cached access token may
            // simply have been revoked, and dropping it forces a fresh one.
            $status === 401 || $status === 403 => tap(
                PushResult::retryable($code, $message),
                fn () => Cache::forget(self::CACHE_KEY),
            ),

            // A malformed message or an invalid token string. Retrying an
            // identical request cannot help.
            default => PushResult::permanentFailure($code, $message),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | OAuth2
    |--------------------------------------------------------------------------
    */

    /** A cached bearer token, or null if one could not be obtained. */
    private function accessToken(): ?string
    {
        return Cache::remember(
            self::CACHE_KEY,
            (int) config('services.fcm.token_ttl', 3540),
            fn () => $this->requestAccessToken(),
        );
    }

    private function requestAccessToken(): ?string
    {
        $credentials = $this->credentials();

        if ($credentials === null) {
            return null;
        }

        $assertion = $this->signedAssertion($credentials);

        if ($assertion === null) {
            return null;
        }

        try {
            $response = Http::asForm()
                ->timeout((int) config('services.fcm.timeout', 10))
                ->post(self::TOKEN_ENDPOINT, [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $assertion,
                ]);
        } catch (\Throwable $e) {
            Log::warning('[fcm] Could not reach Google to exchange the JWT.', ['error' => $e->getMessage()]);

            return null;
        }

        if ($response->failed()) {
            Log::error('[fcm] Google rejected the service-account assertion.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return $response->json('access_token');
    }

    /**
     * Build and sign the JWT that Google exchanges for an access token.
     *
     * Standard RS256 service-account flow. `openssl_sign` is used directly to
     * avoid a JWT dependency; the token is short-lived and never leaves this
     * process except to Google.
     *
     * @param  array<string, mixed>  $credentials
     */
    private function signedAssertion(array $credentials): ?string
    {
        $now = time();

        $header = $this->base64Url(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claims = $this->base64Url(json_encode([
            'iss' => $credentials['client_email'],
            'scope' => self::SCOPE,
            'aud' => self::TOKEN_ENDPOINT,
            'iat' => $now,
            // Google caps assertion lifetime at an hour and rejects anything
            // longer outright.
            'exp' => $now + 3600,
        ]));

        $signature = '';

        if (! openssl_sign("{$header}.{$claims}", $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256)) {
            Log::error('[fcm] Could not sign the assertion — is private_key valid PEM?');

            return null;
        }

        return "{$header}.{$claims}.".$this->base64Url($signature);
    }

    /** @return array<string, mixed>|null */
    private function credentials(): ?array
    {
        $path = config('services.fcm.credentials');

        if (blank($path) || ! is_readable($path)) {
            Log::warning('[fcm] Service-account file is missing or unreadable.', ['path' => $path]);

            return null;
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        if (! is_array($decoded) || blank($decoded['client_email'] ?? null) || blank($decoded['private_key'] ?? null)) {
            Log::error('[fcm] Service-account file is not a valid Firebase key.', ['path' => $path]);

            return null;
        }

        return $decoded;
    }

    /** JWT uses base64url, which is base64 with two characters swapped and no padding. */
    private function base64Url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
