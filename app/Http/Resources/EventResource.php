<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Event */
class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'province' => $this->province,
            'event_type' => $this->event_type,
            'video_url' => $this->video_url,

            'date' => $this->date?->toDateString(),
            'formatted_date' => $this->date ? $this->formatted_date : null,

            // Impact numbers the app's stat cards render.
            'trees_planted' => (int) $this->trees_planted,
            'trees_lost' => (int) $this->trees_lost,
            'trees_alive' => $this->trees_alive,
            'survival_rate' => $this->survival_rate,
            'volunteers' => (int) $this->volunteers,

            'tree_species' => $this->all_tree_species,
            'tree_names' => $this->tree_names ?? [],
            'custom_tree_species' => $this->custom_tree_species,
            'volunteer_names' => $this->volunteer_names ?? [],

            'sponsor_partner' => $this->sponsor_partner,
            'map_embed' => $this->map_embed,

            // Maintenance history.
            'last_maintained_at' => $this->last_maintained_at?->toDateString(),
            'maintenance_notes' => $this->maintenance_notes,
            'maintenance_visits' => $this->maintenance_visits ?? [],
            'maintenance_photos' => $this->maintenance_photos ?? [],

            'is_active' => (bool) $this->is_active,
            'sort_order' => $this->sort_order,

            // Only serialised when the controller eager-loaded them.
            'images' => EventImageResource::collection($this->whenLoaded('images')),
            'donators' => DonatorResource::collection($this->whenLoaded('donators')),
            'partners' => PartnerResource::collection($this->whenLoaded('partners')),

            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
