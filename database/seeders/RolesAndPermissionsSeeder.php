<?php

namespace Database\Seeders;

use App\Models\User;
use App\Providers\AuthServiceProvider;
use App\Support\PermissionCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Idempotent RBAC seeder. Safe to run on every deploy.
 *
 *  1. Clears the Spatie permission cache.
 *  2. Creates (never duplicates) every permission from the PermissionCatalog.
 *  3. Creates the five roles and *syncs* their permission sets (sync = the
 *     declared set becomes exact truth, so removing a permission here removes
 *     it on the next run too).
 *  4. Promotes any legacy `is_admin = true` user to Super Admin so nobody is
 *     locked out when the `is_admin` column is dropped.
 *  5. Clears the cache again so the new grants are live immediately.
 */
class RolesAndPermissionsSeeder extends Seeder
{
    private const GUARD = 'web';

    public function run(): void
    {
        // 1. Fresh cache before we touch anything.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 2. Permissions — idempotent.
        foreach (PermissionCatalog::allPermissionNames() as $name) {
            Permission::findOrCreate($name, self::GUARD);
        }

        // 2a. Explicitly refresh the cache before syncing roles.
        //     When this seeder runs via DatabaseSeeder (which uses
        //     WithoutModelEvents), the model events that normally invalidate
        //     Spatie's permission cache are muted — so syncPermissions() below
        //     would otherwise read a stale, empty cache and fail with
        //     "There is no permission named ...". Forgetting here makes the
        //     just-created permissions resolvable regardless of model events.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 3. Roles + their permission sets.
        foreach ($this->roleDefinitions() as $roleName => $permissions) {
            $role = Role::findOrCreate($roleName, self::GUARD);

            // Super Admin holds NO explicit permissions — the Gate::before()
            // bypass in AuthServiceProvider grants it everything. Keeping its
            // permission set empty avoids implying the grant is editable.
            if ($roleName === AuthServiceProvider::SUPER_ADMIN) {
                continue;
            }

            $role->syncPermissions($permissions);
        }

        // 4. Migrate legacy admins → Super Admin (before is_admin is dropped).
        if (Schema::hasColumn('users', 'is_admin')) {
            User::query()->where('is_admin', true)->each(
                fn (User $user) => $user->assignRole(AuthServiceProvider::SUPER_ADMIN)
            );
        }

        // 5. Rebuild the cache so grants are effective without a manual clear.
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Declarative role → permissions map, composed from the catalog by scope.
     *
     * @return array<string, array<int, string>>
     */
    private function roleDefinitions(): array
    {
        return [
            // Total bypass via Gate::before — intentionally empty.
            AuthServiceProvider::SUPER_ADMIN => [],

            // Everything except Access Control (roles/permissions) — that stays
            // exclusive to Super Admin.
            'Admin' => array_merge(
                PermissionCatalog::permissionsForScopes(['system', 'content', 'careers', 'engagement', 'financial']),
                ['view_dashboard', 'manage_site_settings', 'view_financial_widgets', 'view_activity_widgets'],
            ),

            // Full content/careers/engagement; read-only financial.
            'Manager' => array_merge(
                PermissionCatalog::permissionsForScopes(['content', 'careers', 'engagement']),
                PermissionCatalog::permissionsForScopes(['financial'], PermissionCatalog::READ, includeCustom: false),
                ['view_dashboard', 'view_financial_widgets', 'view_activity_widgets'],
            ),

            // Day-to-day content editor + light moderation. No deletes on content,
            // no financial.
            'Staff' => array_merge(
                PermissionCatalog::permissionsForScopes(['content'], ['view_any', 'view', 'create', 'update'], includeCustom: false),
                PermissionCatalog::permissionsForScopes(['careers'], PermissionCatalog::READ, includeCustom: false),
                PermissionCatalog::permissionsForScopes(['engagement'], PermissionCatalog::READ, includeCustom: false),
                ['update_job_application'],
                ['approve_voice', 'reject_voice', 'update_voice', 'update_voice_comment', 'delete_voice_comment'],
                ['view_dashboard', 'view_activity_widgets'],
            ),

            // Read-only across non-sensitive modules.
            'Viewer' => array_merge(
                PermissionCatalog::permissionsForScopes(['content', 'careers', 'engagement'], PermissionCatalog::READ, includeCustom: false),
                ['view_dashboard', 'view_activity_widgets'],
            ),
        ];
    }
}
