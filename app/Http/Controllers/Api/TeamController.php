<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\TeamResource;
use App\Models\Team;
use Illuminate\Http\JsonResponse;

/**
 * The people behind the project — used by the app's About screen.
 *
 * GET /api/team      — active members
 * GET /api/team/{id} — one member
 */
class TeamController extends ApiController
{
    public function index(): JsonResponse
    {
        $members = Team::active()->ordered()->get();

        return $this->ok([
            'members' => TeamResource::collection($members),
        ]);
    }

    public function show(Team $team): JsonResponse
    {
        abort_unless($team->is_active, 404);

        return $this->ok(new TeamResource($team));
    }
}
