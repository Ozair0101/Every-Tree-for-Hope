<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Media */
class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'date' => $this->date?->toDateString(),
            'formatted_date' => $this->date?->format('M d, Y'),

            'video_youtube_url' => $this->video_youtube_url,
            'video_id' => $this->youtube_video_id,
            'thumbnail_url' => $this->thumbnail_url,
            'is_short' => $this->is_short,

            'is_active' => (bool) $this->is_active,
        ];
    }
}
