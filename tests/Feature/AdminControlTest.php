<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_a_client_visible_price_override(): void
    {
        Http::fake();
        Cache::forget('live_prices');
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true, 'is_active' => true])->save();
        $this->assertTrue($admin->fresh()->is_admin);
        $this->assertTrue($admin->fresh()->is_active);

        Sanctum::actingAs($admin);
        $response = $this
            ->putJson('/api/admin/prices', [
                'overrides' => [
                    'BTC' => [
                        'binance' => ['enabled' => true, 'price' => 65000, 'change_pct' => 1.25],
                    ],
                ],
            ]);
        $response
            ->assertOk()
            ->assertJsonPath('overrides.BTC.binance.price', 65000);

        $this->assertEquals(65000.0, Setting::get('price_overrides')['BTC']['binance']['price']);
        $this->getJson('/api/prices')
            ->assertOk()
            ->assertJsonPath('0.exchanges.binance.price', 65000)
            ->assertJsonPath('0.exchanges.binance.source', 'admin');
    }

    public function test_non_admin_cannot_access_admin_controls(): void
    {
        $member = User::factory()->create();

        Sanctum::actingAs($member);
        $this
            ->getJson('/api/admin/overview')
            ->assertForbidden();
    }

    public function test_admin_is_not_a_super_admin_by_default(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $this->assertFalse($admin->is_super_admin);
    }

    public function test_super_admin_can_access_the_same_protected_control_room(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_super_admin' => true, 'is_active' => true]);

        Sanctum::actingAs($admin);

        $this->getJson('/api/admin/overview')->assertOk();
    }

    public function test_super_admin_can_create_an_admin_account_that_can_log_in(): void
    {
        $superAdmin = User::factory()->create(['is_admin' => true, 'is_super_admin' => true, 'is_active' => true]);
        Sanctum::actingAs($superAdmin);

        $response = $this->postJson('/api/super-admin/admins', [
            'name' => 'Operations Admin',
            'email' => 'operations@example.test',
            'password' => 'AdminPass123!',
            'password_confirmation' => 'AdminPass123!',
        ]);

        $response->assertCreated()
            ->assertJsonPath('admin.is_admin', true)
            ->assertJsonPath('admin.is_super_admin', false);
        $this->assertDatabaseHas('users', [
            'email' => 'operations@example.test',
            'is_admin' => true,
            'is_super_admin' => false,
            'is_active' => true,
        ]);

        $this->postJson('/api/auth/login', [
            'identifier' => 'operations@example.test',
            'password' => 'AdminPass123!',
        ])->assertOk()
            ->assertJsonPath('user.is_admin', true);
    }

    public function test_regular_admin_cannot_create_another_admin_account(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_super_admin' => false, 'is_active' => true]);
        Sanctum::actingAs($admin);

        $this->postJson('/api/super-admin/admins', [
            'name' => 'Unauthorized Admin',
            'email' => 'unauthorized@example.test',
            'password' => 'AdminPass123!',
            'password_confirmation' => 'AdminPass123!',
        ])->assertForbidden();
    }

    public function test_only_an_active_super_admin_can_list_admin_accounts(): void
    {
        $regularAdmin = User::factory()->create(['is_admin' => true, 'is_super_admin' => false, 'is_active' => true]);
        Sanctum::actingAs($regularAdmin);
        $this->getJson('/api/super-admin/admins')->assertForbidden();

        $superAdmin = User::factory()->create(['is_admin' => true, 'is_super_admin' => true, 'is_active' => true]);
        User::factory()->create(['is_admin' => true, 'is_super_admin' => false, 'is_active' => true]);
        Sanctum::actingAs($superAdmin);

        $this->getJson('/api/super-admin/admins')
            ->assertOk()
            ->assertJsonCount(3);
    }

    public function test_duplicate_admin_identity_is_rejected_without_creating_a_second_account(): void
    {
        $existing = User::factory()->create(['email' => 'existing-admin@example.test']);
        $superAdmin = User::factory()->create(['is_admin' => true, 'is_super_admin' => true, 'is_active' => true]);
        Sanctum::actingAs($superAdmin);

        $this->postJson('/api/super-admin/admins', [
            'name' => 'Duplicate Admin',
            'email' => $existing->email,
            'password' => 'AdminPass123!',
            'password_confirmation' => 'AdminPass123!',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('email');
        $this->assertDatabaseCount('users', 2);
    }

    public function test_inactive_super_admin_cannot_use_super_admin_endpoints(): void
    {
        $superAdmin = User::factory()->create(['is_admin' => true, 'is_super_admin' => true, 'is_active' => false]);
        Sanctum::actingAs($superAdmin);

        $this->getJson('/api/super-admin/admins')->assertForbidden();
    }

    public function test_super_admin_creation_requires_password_confirmation_and_identity(): void
    {
        $superAdmin = User::factory()->create(['is_admin' => true, 'is_super_admin' => true, 'is_active' => true]);
        Sanctum::actingAs($superAdmin);

        $this->postJson('/api/super-admin/admins', [
            'name' => 'Incomplete Admin',
            'email' => 'incomplete@example.test',
            'password' => 'AdminPass123!',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('password');
    }
}
