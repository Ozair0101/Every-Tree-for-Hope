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
     * Create a planting event from the mobile app.
     *
     * Guarded by the same `create_event` permission the admin panel enforces —
     * resolved through {@see \App\Policies\EventPolicy} so a role configured in
     * Filament grants (or denies) this automatically. Send as multipart/form-data
     * when attaching `images[]`.
     */
    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('create', Event::class), 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'location' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:120',
            'date' => 'required|date',
            'trees_planted' => 'nullable|integer|min:0|max:100000000',
            'trees_lost' => 'nullable|integer|min:0|max:100000000',
            'volunteers' => 'nullable|integer|min:0|max:10000000',
            'sponsor_partner' => 'nullable|string|max:255',
            'tree_names' => 'nullable|array',
            'tree_names.*' => 'string|max:120',
            // Google Map: an "embed a map" iframe, a share link, or "lat,lng".
            'map_embed' => 'nullable|string|max:10000',
            // Maintenance & Health.
            'last_maintained_at' => 'nullable|date',
            'maintenance_notes' => 'nullable|string|max:5000',
            'images' => 'nullable|array|max:8',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:8192',
        ]);

        $event = Event::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'province' => $validated['province'] ?? null,
            'date' => $validated['date'],
            'trees_planted' => $validated['trees_planted'] ?? 0,
            'trees_lost' => $validated['trees_lost'] ?? 0,
            'volunteers' => $validated['volunteers'] ?? 0,
            'sponsor_partner' => $validated['sponsor_partner'] ?? null,
            'tree_names' => $validated['tree_names'] ?? [],
            'map_embed' => $validated['map_embed'] ?? null,
            'last_maintained_at' => $validated['last_maintained_at'] ?? null,
            'maintenance_notes' => $validated['maintenance_notes'] ?? null,
            'is_active' => true,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
                $event->images()->create([
                    'image_path' => $file->store('events', 'public'),
                    'sort_order' => $i,
                ]);
            }
        }

        $event->load('images');

        return $this->created(['event' => new EventResource($event)], __('Event created.'));
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
