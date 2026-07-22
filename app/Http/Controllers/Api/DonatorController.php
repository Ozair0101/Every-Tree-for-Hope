<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\DonatorResource;
use App\Http\Resources\EventResource;
use App\Models\Donator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * JSON twin of \App\Http\Controllers\DonatorController.
 *
 * GET /api/donators        — the supporters wall
 * GET /api/donators/{code} — look a supporter up by their sponsor code
 */
class DonatorController extends ApiController
{
    /**
     * Verified supporters, newest first.
     */
    public function index(Request $request): JsonResponse
    {
        $donators = Donator::verified()
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage($request, 18));

        return $this->paginated($donators, DonatorResource::class, [
            'total_donators' => Donator::verified()->count(),
        ]);
    }

    /**
     * One supporter by sponsor code, with the events they funded — this is
     * what the app's "scan / enter your code" screen calls.
     */
    public function show(string $code): JsonResponse
    {
        $donator = Donator::findByCode($code);

        if (! $donator) {
            return $this->fail('No supporter found for that code.', null, 404);
        }

        $events = $donator->events()->where('is_active', true)
            ->with('images')
            ->orderBy('date', 'desc')
            ->get();

        return $this->ok([
            'donator' => new DonatorResource($donator),
            'events' => EventResource::collection($events),
        ]);
    }
}
