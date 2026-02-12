<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display the gallery page with events.
     */
    public function index()
    {
        // Get active events with pagination (9 per page)
        $events = Event::active()->ordered()->paginate(9);
        
        // Handle AJAX request - return only events grid content
        if (request()->ajax() || request()->get('ajax')) {
            return view('partials.events-grid', compact('events'));
        }
        
        return view('pages.gallery', compact('events'));
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
