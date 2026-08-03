<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\EventResource;
use App\Http\Resources\MaintenanceVisitResource;
use App\Models\Event;
use App\Models\MaintenanceVisit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Module B — Maintenance visits.
 *
 * A follow-up inspection logged against an existing planting event. Any signed-in
 * volunteer may record one in the field; it is held `pending` (like a planted
 * tree) unless the author may already manage the event, in which case it is
 * published straight away. Only an approved visit is allowed to move the event's
 * numbers — its `trees_lost` rolls into the event's aggregate, and the survival
 * rate the app shows is recomputed from there.
 *
 *   GET  /events/{event}/maintenance-visits — the visit log for an event
 *   POST /events/{event}/maintenance-visits — record a visit (multipart)
 *   POST /maintenance-visits/{visit}/approve — publish a pending visit
 *   POST /maintenance-visits/{visit}/reject  — decline a pending visit
 */
class MaintenanceVisitController extends ApiController
{
    /** Ceiling on photos per visit, mirrored by the app. */
    private const MAX_PHOTOS = 8;

    /**
     * The visit log for an event.
     *
     * Approved visits are visible to everyone; a pending or rejected visit is
     * shown only to the volunteer who logged it and to anyone who can manage the
     * event — so a field worker sees their own draft, and a reviewer sees the
     * queue, without leaking unreviewed data to the public.
     */
    public function index(Request $request, Event $event): JsonResponse
    {
        $viewer = $request->user('sanctum');
        $canModerate = $viewer?->can('update', $event) ?? false;

        $visits = $event->maintenanceVisits()
            ->with(['user', 'images'])
            ->newest()
            ->when(! $canModerate, function ($query) use ($viewer) {
                $query->where(function ($q) use ($viewer) {
                    $q->where('status', 'approved');

                    if ($viewer) {
                        $q->orWhere('user_id', $viewer->id);
                    }
                });
            })
            ->paginate($this->perPage($request, 20));

        return $this->paginated($visits, MaintenanceVisitResource::class, [
            // The numbers the maintenance screen renders at the top, kept in step
            // with what an approved visit will change.
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'trees_planted' => (int) $event->trees_planted,
                'trees_lost' => (int) $event->trees_lost,
                'trees_alive' => $event->trees_alive,
                'survival_rate' => $event->survival_rate,
                'last_maintained_at' => $event->last_maintained_at?->toDateString(),
            ],
            'can_moderate' => $canModerate,
        ]);
    }

    /**
     * Record a maintenance visit.
     *
     * Idempotent: the app generates `client_uuid` before its first attempt and
     * replays the same value, so a visit logged with no signal and re-sent when
     * the connection returns is recorded exactly once — the second arrival gets
     * back the row the first created, side effects and all.
     */
    public function store(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'client_uuid' => 'nullable|uuid',
            'visit_date' => 'required|date|before_or_equal:today',
            'trees_checked' => 'nullable|integer|min:0|max:100000000',
            'trees_lost' => 'nullable|integer|min:0|max:100000000',
            'notes' => 'nullable|string|max:2000',
            'latitude' => 'nullable|numeric|between:-90,90|required_with:longitude',
            'longitude' => 'nullable|numeric|between:-180,180|required_with:latitude',
            'gps_accuracy' => 'nullable|integer|min:0|max:100000',
            'address' => 'nullable|string|max:300',
            'is_mocked' => 'nullable|boolean',
            'images' => 'nullable|array|max:'.self::MAX_PHOTOS,
            'images.*' => 'image|mimes:jpeg,jpg,png,webp,heic|max:8192',
        ]);

        // A replay of a visit already recorded returns the original rather than
        // re-running the insert, the photo writes and the survival recompute.
        $clientUuid = $validated['client_uuid'] ?? null;

        if ($existing = $this->findByClientUuid($clientUuid)) {
            return $this->created(
                ['visit' => new MaintenanceVisitResource($existing->load(['user', 'images']))],
                __('This visit was already recorded.'),
            );
        }

        $author = $request->user();
        // Whoever may manage the event is trusted to publish a visit directly;
        // everyone else's visit waits for review, exactly like a planted tree.
        $autoApprove = $author->can('update', $event);

        $visit = MaintenanceVisit::createOnce($clientUuid, [
            'event_id' => $event->id,
            'user_id' => $author->id,
            'visit_date' => $validated['visit_date'],
            'trees_checked' => $validated['trees_checked'] ?? null,
            'trees_lost' => $validated['trees_lost'] ?? 0,
            'notes' => $validated['notes'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'gps_accuracy' => $validated['gps_accuracy'] ?? null,
            'address' => $validated['address'] ?? null,
            'is_mocked' => $validated['is_mocked'] ?? null,
            'status' => $autoApprove ? 'approved' : 'pending',
            'approved_at' => $autoApprove ? now() : null,
        ]);

        foreach (array_values($request->file('images') ?? []) as $index => $file) {
            $visit->images()->create([
                'image_path' => $file->store('maintenance-visits', 'public'),
                'sort_order' => $index,
            ]);
        }

        // Only a published visit moves the event's survival numbers.
        if ($visit->status === 'approved') {
            $this->recompute($event);
        }

        return $this->created(
            ['visit' => new MaintenanceVisitResource($visit->load(['user', 'images']))],
            $autoApprove
                ? __('Maintenance visit recorded.')
                : __('Maintenance visit submitted and is waiting for review.'),
        );
    }

    /** Publish a pending visit and fold its loss into the event's survival rate. */
    public function approve(Request $request, MaintenanceVisit $visit): JsonResponse
    {
        $event = $visit->event;
        abort_unless($request->user()->can('update', $event), 403);

        $visit->update([
            'status' => 'approved',
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        $this->recompute($event);

        return $this->ok(
            ['visit' => new MaintenanceVisitResource($visit->load(['user', 'images']))],
            __('Maintenance visit approved.'),
        );
    }

    /** Decline a visit. Its loss is removed from the event's survival rate. */
    public function reject(Request $request, MaintenanceVisit $visit): JsonResponse
    {
        $event = $visit->event;
        abort_unless($request->user()->can('update', $event), 403);

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $visit->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['reason'] ?? null,
            'approved_at' => null,
        ]);

        $this->recompute($event);

        return $this->ok(
            ['visit' => new MaintenanceVisitResource($visit->load(['user', 'images']))],
            __('Maintenance visit rejected.'),
        );
    }

    /**
     * Roll the approved visits up into the event.
     *
     * `trees_lost` becomes the sum of every approved visit's loss, capped at the
     * number planted so the survival rate can never read as negative; the
     * last-maintained date follows the most recent approved visit. The event's
     * existing `survival_rate` accessor does the percentage from there, so this
     * writes the two stored inputs and nothing else.
     */
    private function recompute(Event $event): void
    {
        $approved = $event->maintenanceVisits()->approved();

        $lost = (int) $approved->sum('trees_lost');
        $planted = (int) $event->trees_planted;

        $event->update([
            'trees_lost' => $planted > 0 ? min($lost, $planted) : $lost,
            'last_maintained_at' => $approved->max('visit_date') ?? $event->last_maintained_at,
        ]);
    }

    /** The visit a client key already created, if any. */
    private function findByClientUuid(?string $clientUuid): ?MaintenanceVisit
    {
        if (blank($clientUuid)) {
            return null;
        }

        return MaintenanceVisit::query()->where('client_uuid', $clientUuid)->first();
    }
}
