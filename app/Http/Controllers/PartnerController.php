<?php

namespace App\Http\Controllers;

use App\Enums\PartnerType;
use App\Models\Partner;

class PartnerController extends Controller
{
    /**
     * Public partners directory — everyone except advisors.
     */
    public function partners()
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

        $countsByType = [
            'sponsor'      => $partners->where('type', PartnerType::SPONSOR)->count(),
            'collaborator' => $partners->where('type', PartnerType::COLLABORATOR)->count(),
            'supporter'    => $partners->where('type', PartnerType::SUPPORTER)->count(),
            'other'        => $partners->where('type', PartnerType::OTHER)->count(),
        ];

        $featured = $partners->first();
        $rest = $partners->skip(1);

        return view('pages.partners', compact('partners', 'featured', 'rest', 'countsByType'));
    }

    /**
     * Public advisors page — partners of type ADVISOR.
     */
    public function advisors()
    {
        $advisors = Partner::active()
            ->where('type', PartnerType::ADVISOR->value)
            ->ordered()
            ->get();

        return view('pages.advisors', compact('advisors'));
    }
}
