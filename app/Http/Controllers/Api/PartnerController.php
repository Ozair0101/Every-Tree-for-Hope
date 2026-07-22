<?php

namespace App\Http\Controllers\Api;

use App\Enums\PartnerType;
use App\Http\Resources\EventResource;
use App\Http\Resources\PartnerResource;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;

/**
 * JSON twin of \App\Http\Controllers\PartnerController.
 *
 * GET /api/partners        — sponsors, collaborators, supporters, other
 * GET /api/partners/advisors — advisors only
 * GET /api/partners/{code} — one partner by sponsor code, with their events
 */
class PartnerController extends ApiController
{
    /**
     * The partners directory — everyone except advisors, who have their
     * own page on the site and their own endpoint here.
     */
    public function index(): JsonResponse
    {
        $partners = Partner::active()
            ->whereIn('type', [
                PartnerType::SPONSOR->value,
                PartnerType::COLLABORATOR->value,
                PartnerType::SUPPORTER->value,
                PartnerType::OTHER->value,
            ])
            ->ordered()
            ->get();

        return $this->ok([
            'partners' => PartnerResource::collection($partners),
            // The site highlights the first partner as the featured one.
            'featured' => $partners->first()
                ? new PartnerResource($partners->first())
                : null,
            'counts_by_type' => [
                'sponsor' => $partners->where('type', PartnerType::SPONSOR)->count(),
                'collaborator' => $partners->where('type', PartnerType::COLLABORATOR)->count(),
                'supporter' => $partners->where('type', PartnerType::SUPPORTER)->count(),
                'other' => $partners->where('type', PartnerType::OTHER)->count(),
            ],
        ]);
    }

    /**
     * Advisors — partners of type ADVISOR.
     */
    public function advisors(): JsonResponse
    {
        $advisors = Partner::active()
            ->where('type', PartnerType::ADVISOR->value)
            ->ordered()
            ->get();

        return $this->ok([
            'advisors' => PartnerResource::collection($advisors),
        ]);
    }

    /**
     * One partner by sponsor code, with the events they funded.
     */
    public function show(string $code): JsonResponse
    {
        $partner = Partner::findByCode($code);

        if (! $partner) {
            return $this->fail('No partner found for that code.', null, 404);
        }

        $events = $partner->events()->where('is_active', true)
            ->with('images')
            ->orderBy('date', 'desc')
            ->get();

        return $this->ok([
            'partner' => new PartnerResource($partner),
            'events' => EventResource::collection($events),
        ]);
    }
}
