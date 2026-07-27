<?php

namespace App\Services\Tasks;

use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\User;
use Carbon\CarbonInterface;

/**
 * Deciding whether a location reading can be believed.
 *
 * Two separate questions, deliberately kept apart:
 *
 *   1. Is the volunteer inside the radius?  — a fact, computed by haversine
 *   2. Does the reading look genuine?       — a judgement, from several signals
 *
 * The first can refuse a submission. The second never does: every signal here
 * has an innocent explanation, and an automated system that rejects honest work
 * because a phone's clock drifted is worse than one that flags it for a human.
 * So this returns findings, and a person decides.
 *
 * ── On defeating a determined faker ──────────────────────────────────────────
 * It cannot be done from the server alone. A rooted device can report any
 * coordinate it likes, and no amount of arithmetic on that number will reveal
 * it. What this does is make casual faking — a mock-location app from the Play
 * Store, a screenshot of someone else's reading, a submission typed up at home
 * that evening — visible. That is the honest limit, and the reviewer is told
 * what was checked rather than given a false verdict.
 */
class GpsVerificationService
{
    /**
     * Extra metres allowed beyond the radius, drawn from the device's own
     * reported accuracy.
     *
     * A phone that says "I am here, ±40m" and stands 20m outside a 150m fence
     * may well be inside it. Refusing that submission punishes the volunteer for
     * their handset. Capped so a device claiming ±5km cannot walk through any
     * fence it likes.
     */
    private const MAX_ACCURACY_ALLOWANCE = 100;

    /**
     * Below this, a reading is suspiciously perfect.
     *
     * Real GNSS hardware essentially never reports better than ~3m, and mock
     * providers commonly report 0 or 1 because they have no uncertainty to
     * model.
     */
    private const IMPLAUSIBLE_ACCURACY = 1.0;

    /** Faster than this between two readings and the volunteer did not walk. */
    private const IMPLAUSIBLE_SPEED_KMH = 200;

    /** How far the device clock may differ from the server's before it is odd. */
    private const MAX_CLOCK_SKEW_MINUTES = 30;

    /**
     * Can this submission be accepted?
     *
     * Only ever refuses on distance, and only when the task asks for a geo
     * check. Everything else is advisory.
     *
     * @return array{allowed: bool, reason: ?string, distance: ?float, within: ?bool}
     */
    public function checkRadius(Task $task, ?float $latitude, ?float $longitude, ?float $accuracy): array
    {
        if (! $task->requires_geo_check || ! $task->hasGeofence()) {
            return ['allowed' => true, 'reason' => null, 'distance' => null, 'within' => null];
        }

        if ($latitude === null || $longitude === null) {
            return [
                'allowed' => false,
                'reason' => 'This task must be submitted from the site, but no location was sent. '
                    .'Turn on location and try again.',
                'distance' => null,
                'within' => null,
            ];
        }

        $distance = $task->distanceTo($latitude, $longitude);
        $allowance = min(max((float) ($accuracy ?? 0), 0), self::MAX_ACCURACY_ALLOWANCE);
        $limit = (float) $task->radius + $allowance;

        if ($distance > $limit) {
            return [
                'allowed' => false,
                'reason' => sprintf(
                    'You appear to be %s from the task location, which is outside the %dm radius. '
                        .'Move closer to the site and submit again.',
                    $this->readableDistance($distance),
                    $task->radius,
                ),
                'distance' => $distance,
                'within' => false,
            ];
        }

        return [
            'allowed' => true,
            'reason' => null,
            'distance' => $distance,
            // Reported against the true radius, not the widened limit: a
            // submission accepted only thanks to the accuracy allowance is
            // still one the reviewer should see as borderline.
            'within' => $distance <= (float) $task->radius,
        ];
    }

