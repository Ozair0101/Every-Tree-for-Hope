<?php

namespace App\Http\Resources\Api\V1\Tasks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Just enough of an assignment to draw an avatar in a list row.
 *
 * The full {@see TaskAssignmentResource} carries six timestamps and a submission
 * — none of which a 40px circle needs, and all of which would be paid for on
 * every row of every page.
 *
 * @mixin \App\Models\TaskAssignment
 */
class TaskAssigneeStubResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'is_primary' => (bool) $this->is_primary,
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => trim($this->user->name.' '.($this->user->lastname ?? '')),
                // Built from the host the client actually connected on rather
                // than APP_URL, which cannot be right for every platform at
                // once. Same approach as TreeResource and UserResource.
                'avatar_url' => $this->user->profile_image
                    ? $request->getSchemeAndHttpHost().'/storage/'.ltrim($this->user->profile_image, '/')
                    : null,
            ]),
        ];
    }
}
