<?php

namespace Tests\Feature;

use App\Models\Deposit;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use App\Models\VipLevel;
use App\Models\VipPurchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VipMembershipTest extends TestCase
{
    use RefreshDatabase;

    private function package(array $attributes = []): VipLevel
    {
        return VipLevel::create(array_merge([
            'level' => 1, 'name' => 'VIP 1', 'min_deposit' => 0, 'price' => 50,
            'recharge_requirement' => 100, 'required_level_1_members' => 1,
            'required_level_2_members' => 0, 'required_level_3_members' => 0,
            'monthly_salary' => 10, 'duration_days' => 30, 'is_active' => true,
        ], $attributes));
    }

    private function activeProduct(User $user): void
    {
        $product = Product::create(['code' => 'P-'.uniqid(), 'name' => 'Mineral', 'cost_price' => 10, 'daily_income' => 1, 'duration_days' => 30, 'is_active' => true]);
        Purchase::create(['user_id' => $user->id, 'product_id' => $product->id, 'status' => 'active', 'principal' => 10, 'daily_income' => 1, 'duration_days' => 30, 'requested_at' => now(), 'activated_at' => now()]);
    }

    public function test_only_active_product_owning_referrals_count_for_qualification(): void
    {
        $member = User::factory()->create(['is_active' => true]);
        $referral = User::factory()->create(['is_active' => true, 'referrer_id' => $member->id]);
        $level = $this->package(['required_level_1_members' => 1, 'recharge_requirement' => 0]);
        $this->activeProduct($member);
        Deposit::create(['user_id' => $member->id, 'network' => 'TRC20', 'amount' => 100, 'status' => 'confirmed', 'confirmed_at' => now()]);
        Sanctum::actingAs($member);

        $this->getJson("/api/vip/{$level->id}/qualification")->assertOk()->assertJsonPath('eligible', false);
        $this->activeProduct($referral);
        $this->getJson("/api/vip/{$level->id}/qualification")->assertJsonPath('eligible', true);
    }

    public function test_manual_vip_purchase_is_pending_and_admin_can_activate_it(): void
    {
        $member = User::factory()->create(['is_active' => true]);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $level = $this->package(['required_level_1_members' => 0, 'recharge_requirement' => 0]);
        $this->activeProduct($member);
        Sanctum::actingAs($member);
        $purchase = $this->postJson("/api/vip/{$level->id}/purchases", ['payment_source' => 'manual', 'transaction_id' => 'VIP-TX-1'])
            ->assertCreated()->assertJsonPath('status', 'pending')->json();

        Sanctum::actingAs($admin);
        $this->postJson("/api/admin/vip-purchases/{$purchase['id']}/activate")
            ->assertOk()->assertJsonPath('status', 'active');
    }

    public function test_wallet_vip_purchase_debits_invest_balance_and_snapshots_terms(): void
    {
        $member = User::factory()->create(['is_active' => true, 'invest_balance' => 100]);
        $level = $this->package(['required_level_1_members' => 0, 'recharge_requirement' => 0, 'price' => 40]);
        $this->activeProduct($member);
        Sanctum::actingAs($member);

        $this->postJson("/api/vip/{$level->id}/purchases", ['payment_source' => 'invest'])
            ->assertCreated()->assertJsonPath('status', 'active')->assertJsonPath('payment_method', 'wallet');

        $this->assertSame('60', (string) $member->fresh()->invest_balance);
        $level->update(['monthly_salary' => 999]);
        $this->assertSame('10.000000', (string) $member->vipPurchases()->first()->monthly_salary);
    }

    public function test_unqualified_member_cannot_buy_vip(): void
    {
        $member = User::factory()->create(['is_active' => true]);
        $level = $this->package();
        Sanctum::actingAs($member);

        $this->postJson("/api/vip/{$level->id}/purchases", ['payment_source' => 'manual', 'transaction_id' => 'VIP-TX-2'])
            ->assertUnprocessable();
    }

    public function test_non_admin_cannot_activate_vip_purchase(): void
    {
        $member = User::factory()->create(['is_active' => true]);
        $level = $this->package(['required_level_1_members' => 0, 'recharge_requirement' => 0]);
        $this->activeProduct($member);
        Sanctum::actingAs($member);
        $purchase = $this->postJson("/api/vip/{$level->id}/purchases", ['payment_source' => 'manual', 'transaction_id' => 'VIP-TX-3'])->json();

        $this->postJson("/api/admin/vip-purchases/{$purchase['id']}/activate")->assertForbidden();
    }

    public function test_active_vip_salary_can_be_claimed_once_for_elapsed_days(): void
    {
        $member = User::factory()->create(['is_active' => true]);
        $level = $this->package(['required_level_1_members' => 0, 'recharge_requirement' => 0, 'monthly_salary' => 30]);
        $this->activeProduct($member);
        Sanctum::actingAs($member);
        $purchase = $this->postJson("/api/vip/{$level->id}/purchases", ['payment_source' => 'manual', 'transaction_id' => 'VIP-TX-4'])->json();
        VipPurchase::find($purchase['id'])->update(['status' => 'active', 'activated_at' => now()->subDays(15)]);

        $amount = (float) $this->postJson("/api/vip/purchases/{$purchase['id']}/salary")->assertOk()->json('amount');
        $this->assertGreaterThan(14.9, $amount);
        $this->assertLessThan(15.1, $amount);
        $this->assertEqualsWithDelta(15, (float) $member->fresh()->commission_balance, 0.001);
        $this->postJson("/api/vip/purchases/{$purchase['id']}/salary")->assertUnprocessable();
    }
}