    /**
     * Everything questionable about a reading.
     *
     * @param  array<string, mixed>  $payload
     * @return array<int, array{code: string, severity: string, detail: string}>
     */
    public function findings(
        User $user,
        array $payload,
        ?CarbonInterface $capturedAt = null,
        ?float $distance = null,
        ?Task $task = null,
    ): array {
        $findings = [];

        // 1. The device told us outright. Nothing else here is as strong.
        if (($payload['is_mocked'] ?? false) === true) {
            $findings[] = [
                'code' => 'mock_provider',
                'severity' => 'high',
                'detail' => 'The device reported this location came from a mock provider.',
            ];
        }

        // 2. Accuracy no real receiver achieves.
        $accuracy = $payload['gps_accuracy'] ?? null;

        if ($accuracy !== null && (float) $accuracy <= self::IMPLAUSIBLE_ACCURACY) {
            $findings[] = [
                'code' => 'implausible_accuracy',
                'severity' => 'medium',
                'detail' => sprintf(
                    'Reported accuracy of ±%.1fm is better than consumer GPS hardware achieves.',
                    (float) $accuracy,
                ),
            ];
        }

        // 3. No accuracy at all. Weak on its own — some platforms omit it — but
        //    worth recording because mock apps frequently do not supply one.
        if ($accuracy === null) {
            $findings[] = [
                'code' => 'no_accuracy',
                'severity' => 'low',
                'detail' => 'The device did not report an accuracy figure.',
            ];
        }

        // 4. A device clock far from the server's. Usually a wrong timezone;
        //    occasionally someone backdating work they did not do.
        if ($capturedAt) {
            $skew = abs($capturedAt->diffInMinutes(now()));

            if ($skew > self::MAX_CLOCK_SKEW_MINUTES) {
                $findings[] = [
                    'code' => 'clock_skew',
                    'severity' => $skew > 60 * 24 ? 'medium' : 'low',
                    'detail' => sprintf(
                        'The device clock was %d minutes from server time when this was captured.',
                        $skew,
                    ),
                ];
            }
        }

        // 5. Travel between this reading and the volunteer's last one that no
        //    ground transport explains.
        if ($speedFinding = $this->checkTravelSpeed($user, $payload)) {
            $findings[] = $speedFinding;
        }

        // 6. Inside the fence only because of the accuracy allowance.
        if ($task && $distance !== null && $task->radius !== null && $distance > $task->radius) {
            $findings[] = [
                'code' => 'outside_radius_within_accuracy',
                'severity' => 'low',
                'detail' => sprintf(
                    'Recorded %s from the task point, just outside the %dm radius but inside the device\'s margin of error.',
                    $this->readableDistance($distance),
                    $task->radius,
                ),
            ];
        }

        return $findings;
    }

    /**
     * Did this person cross the country since their last submission?
     *
     * Compared against their own previous reading rather than anything global,
     * because the question is about one human's movement. A volunteer genuinely
     * flying between provinces will trip this — which is why it is a finding
     * and not a refusal.
     *
     * @param  array<string, mixed>  $payload
     * @return array{code: string, severity: string, detail: string}|null
     */
    private function checkTravelSpeed(User $user, array $payload): ?array
    {
        $latitude = $payload['latitude'] ?? null;
        $longitude = $payload['longitude'] ?? null;

        if ($latitude === null || $longitude === null) {
            return null;
        }

        $previous = TaskSubmission::query()
            ->where('user_id', $user->id)
            ->whereNotNull('latitude')
            ->latest('created_at')
            ->first();

        if ($previous === null || $previous->created_at === null) {
            return null;
        }

        $minutes = abs($previous->created_at->diffInMinutes(now()));

        // Two readings in the same minute say nothing useful — the arithmetic
        // divides by something near zero and reports a nonsense speed.
        if ($minutes < 1) {
            return null;
        }

        $metres = $this->haversine(
            (float) $previous->latitude,
            (float) $previous->longitude,
            (float) $latitude,
            (float) $longitude,
        );

        $speed = ($metres / 1000) / ($minutes / 60);

        if ($speed < self::IMPLAUSIBLE_SPEED_KMH) {
            return null;
        }

        return [
            'code' => 'implausible_travel',
            'severity' => 'medium',
            'detail' => sprintf(
                'This is %s from their previous submission %d minutes earlier — about %d km/h.',
                $this->readableDistance($metres),
                $minutes,
                (int) round($speed),
            ),
        ];
    }

    /** The worst severity among findings, or null when there is nothing to say. */
    public function worstSeverity(array $findings): ?string
    {
        foreach (['high', 'medium', 'low'] as $level) {
            foreach ($findings as $finding) {
                if (($finding['severity'] ?? null) === $level) {
                    return $level;
                }
            }
        }

        return null;
    }

    /** Great-circle distance in metres. Same formula as Task::distanceTo(). */
    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6_371_000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function readableDistance(float $metres): string
    {
        return $metres >= 1000
            ? sprintf('%.1f km', $metres / 1000)
            : sprintf('%dm', (int) round($metres));
    }
}
