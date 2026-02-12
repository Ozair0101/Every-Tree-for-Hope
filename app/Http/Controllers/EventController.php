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
        // Get active events with pagination (10 per page)
        $events = Event::active()->ordered()->paginate(10);
        
        return view('pages.gallery', compact('events'));
    }
}
