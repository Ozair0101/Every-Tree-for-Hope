<?php

namespace App\Http\Controllers;

use App\Models\Donator;
use App\Models\Event;
use App\Models\Partner;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display the gallery page with events.
     *
     * Supports ?sponsor_code=XXXX to show only the events a given
     * donator or partner sponsored.
     */
    public function index(Request $request)
    {
        // One search box handles both: a sponsor code OR an event title.
        // (`sponsor_code` is still accepted for backwards-compatible shared links.)
        $searchQuery = trim((string) ($request->query('q', $request->query('sponsor_code', ''))));

        $sponsor = null;
        $sponsorType = null;

        // A search term is first tried as a sponsor code; if it matches a
        // donator or partner we show their events, otherwise we fall back
        // to a title search.
        if ($searchQuery !== '') {
            $sponsor = Donator::findByCode($searchQuery);
            if ($sponsor) {
                $sponsorType = 'donator';
            } else {
                $sponsor = Partner::findByCode($searchQuery);
                if ($sponsor) {
                    $sponsorType = 'partner';
                }
            }
        }

        if ($sponsor) {
            // Only the events this sponsor funded
            $query = $sponsor->events()->where('is_active', true);
        } else {
            // All active events, optionally filtered by title
            $query = Event::active();

            if ($searchQuery !== '') {
                $query->where('title', 'like', '%' . $searchQuery . '%');
            }
        }

        $events = $query->orderBy('date', 'desc')
            ->paginate(9)
            ->withQueryString();

        // Handle AJAX request - return only events grid content
        if ($request->ajax() || $request->get('ajax')) {
            return view('partials.events-grid', compact('events'));
        }

        $sponsorName = $sponsor
            ? ($sponsorType === 'donator' ? $sponsor->full_name : $sponsor->company_name)
            : null;

        return view('pages.gallery', compact(
            'events',
            'searchQuery',
            'sponsor',
            'sponsorType',
            'sponsorName',
        ));
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        // Get related events (excluding current event)
        $relatedEvents = Event::active()
            ->where('id', '!=', $event->id)
            ->orderBy('date', 'desc')
            ->take(3)
            ->get();

        return view('pages.event-details', compact('event', 'relatedEvents'));
    }
}
