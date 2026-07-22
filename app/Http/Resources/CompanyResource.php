<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Company */
class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'logo_url' => $this->logo_url,
            // Fallback for the app when there is no logo to show.
            'initials' => $this->initials,
            'website' => $this->website,
            'industry' => $this->industry,
            'location' => $this->location,
            'about' => $this->about,
            'is_verified' => (bool) $this->is_verified,
        ];
    }
}
