<?php

namespace App\Support;

/**
 * The single source of truth for every permission in the system.
 *
 * Both {@see \Database\Seeders\RolesAndPermissionsSeeder} (which creates the
 * permissions and wires them to roles) and the Filament Role resource (which
 * renders the grouped permission picker) read from this class, so the catalog
 * can never drift between the database and the UI.
 *
 * To add a module: append one entry to MODULES (or EXTRAS) and re-run the
 * seeder. Permissions, the role picker, and the policies (via the
 * {@see \App\Policies\BasePolicy} convention) all update from that one change.
 */
class PermissionCatalog
{
    /**
     * The CRUD abilities that a normal, non-soft-deleting model supports.
     * These map 1:1 to {@see \App\Policies\BasePolicy} methods and to the
     * ability names Filament v5 checks on resources/pages/actions.
     */
    public const CRUD = ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'];

    public const READ = ['view_any', 'view'];

    /**
     * Model-backed modules. Each generates "{ability}_{key}" permissions plus
     * any listed custom permissions.
     *
     * scope  — coarse grouping used to build roles.
     * group  — human label used to group the permission picker in the UI.
     *
     * @return array<int, array{key:string,label:string,scope:string,group:string,abilities:array<int,string>,custom:array<string,string>}>
     */
    public static function modules(): array
    {
        return [
            // ── System ──────────────────────────────────────────────
            ['key' => 'user',        'label' => 'Users',        'scope' => 'system',     'group' => 'System',             'abilities' => self::CRUD, 'custom' => []],
            ['key' => 'role',        'label' => 'Roles',        'scope' => 'access',     'group' => 'Access Control',      'abilities' => self::CRUD, 'custom' => []],
            ['key' => 'permission',  'label' => 'Permissions',  'scope' => 'access',     'group' => 'Access Control',      'abilities' => self::READ, 'custom' => []],

            // ── Content Management ──────────────────────────────────
            ['key' => 'company',        'label' => 'Companies',       'scope' => 'content', 'group' => 'Content Management', 'abilities' => self::CRUD, 'custom' => []],
            ['key' => 'event',          'label' => 'Events',          'scope' => 'content', 'group' => 'Content Management', 'abilities' => self::CRUD, 'custom' => []],
            ['key' => 'upcoming_event', 'label' => 'Upcoming Events', 'scope' => 'content', 'group' => 'Content Management', 'abilities' => self::CRUD, 'custom' => []],
            ['key' => 'faq',            'label' => 'FAQs',            'scope' => 'content', 'group' => 'Content Management', 'abilities' => self::CRUD, 'custom' => []],
            ['key' => 'media',          'label' => 'Media',           'scope' => 'content', 'group' => 'Content Management', 'abilities' => self::CRUD, 'custom' => []],
            ['key' => 'partner',        'label' => 'Partners',        'scope' => 'content', 'group' => 'Content Management', 'abilities' => self::CRUD, 'custom' => []],
            ['key' => 'team',           'label' => 'Teams',           'scope' => 'content', 'group' => 'Content Management', 'abilities' => self::CRUD, 'custom' => []],

            // ── Careers ─────────────────────────────────────────────
            ['key' => 'job_category',    'label' => 'Job Categories',   'scope' => 'careers', 'group' => 'Careers', 'abilities' => self::CRUD, 'custom' => []],
            ['key' => 'job_posting',     'label' => 'Job Postings',     'scope' => 'careers', 'group' => 'Careers', 'abilities' => self::CRUD, 'custom' => []],
            ['key' => 'job_application', 'label' => 'Job Applications', 'scope' => 'careers', 'group' => 'Careers', 'abilities' => ['view_any', 'view', 'update', 'delete', 'delete_any'], 'custom' => []],

            // ── User Engagement ─────────────────────────────────────
            ['key' => 'voice',               'label' => 'Voices',              'scope' => 'engagement', 'group' => 'User Engagement', 'abilities' => self::CRUD, 'custom' => ['approve_voice' => 'Approve', 'reject_voice' => 'Reject']],
            ['key' => 'voice_comment',       'label' => 'Voice Comments',      'scope' => 'engagement', 'group' => 'User Engagement', 'abilities' => ['view_any', 'view', 'update', 'delete', 'delete_any'], 'custom' => []],
            ['key' => 'contact_message',     'label' => 'Contact Messages',    'scope' => 'engagement', 'group' => 'User Engagement', 'abilities' => ['view_any', 'view', 'delete', 'delete_any'], 'custom' => []],
            ['key' => 'involvement_request', 'label' => 'Involvement Requests', 'scope' => 'engagement', 'group' => 'User Engagement', 'abilities' => self::CRUD, 'custom' => []],
            ['key' => 'tree_request',        'label' => 'Tree Requests',       'scope' => 'engagement', 'group' => 'User Engagement', 'abilities' => self::CRUD, 'custom' => []],
            ['key' => 'tree',                'label' => 'Planted Trees',       'scope' => 'engagement', 'group' => 'User Engagement', 'abilities' => self::CRUD, 'custom' => ['approve_tree' => 'Approve', 'reject_tree' => 'Reject']],

            // ── Field Operations ────────────────────────────────────
            // Task management. `assign_task` and `review_task` are separate
            // from `update_task` on purpose: a coordinator may hand out and
            // sign off work without being able to rewrite the brief, and a
            // reviewer is not automatically an editor.
            ['key' => 'task', 'label' => 'Tasks', 'scope' => 'operations', 'group' => 'Field Operations', 'abilities' => self::CRUD, 'custom' => [
                'assign_task' => 'Assign to volunteers',
                'review_task' => 'Review submissions (approve/reject)',
                'cancel_task' => 'Cancel',
                'export_task' => 'Export',
            ]],
            ['key' => 'task_category', 'label' => 'Task Categories', 'scope' => 'operations', 'group' => 'Field Operations', 'abilities' => self::CRUD, 'custom' => []],
            ['key' => 'task_template', 'label' => 'Task Templates',  'scope' => 'operations', 'group' => 'Field Operations', 'abilities' => self::CRUD, 'custom' => []],
            ['key' => 'task_recurrence', 'label' => 'Task Schedules', 'scope' => 'operations', 'group' => 'Field Operations', 'abilities' => self::CRUD, 'custom' => []],
            // Read-only by design: an audit trail nobody can edit or delete is
            // the only kind worth keeping. Purging old entries is an archival
            // job, not a permission.
            ['key' => 'task_activity_log', 'label' => 'Activity Log', 'scope' => 'operations', 'group' => 'Field Operations', 'abilities' => self::READ, 'custom' => []],

            // ── Financial ───────────────────────────────────────────
            ['key' => 'donator',         'label' => 'Donators',        'scope' => 'financial', 'group' => 'Financial', 'abilities' => self::CRUD, 'custom' => ['export_donator' => 'Export']],
            ['key' => 'expense',         'label' => 'Expenses',        'scope' => 'financial', 'group' => 'Financial', 'abilities' => self::CRUD, 'custom' => ['export_expense' => 'Export']],
            ['key' => 'sponsor_package', 'label' => 'Sponsor Packages', 'scope' => 'financial', 'group' => 'Financial', 'abilities' => self::CRUD, 'custom' => []],
        ];
    }

