<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SponsorPackageResource;
use App\Models\SponsorPackage;
use Illuminate\Http\JsonResponse;

/**
 * Sponsorship tiers shown on the funding-model page.
 *
 * GET /api/sponsor-packages      — active packages
 * GET /api/sponsor-packages/{id} — one package
 */
class SponsorPackageController extends ApiController
{
    public function index(): JsonResponse
    {
        $packages = SponsorPackage::active()->ordered()->get();

        return $this->ok([
            'packages' => SponsorPackageResource::collection($packages),
        ]);
    }

    public function show(SponsorPackage $sponsorPackage): JsonResponse
    {
        abort_unless($sponsorPackage->is_active, 404);

        return $this->ok(new SponsorPackageResource($sponsorPackage));
    }
}
