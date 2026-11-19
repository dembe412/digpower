<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WithdrawalFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_request_a_withdrawal_with_a_valid_pin_and_balance_is_debited(): void
    {
        $member = User::factory()->create([
            'is_active' => true,
            'invest_balance' => 100000,
            'pin_hash' => Hash::make('123456'),
        ]);
        Sanctum::actingAs($member);

        $response = $this->postJson('/api/withdrawals', [
            'wallet' => 'invest',
            'network' => 'TRC20',
            'address' => 'T'.str_repeat('A', 33),
            'amount' => 50000,
            'pin' => '123456',
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', 'pending')
            ->assertJsonPath('amount', '50000.000000')
            ->assertJsonPath('fee', '500.000000')
            ->assertJsonPath('net_amount', '49500.000000');
        $this->assertSame('50000', (string) $member->fresh()->invest_balance);
        $this->assertDatabaseHas('withdrawals', [
            'user_id' => $member->id,
            'wallet' => 'invest',
            'status' => 'pending',
        ]);
    }

    public function test_member_can_request_withdrawal_via_mobile_money_without_crypto_address(): void
    {
        $member = User::factory()->create([
            'is_active' => true,
            'invest_balance' => 100000,
            'phone' => '0770000000',
            'pin_hash' => Hash::make('123456'),
        ]);
        Sanctum::actingAs($member);

        $response = $this->postJson('/api/withdrawals', [
            'wallet' => 'invest',
            'payout_method' => 'mtn',
            'phone' => '0771234567',
            'amount' => 50000,
            'pin' => '123456',
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', 'pending')
            ->assertJsonPath('phone', '0771234567')
            ->assertJsonPath('payout_method', 'mtn')
            ->assertJsonPath('amount', '50000.000000')
            ->assertJsonPath('fee', '500.000000')
            ->assertJsonPath('net_amount', '49500.000000');
        $this->assertSame('50000', (string) $member->fresh()->invest_balance);
        $this->assertDatabaseHas('withdrawals', [
            'user_id' => $member->id,
            'phone' => '0771234567',
            'payout_method' => 'mtn',
            'status' => 'pending',
        ]);
    }

    public function test_withdrawal_rejects_amount_below_minimum_5000_ugx(): void
    {
        $member = User::factory()->create([
            'is_active' => true,
            'invest_balance' => 100000,
            'pin_hash' => Hash::make('123456'),
        ]);
        Sanctum::actingAs($member);

        $this->postJson('/api/withdrawals', [
            'wallet' => 'invest',
            'payout_method' => 'mtn',
            'phone' => '0771234567',
            'amount' => 4999,
            'pin' => '123456',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('amount');
        $this->assertDatabaseCount('withdrawals', 0);
    }

    public function test_withdrawal_requires_a_security_pin(): void
    {
        $member = User::factory()->create(['is_active' => true, 'invest_balance' => 100000]);
        Sanctum::actingAs($member);

        $this->postJson('/api/withdrawals', [
            'wallet' => 'invest',
            'network' => 'TRC20',
            'address' => 'T'.str_repeat('A', 33),
            'amount' => 50000,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('pin');
        $this->assertDatabaseCount('withdrawals', 0);
    }

    public function test_withdrawal_rejects_amount_above_withdrawable_balance_without_debiting(): void
    {
        $member = User::factory()->create([
            'is_active' => true,
            'invest_balance' => 10000,
            'pin_hash' => Hash::make('123456'),
        ]);
        Sanctum::actingAs($member);

        $this->postJson('/api/withdrawals', [
            'wallet' => 'invest',
            'network' => 'TRC20',
            'address' => 'T'.str_repeat('A', 33),
            'amount' => 10001,
            'pin' => '123456',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('amount');

        $this->assertSame('10000', (string) $member->fresh()->invest_balance);
        $this->assertDatabaseCount('withdrawals', 0);
    }

    public function test_admin_can_approve_complete_and_notify_a_withdrawal(): void
    {
        $member = User::factory()->create(['is_active' => true]);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $withdrawal = Withdrawal::create([
            'user_id' => $member->id,
            'wallet' => 'invest',
            'network' => 'TRC20',
            'address' => 'T'.str_repeat('A', 33),
            'amount' => 50000,
            'fee' => 500,
            'net_amount' => 49500,
            'status' => 'pending',
        ]);
        Sanctum::actingAs($admin);

        $this->postJson("/api/admin/withdrawals/{$withdrawal->id}/approve")
            ->assertOk()
            ->assertJsonPath('status', 'approved');
        $this->postJson("/api/admin/withdrawals/{$withdrawal->id}/complete", ['txid' => 'CHAIN-TX-001'])
            ->assertOk()
            ->assertJsonPath('status', 'completed')
            ->assertJsonPath('txid', 'CHAIN-TX-001');
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $member->id,
            'title' => 'Withdrawal sent',
        ]);
    }

    public function test_admin_rejecting_a_withdrawal_refunds_the_wallet(): void
    {
        $member = User::factory()->create(['is_active' => true, 'invest_balance' => 50000]);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $withdrawal = Withdrawal::create([
            'user_id' => $member->id,
            'wallet' => 'invest',
            'network' => 'TRC20',
            'address' => 'T'.str_repeat('A', 33),
            'amount' => 50000,
            'fee' => 500,
            'net_amount' => 49500,
            'status' => 'pending',
        ]);
        $member->forceFill(['invest_balance' => 0])->save();
        Sanctum::actingAs($admin);

        $this->postJson("/api/admin/withdrawals/{$withdrawal->id}/reject", ['note' => 'Invalid destination'])
            ->assertOk()
            ->assertJsonPath('status', 'rejected');

        $this->assertSame('50000', (string) $member->fresh()->invest_balance);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $member->id,
            'title' => 'Withdrawal rejected',
        ]);
    }
}
