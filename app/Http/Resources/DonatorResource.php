<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Mirrors the public donators wall: name, impact and photo only.
 * Phone and exact amounts stay server-side.
 *
 * @mixin \App\Models\Donator
 */
class DonatorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'full_name' => $this->full_name,
            'impact' => $this->impact,
            'location' => $this->location,
            'profile_image' => $this->profile_image
                ? asset('storage/'.$this->profile_image)
                : null,
            'donation_date' => $this->donation_date?->toDateString(),
            'status' => $this->status,
        ];
    }
}
