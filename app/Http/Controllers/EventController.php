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
        $sponsorCode = trim((string) $request->query('sponsor_code', ''));
        $sponsor = null;
        $sponsorType = null;
        $sponsorNotFound = false;

        if ($sponsorCode !== '') {
            $sponsor = Donator::findByCode($sponsorCode);
            if ($sponsor) {
                $sponsorType = 'donator';
            } else {
                $sponsor = Partner::findByCode($sponsorCode);
                if ($sponsor) {
                    $sponsorType = 'partner';
                }
            }
            $sponsorNotFound = $sponsor === null;
        }

        if ($sponsor) {
            // Only the events this sponsor funded
            $events = $sponsor->events()
                ->where('is_active', true)
                ->orderBy('date', 'desc')
                ->paginate(9)
                ->withQueryString();
        } else {
            // All active events
            $events = Event::active()->orderBy('date', 'desc')->paginate(9);
        }

        // Handle AJAX request - return only events grid content
        if ($request->ajax() || $request->get('ajax')) {
            return view('partials.events-grid', compact('events'));
        }

        $sponsorName = $sponsor
            ? ($sponsorType === 'donator' ? $sponsor->full_name : $sponsor->company_name)
            : null;

        return view('pages.gallery', compact(
            'events',
            'sponsorCode',
            'sponsor',
            'sponsorType',
            'sponsorName',
            'sponsorNotFound',
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
