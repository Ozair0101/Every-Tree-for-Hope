# Role & Permission System (RBAC)

Authorization for the Filament admin panel, built on
[spatie/laravel-permission](https://spatie.be/docs/laravel-permission) v8.

## Architecture at a glance

| Concern | Where |
|---|---|
| Permission catalog (single source of truth) | `app/Support/PermissionCatalog.php` |
| Convention policy (ability → `{ability}_{model}`) | `app/Policies/BasePolicy.php` + 21 per-model policies |
| Policy registration + **Super Admin bypass** | `app/Providers/AuthServiceProvider.php` |
| Roles, permissions, legacy-admin migration | `database/seeders/RolesAndPermissionsSeeder.php` |
| Panel access gate (`canAccessPanel`) | `app/Models/User.php` |
| Role/Permission management UI | `app/Filament/Resources/Roles`, `.../Permissions` |
| Route middleware aliases (`role`, `permission`) | `bootstrap/app.php` |

**Design principle — policies are the single source of truth.** Filament v5
automatically calls the model policy for navigation visibility, resource page
access, and Create/View/Edit/Delete/bulk actions. We therefore never duplicate
`can*()` methods inside resources; the policy decides once. Non-model surfaces
(custom pages, widgets, custom actions) use explicit `canAccess()` / `canView()`
/ `->authorize()` because they have no model policy.

**Naming convention:** `{ability}_{model}` — e.g. `view_any_donator`,
`delete_faq`, `approve_voice`. This maps 1:1 to `BasePolicy` methods and to the
ability names Filament checks, so adding a module = add one line to the catalog
+ one thin policy class.

## Roles (least privilege)

| Role | Scope |
|---|---|
| **Super Admin** | Total bypass via `Gate::before()`. Holds **no** explicit permissions. |
| **Admin** | Everything except Access Control (roles/permissions). |
| **Manager** | Full content/careers/engagement; **read-only** financial. |
| **Staff** | Content create/update (no delete); light voice/comment moderation; no financial. |
| **Viewer** | `view`/`view_any` on non-sensitive modules only. |

## Super Admin bypass (`AuthServiceProvider`)

```php
Gate::before(fn ($user, string $ability) => $user->hasRole('Super Admin') ? true : null);
```

Returning `true` short-circuits **every** gate/policy. Returning `null` (never
`false`) lets the normal pipeline run for everyone else. **Security
implication:** this ignores all least-privilege boundaries — grant "Super Admin"
only to fully trusted operators. The Super Admin role is protected from deletion
in the Role resource because the bypass depends on it existing.

---

## Security review

| # | Class of hole | Status | Mitigation |
|---|---|---|---|
| 1 | Hidden button but reachable endpoint | ✅ Closed | Filament enforces the same policy on the **route**, not just the UI. Verified by `FilamentAccessTest` (Viewer gets 403 on `/admin/donators` & `/admin/roles`). |
| 2 | Missing policy on a model | ✅ Closed | All 21 panel models have a registered policy (`AuthServiceProvider::$policies`). |
| 3 | Unauthenticated panel access | ✅ Closed | `Authenticate` middleware + `User::canAccessPanel()` (must hold ≥1 role). |
| 4 | Privilege escalation via user edit | ✅ Closed | The `roles` field in `UserResource` is hidden **and** non-dehydrated unless the operator has `update_role`. A Staff/Manager cannot grant themselves roles. |
| 5 | Custom action bypass (approve/reject voice) | ✅ Closed | `->authorize('approve_voice')` hides the action **and** blocks manual Livewire calls. |
| 6 | Sensitive widget data leakage | ✅ Closed | Financial widgets gated by `view_financial_widgets`; `canView()` stops both render and data queries. |
| 7 | Settings tampering | ✅ Closed | `ManageSiteSettings::canAccess()` requires `manage_site_settings`. |
| 8 | Lockout after `is_admin` removal | ✅ Closed | Seeder promotes every legacy `is_admin=true` user to Super Admin **before** the column is dropped. |
| 9 | Deleting the bypass role | ✅ Closed | Delete action hidden for the Super Admin role (list + edit header). |
| 10 | Permission tampering via UI | ✅ Closed | `PermissionResource` is read-only; `PermissionPolicy` denies create/update/delete. |

### Residual notes / follow-ups
- **Export permissions** (`export_donator`, `export_expense`) exist in the
  catalog but no export action is wired yet — attach `->authorize('export_donator')`
  when export is added.
- **`Gate::before` is total** — any future non-panel gate is also bypassed for
  Super Admin. That is intended, but keep the role list short.
- Consider a policy rule preventing a user from deleting **their own** account
  and from removing the last Super Admin (not yet enforced).

---

## Deployment checklist

Run in this order on each environment:

```bash
# 1. Pull code & install the new dependency
composer install --no-dev --optimize-autoloader

# 2. Migrate — creates Spatie tables, then drops is_admin
#    (seeder below must run BEFORE the drop on an existing DB; on this repo the
#     is_admin promotion already ran, and the drop migration is guarded by
#     Schema::hasColumn, so re-running is safe.)
php artisan migrate --force

# 3. Seed roles, permissions, and promote existing admins (idempotent)
php artisan db:seed --class=RolesAndPermissionsSeeder --force

# 4. Clear the permission cache + config/route caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
```

**Post-deploy verification**
- [ ] `php artisan test tests/Feature/Authorization` is green.
- [ ] At least one user has the **Super Admin** role (`User::role('Super Admin')->exists()`).
- [ ] Log in as Super Admin → all nav groups visible, including **Access Control**.
- [ ] Log in as Viewer → financial/user/roles nav hidden; `/admin/donators` returns 403.
- [ ] The `is_admin` column no longer exists (`Schema::hasColumn('users','is_admin') === false`).

**Whenever the permission catalog changes** (new module / permission): edit
`PermissionCatalog`, then re-run step 3. `syncPermissions` makes the declared
role sets exact truth, so removals are applied too.

**Rollback:** the drop-`is_admin` migration has a `down()` that re-adds the
column; `php artisan migrate:rollback` restores it. Roles/permissions data is
left intact (safe to keep).
