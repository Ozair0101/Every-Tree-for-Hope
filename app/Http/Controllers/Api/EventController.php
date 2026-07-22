<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\EventResource;
use App\Models\Donator;
use App\Models\Event;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * JSON twin of \App\Http\Controllers\EventController.
 *
 * GET /api/events        — paginated, searchable gallery
 * GET /api/events/{event} — one event plus related ones
 */
class EventController extends ApiController
{
    /**
     * Past planting events.
     *
     * One `q` parameter does double duty, exactly like the web gallery:
     * it is first tried as a sponsor code (donator or partner) and, when
     * that misses, falls back to an event-title search.
     */
    public function index(Request $request): JsonResponse
    {
        // `sponsor_code` stays accepted so old shared links keep working.
        $searchQuery = trim((string) ($request->query('q', $request->query('sponsor_code', ''))));

        $sponsor = null;
        $sponsorType = null;

        if ($searchQuery !== '') {
            $sponsor = Donator::findByCode($searchQuery);

            if ($sponsor) {
                $sponsorType = 'donator';
            } else {
                $sponsor = Partner::findByCode($searchQuery);
                $sponsorType = $sponsor ? 'partner' : null;
            }
        }

        if ($sponsor) {
            // Only the events this sponsor funded.
            $query = $sponsor->events()->where('is_active', true);
        } else {
            $query = Event::active();

            if ($searchQuery !== '') {
                $query->where('title', 'like', '%' . $searchQuery . '%');
            }
        }

        $events = $query->with(['images', 'donators', 'partners'])
            ->orderBy('date', 'desc')
            ->paginate($this->perPage($request, 9))
            ->withQueryString();

        return $this->paginated($events, EventResource::class, [
            // Echoed back so the app can show "Events sponsored by X".
            'sponsor' => $sponsor ? [
                'type' => $sponsorType,
                'code' => $sponsor->code,
                'name' => $sponsorType === 'donator'
                    ? $sponsor->full_name
                    : $sponsor->company_name,
            ] : null,
        ]);
    }

    /**
     * One event, with up to three related events for the "see also" strip.
     */
    public function show(Event $event): JsonResponse
    {
        $event->load(['images', 'donators', 'partners']);

        $relatedEvents = Event::active()
            ->where('id', '!=', $event->id)
            ->orderBy('date', 'desc')
            ->take(3)
            ->get();

        return $this->ok([
            'event' => new EventResource($event),
            'related_events' => EventResource::collection($relatedEvents),
        ]);
    }
}
