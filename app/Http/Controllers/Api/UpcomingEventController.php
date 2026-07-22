<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UpcomingEventResource;
use App\Models\UpcomingEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Upcoming events — the counterpart to the "get involved" form, which
 * takes an `upcoming_event_id`. The web site renders these inside its
 * pages, so there was no dedicated web controller to mirror.
 *
 * GET /api/upcoming-events        — future events
 * GET /api/upcoming-events/{id}   — one event
 */
class UpcomingEventController extends ApiController
{
    /**
     * Active events dated today or later.
     *
     * Pass `?include_past=1` to list past ones too, for an archive screen.
     */
    public function index(Request $request): JsonResponse
    {
        $query = UpcomingEvent::active();

        if (! $request->boolean('include_past')) {
            $query->upcoming();
        }

        $events = $query->orderBy('date')
            ->paginate($this->perPage($request, 12));

        return $this->paginated($events, UpcomingEventResource::class);
    }

    public function show(UpcomingEvent $upcomingEvent): JsonResponse
    {
        abort_unless($upcomingEvent->is_active, 404);

        return $this->ok([
            'event' => new UpcomingEventResource($upcomingEvent),
            'registrations_count' => $upcomingEvent->registrations()->count(),
        ]);
    }
}
