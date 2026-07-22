<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\EventResource;
use App\Http\Resources\MediaResource;
use App\Http\Resources\PartnerResource;
use App\Http\Resources\UpcomingEventResource;
use App\Http\Resources\VoiceResource;
use App\Models\Donator;
use App\Models\Event;
use App\Models\JobPosting;
use App\Models\Media;
use App\Models\Partner;
use App\Models\UpcomingEvent;
use App\Models\Voice;
use Illuminate\Http\JsonResponse;

/**
 * The web site's home, about and works pages are static Blade views with
 * no controller — there is nothing to duplicate. The mobile app still
 * needs the numbers and the featured content those pages show, so this
 * controller assembles them.
 *
 * GET /api/home  — one call that fills the whole home screen
 * GET /api/stats — just the impact counters
 */
class HomeController extends ApiController
{
    public function index(): JsonResponse
    {
        return $this->ok([
            'stats' => $this->impactStats(),

            'latest_events' => EventResource::collection(
                Event::active()->with('images')->orderBy('date', 'desc')->take(6)->get()
            ),

            'upcoming_events' => UpcomingEventResource::collection(
                UpcomingEvent::active()->upcoming()->orderBy('date')->take(4)->get()
            ),

            'featured_voices' => VoiceResource::collection(
                Voice::approved()->ordered()->take(6)->get()
            ),

            'media' => MediaResource::collection(
                Media::active()->ordered()->take(6)->get()
            ),

            'partners' => PartnerResource::collection(
                Partner::active()->ordered()->take(12)->get()
            ),
        ]);
    }

    public function stats(): JsonResponse
    {
        return $this->ok($this->impactStats());
    }

    /**
     * Aggregate counters across the whole project.
     */
    protected function impactStats(): array
    {
        $events = Event::active();

        return [
            'events' => (clone $events)->count(),
            'trees_planted' => (int) (clone $events)->sum('trees_planted'),
            'trees_lost' => (int) (clone $events)->sum('trees_lost'),
            'volunteers' => (int) (clone $events)->sum('volunteers'),
            'provinces' => (clone $events)->whereNotNull('province')->distinct('province')->count('province'),
            'donators' => Donator::verified()->count(),
            'partners' => Partner::active()->count(),
            'voices' => Voice::approved()->count(),
            'open_jobs' => JobPosting::active()->open()->count(),
        ];
    }
}
