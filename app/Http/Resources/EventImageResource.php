<?php

namespace App\Http\Resources;

use App\Support\Thumb;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\EventImage */
class EventImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->full_image_url,
            // A small copy for list cards; the app falls back to `url` if absent.
            'thumbnail_url' => Thumb::url($this->image_path, 400),
            'caption' => $this->caption,
            'sort_order' => $this->sort_order,
        ];
    }
}
