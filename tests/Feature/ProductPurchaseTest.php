<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductPurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_active_products(): void
    {
        Product::create(['code' => 'GOLD', 'name' => 'Gold', 'cost_price' => 100, 'daily_income' => 0.1, 'duration_days' => 30, 'is_active' => true]);

        $this->getJson('/api/products')->assertOk()->assertJsonPath('0.code', 'GOLD');
    }

    public function test_member_submits_manual_purchase_and_duplicate_is_rejected(): void
    {
        $member = User::factory()->create(['is_active' => true]);
        $product = Product::create(['code' => 'SILVER', 'name' => 'Silver', 'cost_price' => 50, 'daily_income' => 0.05, 'duration_days' => 30, 'is_active' => true]);
        Sanctum::actingAs($member);

        $this->postJson("/api/products/{$product->id}/purchases", ['transaction_id' => 'TX-001'])
            ->assertCreated()
            ->assertJsonPath('status', 'pending');

        $this->postJson("/api/products/{$product->id}/purchases", ['transaction_id' => 'TX-002'])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'You already have an open purchase for this product.');
    }

    public function test_admin_can_activate_pending_purchase(): void
    {
        $member = User::factory()->create(['is_active' => true]);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $product = Product::create(['code' => 'BRONZE', 'name' => 'Bronze', 'cost_price' => 20, 'daily_income' => 0.02, 'duration_days' => 30, 'is_active' => true]);
        Sanctum::actingAs($member);
        $purchase = $this->postJson("/api/products/{$product->id}/purchases", ['transaction_id' => 'TX-002'])->json();

        Sanctum::actingAs($admin);
        $this->postJson("/api/admin/purchases/{$purchase['id']}/activate")
            ->assertOk()
            ->assertJsonPath('status', 'active');
    }

    public function test_product_activation_pays_the_configured_referral_rebate(): void
    {
        $referrer = User::factory()->create(['is_active' => true]);
        $member = User::factory()->create(['is_active' => true, 'referrer_id' => $referrer->id]);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $product = Product::create(['code' => 'DIAMOND', 'name' => 'Diamond', 'cost_price' => 100, 'daily_income' => 0.1, 'duration_days' => 30, 'is_active' => true]);
        Sanctum::actingAs($member);
        $purchase = $this->postJson("/api/products/{$product->id}/purchases", ['transaction_id' => 'TX-REBATE'])->json();

        Sanctum::actingAs($admin);
        $this->postJson("/api/admin/purchases/{$purchase['id']}/activate")->assertOk();

        $this->assertDatabaseHas('transactions', [
            'user_id' => $referrer->id,
            'type' => 'referral',
            'amount' => '10.000000',
        ]);
        $this->assertSame('10', (string) $referrer->fresh()->commission_balance);
    }

    public function test_product_activation_pays_all_three_referral_levels_once(): void
    {
        $levelThree = User::factory()->create(['is_active' => true]);
        $levelTwo = User::factory()->create(['is_active' => true, 'referrer_id' => $levelThree->id]);
        $levelOne = User::factory()->create(['is_active' => true, 'referrer_id' => $levelTwo->id]);
        $member = User::factory()->create(['is_active' => true, 'referrer_id' => $levelOne->id]);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $product = Product::create(['code' => 'TRIPLE', 'name' => 'Triple', 'cost_price' => 100, 'daily_income' => 0.1, 'duration_days' => 30, 'is_active' => true]);
        Sanctum::actingAs($member);
        $purchase = $this->postJson("/api/products/{$product->id}/purchases", ['transaction_id' => 'TX-TRIPLE'])->json();

        Sanctum::actingAs($admin);
        $this->postJson("/api/admin/purchases/{$purchase['id']}/activate")->assertOk();
        $this->postJson("/api/admin/purchases/{$purchase['id']}/activate")->assertUnprocessable();

        $this->assertSame('10', (string) $levelOne->fresh()->commission_balance);
        $this->assertSame('5', (string) $levelTwo->fresh()->commission_balance);
        $this->assertSame('1', (string) $levelThree->fresh()->commission_balance);
        $this->assertDatabaseCount('transactions', 3);
    }

    public function test_rejected_purchase_does_not_pay_a_rebate(): void
    {
        $referrer = User::factory()->create(['is_active' => true]);
        $member = User::factory()->create(['is_active' => true, 'referrer_id' => $referrer->id]);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $product = Product::create(['code' => 'REJECT', 'name' => 'Reject', 'cost_price' => 100, 'daily_income' => 0.1, 'duration_days' => 30, 'is_active' => true]);
        Sanctum::actingAs($member);
        $purchase = $this->postJson("/api/products/{$product->id}/purchases", ['transaction_id' => 'TX-REJECT'])->json();

        Sanctum::actingAs($admin);
        $this->postJson("/api/admin/purchases/{$purchase['id']}/reject", ['note' => 'Payment not found'])->assertOk();

        $this->assertSame('0', (string) $referrer->fresh()->commission_balance);
        $this->assertDatabaseMissing('transactions', ['type' => 'referral']);
    }

    public function test_inactive_referrer_does_not_receive_a_rebate(): void
    {
        $referrer = User::factory()->create(['is_active' => false]);
        $member = User::factory()->create(['is_active' => true, 'referrer_id' => $referrer->id]);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $product = Product::create(['code' => 'INACTIVE', 'name' => 'Inactive', 'cost_price' => 100, 'daily_income' => 0.1, 'duration_days' => 30, 'is_active' => true]);
        Sanctum::actingAs($member);
        $purchase = $this->postJson("/api/products/{$product->id}/purchases", ['transaction_id' => 'TX-INACTIVE'])->json();

        Sanctum::actingAs($admin);
        $this->postJson("/api/admin/purchases/{$purchase['id']}/activate")->assertOk();

        $this->assertSame('0', (string) $referrer->fresh()->commission_balance);
        $this->assertDatabaseMissing('transactions', ['type' => 'referral']);
    }

    public function test_non_admin_cannot_activate_a_product_purchase(): void
    {
        $member = User::factory()->create(['is_active' => true]);
        $product = Product::create(['code' => 'AUTH', 'name' => 'Auth', 'cost_price' => 100, 'daily_income' => 0.1, 'duration_days' => 30, 'is_active' => true]);
        Sanctum::actingAs($member);
        $purchase = $this->postJson("/api/products/{$product->id}/purchases", ['transaction_id' => 'TX-AUTH'])->json();

        $this->postJson("/api/admin/purchases/{$purchase['id']}/activate")
            ->assertForbidden();
    }

    public function test_member_can_buy_from_invest_wallet_and_receives_an_active_product(): void
    {
        $member = User::factory()->create(['is_active' => true, 'invest_balance' => 100]);
        $product = Product::create(['code' => 'WALLET', 'name' => 'Wallet', 'cost_price' => 40, 'daily_income' => 0.5, 'duration_days' => 30, 'is_active' => true]);
        Sanctum::actingAs($member);

        $this->postJson("/api/products/{$product->id}/purchases", ['payment_source' => 'invest'])
            ->assertCreated()
            ->assertJsonPath('status', 'active')
            ->assertJsonPath('payment_method', 'wallet');

        $this->assertSame('60', (string) $member->fresh()->invest_balance);
    }

    public function test_wallet_purchase_rejects_insufficient_balance_without_creating_purchase(): void
    {
        $member = User::factory()->create(['is_active' => true, 'invest_balance' => 10]);
        $product = Product::create(['code' => 'SHORT', 'name' => 'Short', 'cost_price' => 40, 'daily_income' => 0.5, 'duration_days' => 30, 'is_active' => true]);
        Sanctum::actingAs($member);

        $this->postJson("/api/products/{$product->id}/purchases", ['payment_source' => 'invest'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('payment_source');

        $this->assertDatabaseCount('purchases', 0);
    }

    public function test_product_earnings_can_be_cashed_out_through_existing_withdrawal_flow(): void
    {
        $member = User::factory()->create(['is_active' => true, 'invest_balance' => 100]);
        $product = Product::create(['code' => 'EARN', 'name' => 'Earn', 'cost_price' => 40, 'daily_income' => 2, 'duration_days' => 30, 'is_active' => true]);
        Sanctum::actingAs($member);
        $purchase = $this->postJson("/api/products/{$product->id}/purchases", ['payment_source' => 'invest'])->json();
        $activatedAt = Carbon::parse($purchase['activated_at']);
        Carbon::setTestNow($activatedAt->addDays(3));

        $this->postJson("/api/purchases/{$purchase['id']}/cash-out", [
            'network' => 'TRC20',
            'address' => str_repeat('A', 20),
        ])->assertCreated()
            ->assertJsonPath('amount', '6.000000');

        $this->assertDatabaseHas('withdrawals', ['purchase_id' => $purchase['id'], 'status' => 'pending']);
        $this->assertSame('0', (string) $member->fresh()->commission_balance);
        $this->assertSame('6.000000', (string) $product->purchases()->first()->cashed_out_amount);
        Carbon::setTestNow();
    }

    public function test_member_cannot_cash_out_another_members_product(): void
    {
        $owner = User::factory()->create(['is_active' => true, 'invest_balance' => 100]);
        $other = User::factory()->create(['is_active' => true]);
        $product = Product::create(['code' => 'OWNER', 'name' => 'Owner', 'cost_price' => 40, 'daily_income' => 2, 'duration_days' => 30, 'is_active' => true]);
        Sanctum::actingAs($owner);
        $purchase = $this->postJson("/api/products/{$product->id}/purchases", ['payment_source' => 'invest'])->json();
        Sanctum::actingAs($other);

        $this->postJson("/api/purchases/{$purchase['id']}/cash-out", ['network' => 'TRC20', 'address' => str_repeat('A', 20)])
            ->assertForbidden();
    }
}
