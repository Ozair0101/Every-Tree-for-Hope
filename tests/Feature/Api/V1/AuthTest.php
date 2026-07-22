<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Covers the mobile authentication surface: token issue, token use, and token
 * revocation. This is the pipeline every other mobile feature depends on, so it
 * is tested first and in detail.
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORD = 'correct-horse-battery-staple';

    private function user(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'email' => 'volunteer@example.test',
            'password' => Hash::make(self::PASSWORD),
        ], $attributes));
    }

    private function credentials(array $overrides = []): array
    {
        return array_merge([
            'email' => 'volunteer@example.test',
            'password' => self::PASSWORD,
            'device_name' => 'Pixel 7 — integration test',
        ], $overrides);
    }

    // ── Login ───────────────────────────────────────────────────────────

    public function test_valid_credentials_return_a_token_and_the_user(): void
    {
        $user = $this->user();

        $response = $this->postJson('/api/v1/auth/login', $this->credentials());

        $response->assertOk()
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'name', 'email', 'roles', 'permissions', 'is_super_admin'],
            ])
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.email', $user->email);

        $this->assertNotEmpty($response->json('token'));
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_the_password_is_never_returned(): void
    {
        $this->user();

        $response = $this->postJson('/api/v1/auth/login', $this->credentials());

        $this->assertArrayNotHasKey('password', $response->json('user'));
        $response->assertDontSee(self::PASSWORD);
    }

    public function test_a_wrong_password_is_rejected(): void
    {
        $this->user();

        $this->postJson('/api/v1/auth/login', $this->credentials(['password' => 'wrong']))
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_an_unknown_email_is_rejected_identically_to_a_wrong_password(): void
    {
        $this->user();

        $unknown = $this->postJson('/api/v1/auth/login', $this->credentials([
            'email' => 'nobody@example.test',
        ]))->assertStatus(422);

        $wrongPassword = $this->postJson('/api/v1/auth/login', $this->credentials([
            'password' => 'wrong',
        ]))->assertStatus(422);

        // Identical responses — the endpoint must not leak which accounts exist.
        $this->assertSame(
            $unknown->json('errors'),
            $wrongPassword->json('errors'),
        );
    }

    public function test_device_name_is_required(): void
    {
        $this->user();

        $this->postJson('/api/v1/auth/login', $this->credentials(['device_name' => '']))
            ->assertStatus(422)
            ->assertJsonValidationErrors('device_name');
    }

    public function test_logging_in_twice_from_one_device_does_not_accumulate_tokens(): void
    {
        $this->user();

        $this->postJson('/api/v1/auth/login', $this->credentials())->assertOk();
        $this->postJson('/api/v1/auth/login', $this->credentials())->assertOk();

        // Same device name → the earlier token is replaced, not duplicated.
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_logging_in_from_a_second_device_keeps_both_tokens(): void
    {
        $this->user();

        $this->postJson('/api/v1/auth/login', $this->credentials())->assertOk();
        $this->postJson('/api/v1/auth/login', $this->credentials([
            'device_name' => 'iPhone 15',
        ]))->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 2);
    }

    public function test_a_volunteer_without_any_admin_role_can_still_log_in(): void
    {
        // Panel access requires a role; mobile access deliberately does not.
        $user = $this->user();
        $this->assertCount(0, $user->getRoleNames());

        $this->postJson('/api/v1/auth/login', $this->credentials())
            ->assertOk()
            ->assertJsonPath('user.roles', []);
    }

    public function test_roles_and_permissions_are_exposed_for_ui_gating(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->user()->assignRole('Admin');

        $response = $this->postJson('/api/v1/auth/login', $this->credentials())->assertOk();

        $this->assertContains('Admin', $response->json('user.roles'));
        $this->assertNotEmpty($response->json('user.permissions'));
    }

    // ── Authenticated endpoints ─────────────────────────────────────────

    public function test_me_returns_the_authenticated_user(): void
    {
        $user = $this->user();
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_me_rejects_an_unauthenticated_request_with_json_not_a_redirect(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
        $this->assertJson($response->getContent());
    }

    public function test_an_invalid_token_is_rejected(): void
    {
        $this->withHeader('Authorization', 'Bearer not-a-real-token')
            ->getJson('/api/v1/auth/me')
            ->assertStatus(401);
    }

    public function test_logout_revokes_only_the_current_token(): void
    {
        $user = $this->user();

        $keep = $user->createToken('Other device')->plainTextToken;
        $revoke = $user->createToken('This device')->plainTextToken;
        $this->assertDatabaseCount('personal_access_tokens', 2);

        $this->withHeader('Authorization', "Bearer {$revoke}")
            ->postJson('/api/v1/auth/logout')
            ->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 1);

        // Each real HTTP request is a fresh process, but within one test the
        // application container persists and the guard caches the user it
        // already resolved. Forget it so the next call re-authenticates from
        // the bearer token, as production would.
        $this->app['auth']->forgetGuards();

        // The revoked token no longer works...
        $this->withHeader('Authorization', "Bearer {$revoke}")
            ->getJson('/api/v1/auth/me')
            ->assertStatus(401);

        $this->app['auth']->forgetGuards();

        // ...but the other device is still signed in.
        $this->withHeader('Authorization', "Bearer {$keep}")
            ->getJson('/api/v1/auth/me')
            ->assertOk();
    }

    public function test_logout_requires_authentication(): void
    {
        $this->postJson('/api/v1/auth/logout')->assertStatus(401);
    }

    // ── Rate limiting ───────────────────────────────────────────────────

    public function test_repeated_failed_logins_are_throttled(): void
    {
        $this->user();

        // The route allows 5 attempts per minute.
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login', $this->credentials(['password' => 'wrong']))
                ->assertStatus(422);
        }

        $this->postJson('/api/v1/auth/login', $this->credentials(['password' => 'wrong']))
            ->assertStatus(429);
    }
}
