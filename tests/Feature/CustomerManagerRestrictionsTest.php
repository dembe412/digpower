<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomerManagerRestrictionsTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;

    private User $client;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager = User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => false,
            'admin_role' => 'manager',
            'is_active' => true,
        ]);

        $this->client = User::factory()->create([
            'is_admin' => false,
            'is_active' => true,
            'invest_balance' => 50000,
            'commission_balance' => 12000,
        ]);

        $this->product = Product::create([
            'code' => 'MIN-01',
            'name' => 'Gold Miner Pro',
            'cost_price' => 100000,
            'daily_income' => 5000,
            'duration_days' => 30,
            'is_active' => true,
        ]);

        Purchase::create([
            'user_id' => $this->client->id,
            'product_id' => $this->product->id,
            'status' => 'active',
            'payment_method' => 'manual',
            'principal' => 100000,
            'daily_income' => 5000,
            'duration_days' => 30,
            'requested_at' => now(),
            'activated_at' => now(),
        ]);
    }

    public function test_customer_manager_cannot_access_financial_overview(): void
    {
        Sanctum::actingAs($this->manager);

        $this->getJson('/api/admin/overview')->assertForbidden();
    }

    public function test_customer_manager_cannot_access_transactions_or_approvals(): void
    {
        Sanctum::actingAs($this->manager);

        $this->getJson('/api/admin/deposits')->assertForbidden();
        $this->getJson('/api/admin/withdrawals')->assertForbidden();
        $this->getJson('/api/admin/purchases')->assertForbidden();
        $this->getJson('/api/admin/vip-purchases')->assertForbidden();
    }

    public function test_customer_manager_cannot_see_active_value_or_client_financial_balances(): void
    {
        Sanctum::actingAs($this->manager);

        $response = $this->getJson('/api/admin/users')->assertOk();

        // Metric stats: active_value must be null
        $this->assertNull($response->json('stats.active_value'));
        $this->assertEquals(1, $response->json('stats.total'));
        $this->assertEquals(1, $response->json('stats.with_product'));
        $this->assertEquals(0, $response->json('stats.without_product'));

        // Client details: financial balances must be redacted/null
        $clientData = $response->json('clients.data.0');
        $this->assertNull($clientData['invest_balance']);
        $this->assertNull($clientData['commission_balance']);
        $this->assertNull($clientData['total_balance']);
        $this->assertNull($clientData['active_principal']);
        $this->assertNull($clientData['total_earned']);

        // Equipment list: principal and earned amount must be redacted/null
        $productData = $clientData['active_products'][0];
        $this->assertNull($productData['principal']);
        $this->assertNull($productData['earned_so_far']);
        $this->assertEquals('Gold Miner Pro', $productData['product_name']);
    }

    public function test_operations_admin_can_see_active_value_and_financial_balances(): void
    {
        $opsAdmin = User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => false,
            'admin_role' => 'admin',
            'is_active' => true,
        ]);

        Sanctum::actingAs($opsAdmin);

        $response = $this->getJson('/api/admin/users')->assertOk();

        // Ops admin gets real financial figures
        $this->assertEquals('100000', $response->json('stats.active_value'));
        $clientData = $response->json('clients.data.0');
        $this->assertEquals('50000', $clientData['invest_balance']);
        $this->assertEquals('12000', $clientData['commission_balance']);
    }

    public function test_customer_manager_can_assign_equipment_to_client(): void
    {
        Sanctum::actingAs($this->manager);

        $newProduct = Product::create([
            'code' => 'MIN-02',
            'name' => 'Silver Drill',
            'cost_price' => 50000,
            'daily_income' => 2000,
            'duration_days' => 15,
            'is_active' => true,
        ]);

        $this->postJson("/api/admin/users/{$this->client->id}/assign-product", [
            'product_id' => $newProduct->id,
        ])->assertCreated();

        $this->assertDatabaseHas('purchases', [
            'user_id' => $this->client->id,
            'product_id' => $newProduct->id,
            'status' => 'active',
            'payment_method' => 'admin_assigned',
        ]);
    }

    public function test_customer_manager_cannot_toggle_or_archive_clients(): void
    {
        Sanctum::actingAs($this->manager);

        $this->postJson("/api/admin/users/{$this->client->id}/toggle")->assertForbidden();
        $this->deleteJson("/api/admin/users/{$this->client->id}")->assertForbidden();
    }
}