    /**
     * Standalone permissions not tied to an Eloquent model (custom pages,
     * dashboard, widget groups).
     *
     * @return array<string, array<string, string>> group label => [permission => human label]
     */
    public static function extras(): array
    {
        return [
            'System' => [
                'view_dashboard' => 'View dashboard',
                'manage_site_settings' => 'Manage site settings',
            ],
            'Dashboard Widgets' => [
                'view_financial_widgets' => 'View financial widgets (stats, donations, top donors)',
                'view_activity_widgets' => 'View recent-activity widget',
            ],
        ];
    }

    /**
     * Every permission name in the system, flat and de-duplicated.
     *
     * @return array<int, string>
     */
    public static function allPermissionNames(): array
    {
        $names = [];

        foreach (self::modules() as $module) {
            foreach ($module['abilities'] as $ability) {
                $names[] = "{$ability}_{$module['key']}";
            }
            foreach (array_keys($module['custom']) as $custom) {
                $names[] = $custom;
            }
        }

        foreach (self::extras() as $permissions) {
            foreach (array_keys($permissions) as $name) {
                $names[] = $name;
            }
        }

        return array_values(array_unique($names));
    }

    /**
     * Permission names grouped by UI label, with human-readable option labels —
     * consumed directly by the Role resource's grouped CheckboxList.
     *
     * @return array<string, array<string, string>> group label => [permission => human label]
     */
    public static function groupedForUi(): array
    {
        $abilityLabels = [
            'view_any' => 'View list',
            'view' => 'View',
            'create' => 'Create',
            'update' => 'Update',
            'delete' => 'Delete',
            'delete_any' => 'Delete any (bulk)',
        ];

        $grouped = [];

        foreach (self::modules() as $module) {
            $label = $module['label'];
            foreach ($module['abilities'] as $ability) {
                $grouped[$label]["{$ability}_{$module['key']}"] = $abilityLabels[$ability] ?? ucfirst(str_replace('_', ' ', $ability));
            }
            foreach ($module['custom'] as $name => $human) {
                $grouped[$label][$name] = $human;
            }
        }

        foreach (self::extras() as $group => $permissions) {
            foreach ($permissions as $name => $human) {
                $grouped[$group][$name] = $human;
            }
        }

        return $grouped;
    }

    /**
     * Names of all model-backed permissions whose module matches one of the
     * given scopes, optionally restricted to a subset of abilities.
     *
     * Used by the seeder to compose roles declaratively.
     *
     * @param  array<int, string>  $scopes
     * @param  array<int, string>|null  $onlyAbilities  null = all the module's abilities + customs
     * @return array<int, string>
     */
    public static function permissionsForScopes(array $scopes, ?array $onlyAbilities = null, bool $includeCustom = true): array
    {
        $names = [];

        foreach (self::modules() as $module) {
            if (! in_array($module['scope'], $scopes, true)) {
                continue;
            }

            foreach ($module['abilities'] as $ability) {
                if ($onlyAbilities === null || in_array($ability, $onlyAbilities, true)) {
                    $names[] = "{$ability}_{$module['key']}";
                }
            }

            if ($includeCustom) {
                foreach (array_keys($module['custom']) as $custom) {
                    $names[] = $custom;
                }
            }
        }

        return $names;
    }
}
