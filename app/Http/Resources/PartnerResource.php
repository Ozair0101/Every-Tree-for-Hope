<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Partner */
class PartnerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'company_name' => $this->company_name,
            // `type` is a PartnerType enum — send the raw value plus its
            // display label and accent colour so the app can badge it.
            'type' => $this->type?->value,
            'type_label' => $this->type?->label(),
            'type_color' => $this->type?->color(),
            'description' => $this->description,
            'logo_url' => $this->full_logo_url,
            'is_active' => (bool) $this->is_active,
            'sort_order' => $this->sort_order,
        ];
    }
}
