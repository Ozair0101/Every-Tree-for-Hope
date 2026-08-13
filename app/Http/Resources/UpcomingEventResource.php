<?php

namespace App\Http\Resources;

use App\Support\PublicUrl;
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
            'tree_names' => $this->tree_names ?? [],

            'images' => collect($this->images ?? [])
                ->map(fn ($path) => PublicUrl::for($request, (string) $path))
                ->filter()
                ->values()
                ->all(),
            // Light copies for the poster card; the app falls back to `images`.
            'image_thumbnails' => collect($this->images ?? [])
                ->map(fn ($path) => \App\Support\Thumb::url((string) $path, 800))
                ->filter()
                ->values()
                ->all(),

            'is_active' => (bool) $this->is_active,
        ];
    }
}
