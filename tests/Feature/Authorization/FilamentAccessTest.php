<?php

namespace Tests\Feature\Authorization;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifies authorization at the HTTP/route layer — i.e. that direct URL access
 * is blocked even though the navigation/buttons are already hidden in the UI.
 */
class FilamentAccessTest extends TestCase
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

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_user_without_a_role_cannot_enter_the_panel(): void
    {
        $this->actingAs(User::factory()->create());

        // canAccessPanel() === false → Filament denies entry.
        $this->get('/admin')->assertForbidden();
    }

    public function test_viewer_can_open_permitted_resource_but_not_forbidden_one(): void
    {
        $this->actingAs($this->userWithRole('Viewer'));

        // Viewer may read FAQs (content) …
        $this->get('/admin/faqs')->assertSuccessful();

        // … but direct URL access to Donators (financial) is blocked, even
        // though the nav item is hidden.
        $this->get('/admin/donators')->assertForbidden();

        // … and Access Control is Super-Admin-only.
        $this->get('/admin/roles')->assertForbidden();
    }

    public function test_admin_can_open_financial_resource(): void
    {
        $this->actingAs($this->userWithRole('Admin'));

        $this->get('/admin/donators')->assertSuccessful();
    }

    public function test_only_super_admin_reaches_access_control(): void
    {
        $this->actingAs($this->userWithRole('Super Admin'));

        $this->get('/admin/roles')->assertSuccessful();
        $this->get('/admin/permissions')->assertSuccessful();
    }

    public function test_settings_page_is_gated(): void
    {
        $this->actingAs($this->userWithRole('Viewer'));
        $this->get('/admin/manage-site-settings')->assertForbidden();

        $this->actingAs($this->userWithRole('Admin'));
        $this->get('/admin/manage-site-settings')->assertSuccessful();
    }
}
