<?php

namespace App\Services\Push;

/**
 * The outcome of one delivery attempt.
 *
 * A value object rather than a bare bool, because "it failed" is not enough to
 * act on: a dead token must retire the device, a rate-limit must be retried
 * later, and a malformed payload must not be retried at all. The caller needs
 * to tell those apart without parsing an error string.
 */
class PushResult
{
    private function __construct(
        public readonly bool $successful,
        public readonly ?string $messageId = null,
        public readonly ?string $errorCode = null,
        public readonly ?string $errorMessage = null,
        public readonly bool $tokenIsDead = false,
        public readonly bool $retryable = false,
    ) {}

    public static function success(?string $messageId = null): self
    {
        return new self(successful: true, messageId: $messageId);
    }

    /**
     * The device is gone — app uninstalled, or the token was replaced.
     *
     * Never retried, and the token is retired: every further send to it is a
     * wasted call.
     */
    public static function deadToken(string $code, ?string $message = null): self
    {
        return new self(
            successful: false,
            errorCode: $code,
            errorMessage: $message,
            tokenIsDead: true,
        );
    }

    /** A transient fault — FCM unavailable, rate limited, network dropped. */
    public static function retryable(string $code, ?string $message = null): self
    {
        return new self(
            successful: false,
            errorCode: $code,
            errorMessage: $message,
            retryable: true,
        );
    }

    /**
     * Our fault: a malformed payload or bad credentials.
     *
     * Not retryable — the same request will fail identically in five minutes,
     * and retrying only hides the bug behind a slow queue.
     */
    public static function permanentFailure(string $code, ?string $message = null): self
    {
        return new self(successful: false, errorCode: $code, errorMessage: $message);
    }

    /** No transport is configured, so nothing was attempted. */
    public static function skipped(string $reason): self
    {
        return new self(successful: false, errorCode: 'NOT_CONFIGURED', errorMessage: $reason);
    }
}
