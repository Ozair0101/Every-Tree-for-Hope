<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Team */
class TeamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'position' => $this->position,
            'bio' => $this->bio,
            'message' => $this->message,
            'email' => $this->email,
            'social_media_url' => $this->social_media_url,
            'image_url' => $this->full_image_url,
        ];
    }
}
