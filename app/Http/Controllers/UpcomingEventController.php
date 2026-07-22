<?php

namespace App\Http\Controllers;

use App\Models\UpcomingEvent;
use Illuminate\Http\Request;

class UpcomingEventController extends Controller
{
    /**
     * Listing of every event still ahead of us, with keyword search.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $query = UpcomingEvent::query()->active()->upcoming();

        if ($search !== '') {
            // title/description are translatable JSON — a LIKE matches any locale.
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('province', 'like', "%{$search}%");
            });
        }

        $events = $query->orderBy('date')->paginate(9)->withQueryString();

        $totalUpcoming = UpcomingEvent::query()->active()->upcoming()->count();

        return view('pages.upcoming-events', compact('events', 'search', 'totalUpcoming'));
    }

    /**
     * Public detail page for a single upcoming event.
     */
    public function show(UpcomingEvent $upcomingEvent)
    {
        abort_unless($upcomingEvent->is_active, 404);

        $relatedEvents = UpcomingEvent::query()
            ->active()
            ->upcoming()
            ->where('id', '!=', $upcomingEvent->id)
            ->orderBy('date')
            ->take(3)
            ->get();

        // Named `$event` so the shared registration partial can be reused as-is.
        return view('pages.upcoming-event-details', [
            'event' => $upcomingEvent,
            'relatedEvents' => $relatedEvents,
        ]);
    }
}
