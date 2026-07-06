<?php

namespace Tests\Feature\Authorization;

use App\Filament\Pages\ManageSiteSettings;
use App\Filament\Resources\Donators\DonatorResource;
use App\Filament\Resources\Faqs\FaqResource;
use App\Filament\Resources\Permissions\PermissionResource;
use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\UserResource;
use App\Filament\Widgets\RecentActivityWidget;
use App\Filament\Widgets\TopDonatorsWidget;
use App\Models\User;
use App\Support\PermissionCatalog;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function userWithRole(string $role): User
    {
        return User::factory()->create()->assignRole($role);
    }

    /** The seeder must create every catalogued permission and all five roles. */
    public function test_seeder_creates_full_catalog_and_roles(): void
    {
        $this->assertSame(
            count(PermissionCatalog::allPermissionNames()),
            Permission::count(),
        );

        foreach (['Super Admin', 'Admin', 'Manager', 'Staff', 'Viewer'] as $role) {
            $this->assertTrue(Role::where('name', $role)->exists(), "Missing role: {$role}");
        }

        // Super Admin intentionally holds no explicit permissions (bypass).
        $this->assertSame(0, Role::findByName('Super Admin')->permissions->count());
    }

    /** The seeder is idempotent — a second run must not duplicate anything. */
    public function test_seeder_is_idempotent(): void
    {
        $permissions = Permission::count();
        $roles = Role::count();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->assertSame($permissions, Permission::count());
        $this->assertSame($roles, Role::count());
    }

    /** Super Admin bypasses every ability via Gate::before(). */
    public function test_super_admin_bypasses_all_checks(): void
    {
        $this->actingAs($this->userWithRole('Super Admin'));

        $this->assertTrue(DonatorResource::canViewAny());
        $this->assertTrue(RoleResource::canViewAny());
        $this->assertTrue(UserResource::canCreate());
        $this->assertTrue(TopDonatorsWidget::canView());
        $this->assertTrue(ManageSiteSettings::canAccess());
        // Even a permission that was never seeded resolves true for Super Admin.
        $this->assertTrue(auth()->user()->can('some_unlisted_ability'));
    }

    /** Viewer may read non-sensitive content but nothing else. */
    public function test_viewer_is_read_only_on_content(): void
    {
        $this->actingAs($this->userWithRole('Viewer'));

        $this->assertTrue(FaqResource::canViewAny());
        $this->assertFalse(FaqResource::canCreate());
        $this->assertFalse(DonatorResource::canViewAny());   // financial — out of scope
        $this->assertFalse(RoleResource::canViewAny());      // access control — super only
        $this->assertFalse(TopDonatorsWidget::canView());    // financial widget
        $this->assertTrue(RecentActivityWidget::canView());  // activity widget
        $this->assertFalse(ManageSiteSettings::canAccess());
    }

    /** Manager runs content/careers/engagement and reads financial. */
    public function test_manager_scope(): void
    {
        $this->actingAs($this->userWithRole('Manager'));

        $this->assertTrue(FaqResource::canCreate());
        $this->assertTrue(DonatorResource::canViewAny());    // read financial
        $this->assertFalse(DonatorResource::canCreate());    // but not write it
        $this->assertTrue(TopDonatorsWidget::canView());
        $this->assertFalse(RoleResource::canViewAny());      // no access control
        $this->assertFalse(UserResource::canViewAny());      // no user admin
    }

    /** Admin gets everything except Access Control (roles/permissions). */
    public function test_admin_excludes_access_control(): void
    {
        $this->actingAs($this->userWithRole('Admin'));

        $this->assertTrue(DonatorResource::canCreate());
        $this->assertTrue(UserResource::canViewAny());
        $this->assertTrue(ManageSiteSettings::canAccess());
        $this->assertFalse(RoleResource::canViewAny());
        $this->assertFalse(PermissionResource::canViewAny());
    }

    /** Voice moderation custom permissions gate the approve/reject actions. */
    public function test_voice_moderation_permissions(): void
    {
        $this->actingAs($this->userWithRole('Staff'));
        $this->assertTrue(auth()->user()->can('approve_voice'));
        $this->assertTrue(auth()->user()->can('reject_voice'));

        $this->actingAs($this->userWithRole('Viewer'));
        $this->assertFalse(auth()->user()->can('approve_voice'));
    }

    /** Panel access is role-based: no role → no entry. */
    public function test_panel_access_requires_a_role(): void
    {
        $panel = filament()->getPanel('admin');

        $this->assertTrue($this->userWithRole('Viewer')->canAccessPanel($panel));
        $this->assertFalse(User::factory()->create()->canAccessPanel($panel));
    }
}
