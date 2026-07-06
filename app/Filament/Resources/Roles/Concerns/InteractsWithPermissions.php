<?php

namespace App\Filament\Resources\Roles\Concerns;

use App\Filament\Resources\Roles\RoleResource;
use App\Support\PermissionCatalog;

/**
 * Shared load/save logic for the Role create & edit pages.
 *
 * The form splits permissions into one CheckboxList per group (state paths
 * like `permissions_user_engagement`). These helpers fold those groups into a
 * single flat permission-name array for `syncPermissions()`, and expand a
 * role's current permissions back into the per-group fields on edit.
 */
trait InteractsWithPermissions
{
    /**
     * Pull every `permissions_*` group out of the form data, unset them, and
     * return the de-duplicated flat list of selected permission names.
     *
     * @param  array<string, mixed>  $data
     * @return array<int, string>
     */
    protected function extractPermissionNames(array &$data): array
    {
        $names = [];

        foreach (array_keys($data) as $key) {
            if (str_starts_with($key, 'permissions_')) {
                if (is_array($data[$key])) {
                    $names = array_merge($names, $data[$key]);
                }
                unset($data[$key]);
            }
        }

        return array_values(array_unique($names));
    }

    /**
     * Expand a flat list of permission names into the per-group form fields.
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $currentPermissionNames
     * @return array<string, mixed>
     */
    protected function fillPermissionGroups(array $data, array $currentPermissionNames): array
    {
        foreach (PermissionCatalog::groupedForUi() as $group => $permissions) {
            $data[RoleResource::groupField($group)] = array_values(
                array_intersect(array_keys($permissions), $currentPermissionNames)
            );
        }

        return $data;
    }
}
