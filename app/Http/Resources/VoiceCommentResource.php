<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\VoiceComment */
class VoiceCommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'author_name' => $this->author_name,
            // Avatar bits, so the app draws the same circle the web does.
            'initials' => $this->author_initials,
            'color' => $this->author_color,
            'body' => $this->body,
            'created_at' => $this->created_at?->toIso8601String(),
            'time_ago' => $this->created_at?->diffForHumans(),
        ];
    }
}
