<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * The authenticated user as the mobile client sees them.
 *
 * Roles and permissions are included deliberately: the app uses them to decide
 * which screens and actions to show. They are a UI hint only — every request is
 * still authorised server-side by the existing policy classes, so a tampered
 * client gains nothing by lying about what it holds.
 *
 * @property \App\Models\User $resource
 */
class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'country' => $this->country,
            'address' => $this->address,
            // A ready-to-render absolute URL, or null when no avatar was set —
            // the client shows an initials monogram in that case and never has
            // to know the storage layout.
            //
            // Built from the host the client actually connected on (the request
            // Host header) rather than APP_URL. The app reaches the API at a
            // different host per platform — localhost:8000 on web/iOS,
            // 10.0.2.2:8000 on the Android emulator, a LAN IP on a real device —
            // and a single APP_URL cannot be correct for all of them. Using the
            // request host guarantees the avatar is served from wherever the
            // client just successfully talked to the API.
            'profile_image_url' => $this->profile_image
                ? $request->getSchemeAndHttpHost() . '/storage/' . ltrim($this->profile_image, '/')
                : null,
            'roles' => $this->getRoleNames()->values(),
            'permissions' => $this->getAllPermissions()->pluck('name')->values(),
            'is_super_admin' => $this->isSuperAdmin(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
