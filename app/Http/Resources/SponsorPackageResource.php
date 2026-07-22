<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\SponsorPackage */
class SponsorPackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,

            'price' => (float) $this->price,
            'currency' => $this->currency,
            'formatted_price' => $this->formatted_price,

            'trees_count' => $this->trees_count,
            'badge_label' => $this->badge_label,
            'allocations' => $this->allocations ?? [],

            'is_featured' => (bool) $this->is_featured,
            'sort_order' => $this->sort_order,
        ];
    }
}
