<?php

namespace Tests\Feature\Authorization;

use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Exercises the grouped-permission UI save path on the Role resource pages.
 */
class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        // Only Super Admin may manage roles.
        $this->actingAs(User::factory()->create()->assignRole('Super Admin'));
    }

    public function test_creating_a_role_syncs_selected_grouped_permissions(): void
    {
        Livewire::test(CreateRole::class)
            ->fillForm([
                'name' => 'Content Editor',
                'guard_name' => 'web',
                // Permissions are grouped per module; field = "permissions_" + slug(module label).
                'permissions_faqs' => ['view_any_faq', 'create_faq'],
                'permissions_voices' => ['approve_voice'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $role = Role::findByName('Content Editor');

        $this->assertTrue($role->hasPermissionTo('view_any_faq'));
        $this->assertTrue($role->hasPermissionTo('create_faq'));
        $this->assertTrue($role->hasPermissionTo('approve_voice'));
        $this->assertFalse($role->hasPermissionTo('delete_faq'));
    }

    public function test_editing_a_role_preloads_and_updates_permissions(): void
    {
        $role = Role::create(['name' => 'Temp', 'guard_name' => 'web']);
        $role->givePermissionTo('view_any_faq');

        Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
            // The existing permission is preloaded into its module group field.
            ->assertSchemaStateSet(['permissions_faqs' => ['view_any_faq']])
            ->fillForm([
                'permissions_faqs' => ['view_any_faq', 'update_faq'],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $role->refresh();
        $this->assertTrue($role->hasPermissionTo('update_faq'));
        $this->assertTrue($role->hasPermissionTo('view_any_faq'));
    }
}
