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
            'email' => $this->email,
            'roles' => $this->getRoleNames()->values(),
            'permissions' => $this->getAllPermissions()->pluck('name')->values(),
            'is_super_admin' => $this->isSuperAdmin(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
