<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\UpcomingEvent */
class UpcomingEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // Translatable — resolved to the request locale.
            'title' => $this->title,
            'description' => $this->description,

            'date' => $this->date?->toDateString(),
            'formatted_date' => $this->formatted_date,
            'location' => $this->location,
            'province' => $this->province,

            'images' => collect($this->images ?? [])
                ->map(fn ($path) => str_starts_with((string) $path, 'http')
                    ? $path
                    : asset('storage/' . $path))
                ->values()
                ->all(),

            'is_active' => (bool) $this->is_active,
        ];
    }
}
