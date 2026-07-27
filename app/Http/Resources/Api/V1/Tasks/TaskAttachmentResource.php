<?php

namespace App\Http\Resources\Api\V1\Tasks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\TaskAttachment */
class TaskAttachmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'file_name' => $this->file_name,
            'file_type' => $this->file_type->value,
            'file_type_label' => $this->file_type->label(),
            'mime_type' => $this->mime_type,
            'file_size' => $this->file_size,
            'readable_size' => $this->readable_size,

            // Resolved against the host the client connected on, not APP_URL,
            // which cannot be correct for both the emulator and a device.
            'url' => $this->file_path
                ? $request->getSchemeAndHttpHost().'/storage/'.ltrim($this->file_path, '/')
                : null,

            // Dimensions let the client reserve the right space before the file
            // arrives — worth sending on the connections this app runs over.
            'width' => $this->width,
            'height' => $this->height,
            'duration_seconds' => $this->duration_seconds,
            'readable_duration' => $this->readable_duration,

            'uploaded_by' => $this->whenLoaded('uploader', fn () => $this->uploader?->name),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
