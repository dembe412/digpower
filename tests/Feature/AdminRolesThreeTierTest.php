<?php

namespace Tests\Feature;

use App\Models\Deposit;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use App\Models\VipLevel;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminRolesThreeTierTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    private User $opsAdmin;

    private User $customerManager;

    private User $clientWithProduct;

    private User $clientWithoutProduct;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        VipLevel::firstOrCreate(['level' => 0], ['name' => 'LV0', 'min_deposit' => 0]);

        $this->superAdmin = User::factory()->create([
            'name' => 'Root Super Admin',
            'email' => 'super@example.com',
            'is_active' => true,
        ]);
        $this->superAdmin->forceFill([
            'is_admin' => true,
            'is_super_admin' => true,
            'admin_role' => 'super_admin',
        ])->save();

        $this->opsAdmin = User::factory()->create([
            'name' => 'Operations Admin',
            'email' => 'ops@example.com',
            'is_active' => true,
        ]);
        $this->opsAdmin->forceFill([
            'is_admin' => true,
            'is_super_admin' => false,
            'admin_role' => 'admin',
        ])->save();

        $this->customerManager = User::factory()->create([
            'name' => 'Customer Manager',
            'email' => 'support@example.com',
            'is_active' => true,
        ]);
        $this->customerManager->forceFill([
            'is_admin' => true,
            'is_super_admin' => false,
            'admin_role' => 'manager',
        ])->save();

        $this->product = Product::create([
            'code' => 'BRONZE-TEST',
            'name' => 'Bronze Miner',
            'cost_price' => 50000,
            'daily_income' => 2000,
            'duration_days' => 30,
            'is_active' => true,
        ]);

        $this->clientWithProduct = User::factory()->create([
            'name' => 'Client With Product',
            'phone' => '256701111111',
            'is_active' => true,
            'invest_balance' => 10000,
        ]);

        Purchase::create([
            'user_id' => $this->clientWithProduct->id,
            'product_id' => $this->product->id,
            'status' => 'active',
            'principal' => 50000,
            'daily_income' => 2000,
            'duration_days' => 30,
            'activated_at' => now()->subDays(5),
            'matures_at' => now()->addDays(25),
        ]);

        $this->clientWithoutProduct = User::factory()->create([
            'name' => 'Client Without Product',
            'phone' => '256702222222',
            'is_active' => true,
            'invest_balance' => 0,
        ]);
    }

    public function test_super_admin_has_oversight_but_cannot_execute_transactional_operations(): void
    {
        Sanctum::actingAs($this->superAdmin);

        // Super Admin can view overview and monitoring
        $this->getJson('/api/admin/overview')->assertOk();
        $this->getJson('/api/admin/deposits')->assertOk();
        $this->getJson('/api/admin/withdrawals')->assertOk();

        // But Super Admin CANNOT modify or approve transactions
        $deposit = Deposit::create([
            'user_id' => $this->clientWithProduct->id,
            'amount' => 50000,
            'currency' => 'UGX',
            'status' => 'pending',
            'txid' => 'TX-TEST-1234',
        ]);

        $response = $this->postJson("/api/admin/deposits/{$deposit->id}/confirm");
        $response->assertStatus(403);

        $withdrawal = Withdrawal::create([
            'user_id' => $this->clientWithProduct->id,
            'amount' => 20000,
            'net_amount' => 18000,
            'fee' => 2000,
            'wallet' => 'invest',
            'status' => 'pending',
        ]);

        $this->postJson("/api/admin/withdrawals/{$withdrawal->id}/approve")->assertStatus(403);
    }

    public function test_super_admin_can_manage_the_other_two_types_of_admins(): void
    {
        Sanctum::actingAs($this->superAdmin);

        // 1. Super admin can list admins
        $response = $this->getJson('/api/super-admin/admins');
        $response->assertOk()
            ->assertJsonFragment(['name' => 'Operations Admin', 'admin_role' => 'admin'])
            ->assertJsonFragment(['name' => 'Customer Manager', 'admin_role' => 'manager']);

        // 2. Super admin can create a new Customer Manager
        $createRes = $this->postJson('/api/super-admin/admins', [
            'name' => 'Grace Support',
            'email' => 'grace@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'manager',
        ]);
        $createRes->assertCreated()
            ->assertJsonPath('admin.name', 'Grace Support')
            ->assertJsonPath('admin.admin_role', 'manager');

        $graceId = $createRes->json('admin.id');

        // 3. Super admin can update the role of an admin (e.g. promote to Operations Admin)
        $this->putJson("/api/super-admin/admins/{$graceId}", [
            'name' => 'Grace Operations',
            'role' => 'admin',
        ])->assertOk()
            ->assertJsonPath('admin.admin_role', 'admin');

        // 4. Super admin can toggle/deactivate an admin account
        $this->postJson("/api/super-admin/admins/{$graceId}/toggle")
            ->assertOk()
            ->assertJsonPath('is_active', false);
    }

    public function test_operations_admin_can_manage_transactions_and_products(): void
    {
        Sanctum::actingAs($this->opsAdmin);

        // Ops admin can confirm deposit
        $deposit = Deposit::create([
            'user_id' => $this->clientWithProduct->id,
            'amount' => 50000,
            'currency' => 'UGX',
            'status' => 'pending',
            'txid' => 'TX-TEST-9999',
        ]);

        $this->postJson("/api/admin/deposits/{$deposit->id}/confirm")
            ->assertOk()
            ->assertJsonPath('status', 'confirmed');

        // Ops admin can approve withdrawal
        $withdrawal = Withdrawal::create([
            'user_id' => $this->clientWithProduct->id,
            'amount' => 20000,
            'net_amount' => 18000,
            'fee' => 2000,
            'wallet' => 'invest',
            'status' => 'pending',
        ]);

        $this->postJson("/api/admin/withdrawals/{$withdrawal->id}/approve")
            ->assertOk()
            ->assertJsonPath('status', 'approved');

        // Ops admin cannot manage admins
        $this->getJson('/api/super-admin/admins')->assertForbidden();
    }

    public function test_customer_manager_cannot_access_transactions(): void
    {
        Sanctum::actingAs($this->customerManager);

        // Access to transactions is denied
        $this->getJson('/api/admin/deposits')->assertForbidden();
        $this->getJson('/api/admin/withdrawals')->assertForbidden();
        $this->getJson('/api/admin/yield-batches')->assertForbidden();
        $this->getJson('/api/admin/purchases')->assertForbidden();
        $this->getJson('/api/admin/vip-purchases')->assertForbidden();
    }

    public function test_customer_manager_can_see_clients_filtered_with_and_without_products(): void
    {
        Sanctum::actingAs($this->customerManager);

        // 1. Filter ALL clients
        $allRes = $this->getJson('/api/admin/users?filter=all');
        $allRes->assertOk()
            ->assertJsonPath('stats.total', 2)
            ->assertJsonPath('stats.with_product', 1)
            ->assertJsonPath('stats.without_product', 1);

        // 2. Filter WITH PRODUCT only
        $withProductRes = $this->getJson('/api/admin/users?filter=with_product');
        $withProductRes->assertOk()
            ->assertJsonCount(1, 'clients.data')
            ->assertJsonPath('clients.data.0.id', $this->clientWithProduct->id)
            ->assertJsonPath('clients.data.0.has_active_product', true)
            ->assertJsonPath('clients.data.0.active_products_count', 1);

        // 3. Filter WITHOUT PRODUCT only
        $withoutProductRes = $this->getJson('/api/admin/users?filter=without_product');
        $withoutProductRes->assertOk()
            ->assertJsonCount(1, 'clients.data')
            ->assertJsonPath('clients.data.0.id', $this->clientWithoutProduct->id)
            ->assertJsonPath('clients.data.0.has_active_product', false)
            ->assertJsonPath('clients.data.0.active_products_count', 0);
    }

    public function test_customer_manager_can_assign_equipment_to_client_like_tls(): void
    {
        Sanctum::actingAs($this->customerManager);

        // Client initially has no products
        $this->assertEquals(0, $this->clientWithoutProduct->purchases()->count());

        // Manager assigns a product (like TLS addLock)
        $response = $this->postJson("/api/admin/users/{$this->clientWithoutProduct->id}/assign-product", [
            'product_id' => $this->product->id,
        ]);

        $response->assertCreated()
            ->assertJsonPath('purchase.status', 'active')
            ->assertJsonPath('purchase.product_id', $this->product->id);

        $this->assertEquals(1, $this->clientWithoutProduct->purchases()->where('status', 'active')->count());
    }

    public function test_operations_admin_can_create_update_and_delete_products(): void
    {
        Sanctum::actingAs($this->opsAdmin);

        // 1. Create new product
        $createRes = $this->postJson('/api/admin/products', [
            'code' => 'CUSTOM-MINER-1',
            'name' => 'Custom Titanium Miner',
            'cost_price' => 120000,
            'daily_income' => 3500,
            'duration_days' => 45,
            'image_path' => '/images/products/mining/bronze.webp',
            'tagline' => 'Custom miner tagline',
            'sort_order' => 10,
            'is_active' => true,
        ]);
        $createRes->assertCreated()
            ->assertJsonPath('code', 'CUSTOM-MINER-1')
            ->assertJsonPath('name', 'Custom Titanium Miner');
        $newProductId = $createRes->json('id');

        // 2. Update the product
        $updateRes = $this->putJson("/api/admin/products/{$newProductId}", [
            'code' => 'CUSTOM-MINER-1',
            'name' => 'Custom Titanium Miner v2',
            'cost_price' => 135000,
            'daily_income' => 4000,
            'duration_days' => 50,
            'image_path' => '/images/products/mining/bronze.webp',
            'tagline' => 'Updated tagline',
            'sort_order' => 11,
            'is_active' => true,
        ]);
        $updateRes->assertOk()
            ->assertJsonPath('name', 'Custom Titanium Miner v2')
            ->assertJsonPath('cost_price', '135000.00');

        // 3. Delete product with no purchases -> permanently deleted
        $deleteRes = $this->deleteJson("/api/admin/products/{$newProductId}");
        $deleteRes->assertOk()->assertJsonPath('status', 'deleted');
        $this->assertDatabaseMissing('products', ['id' => $newProductId]);

        // 4. Delete product with existing purchases -> deactivated instead of deleted
        $deletePurchasedRes = $this->deleteJson("/api/admin/products/{$this->product->id}");
        $deletePurchasedRes->assertOk()->assertJsonPath('status', 'deactivated');
        $this->assertDatabaseHas('products', ['id' => $this->product->id, 'is_active' => false]);

        // 5. Super admin cannot modify or delete products (oversight separation)
        Sanctum::actingAs($this->superAdmin);
        $this->postJson('/api/admin/products', [
            'code' => 'SUPER-FAIL',
            'name' => 'Super Miner',
            'cost_price' => 50000,
            'daily_income' => 1000,
        ])->assertForbidden();
    }
}
