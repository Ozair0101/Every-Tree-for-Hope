<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UpcomingEventResource;
use App\Models\UpcomingEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Schedule an upcoming event from the mobile app.
     *
     * Guarded by `create_upcoming_event` via {@see \App\Policies\UpcomingEventPolicy}.
     * `title` and `description` are translatable; the plain strings sent here are
     * stored under the request's locale (X-Locale). Send as multipart/form-data
     * when attaching `images[]`.
     */
    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('create', UpcomingEvent::class), 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:120',
            'tree_names' => 'nullable|array',
            'tree_names.*' => 'string|max:120',
            'images' => 'nullable|array|max:8',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:8192',
        ]);

        // Stored as a JSON array of relative paths on the `images` column.
        $paths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('upcoming-events', 'public');
            }
        }

        $event = new UpcomingEvent();
        $event->title = $validated['title'];
        $event->description = $validated['description'] ?? null;
        $event->date = $validated['date'];
        $event->location = $validated['location'] ?? null;
        $event->province = $validated['province'] ?? null;
        $event->tree_names = $validated['tree_names'] ?? [];
        $event->images = $paths;
        $event->is_active = true;
        $event->save();

        return $this->created(
            ['event' => new UpcomingEventResource($event)],
            __('Upcoming event created.'),
        );
    }

    /**
     * Edit an upcoming event. Requires `update_upcoming_event`.
     *
     * Only the sent fields change. `title`/`description` are translatable and are
     * stored under the request's locale, as in {@see store()}. Photos follow the
     * replace-if-provided rule. Send as multipart with `_method=PUT` for images.
     */
    public function update(Request $request, UpcomingEvent $upcomingEvent): JsonResponse
    {
        abort_unless($request->user()->can('update', $upcomingEvent), 403);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'date' => 'sometimes|required|date',
            'location' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:120',
            'tree_names' => 'nullable|array',
            'tree_names.*' => 'string|max:120',
            'images' => 'nullable|array|max:8',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:8192',
        ]);

        foreach (['title', 'description', 'date', 'location', 'province'] as $field) {
            if (array_key_exists($field, $validated)) {
                $upcomingEvent->{$field} = $validated[$field];
            }
        }
        if (array_key_exists('tree_names', $validated)) {
            $upcomingEvent->tree_names = $validated['tree_names'] ?? [];
        }

        if ($request->hasFile('images')) {
            foreach ((array) $upcomingEvent->images as $path) {
                Storage::disk('public')->delete($path);
            }
            $paths = [];
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('upcoming-events', 'public');
            }
            $upcomingEvent->images = $paths;
        }

        $upcomingEvent->save();

        return $this->ok(
            ['event' => new UpcomingEventResource($upcomingEvent->fresh())],
            __('Upcoming event updated.'),
        );
    }

    /** Delete an upcoming event and its photographs. Requires `delete_upcoming_event`. */
    public function destroy(Request $request, UpcomingEvent $upcomingEvent): JsonResponse
    {
        abort_unless($request->user()->can('delete', $upcomingEvent), 403);

        foreach ((array) $upcomingEvent->images as $path) {
            Storage::disk('public')->delete($path);
        }
        $upcomingEvent->delete();

        return $this->ok(null, __('Upcoming event deleted.'));
    }
}
