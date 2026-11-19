<?php

namespace Tests\Feature;

use App\Models\Deposit;
use App\Models\User;
use App\Models\VipLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DepositFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        VipLevel::firstOrCreate(['level' => 0], ['name' => 'LV0', 'min_deposit' => 0]);
    }

    public function test_options_returns_supported_mobile_money_and_crypto_methods(): void
    {
        $member = User::factory()->create(['is_active' => true]);
        Sanctum::actingAs($member);

        $response = $this->getJson('/api/deposit/options');

        $response->assertOk()
            ->assertJsonStructure(['methods', 'networks', 'min', 'currency'])
            ->assertJsonFragment(['key' => 'mtn'])
            ->assertJsonFragment(['key' => 'airtel']);
    }

    public function test_member_can_submit_tls_style_deposit_with_transaction_id(): void
    {
        $member = User::factory()->create([
            'is_active' => true,
            'invest_balance' => 0,
            'name' => 'John Doe',
            'phone' => '0770001122',
        ]);
        Sanctum::actingAs($member);

        $response = $this->postJson('/api/deposits', [
            'payment_method' => 'mtn',
            'amount' => 5000,
            'transaction_id' => 'MM-TX-998877',
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', 'pending')
            ->assertJsonPath('payment_method', 'mtn')
            ->assertJsonPath('txid', 'MM-TX-998877');
        $this->assertEquals(5000, $response->json('amount'));

        $this->assertDatabaseHas('deposits', [
            'user_id' => $member->id,
            'payment_method' => 'mtn',
            'txid' => 'MM-TX-998877',
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_confirm_deposit_and_funds_credit_to_invest_wallet(): void
    {
        $member = User::factory()->create([
            'is_active' => true,
            'invest_balance' => 0,
        ]);
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        $deposit = Deposit::create([
            'user_id' => $member->id,
            'network' => 'mtn',
            'payment_method' => 'mtn',
            'payment_number' => '0770000000',
            'amount' => 100,
            'txid' => 'MTN-884422',
            'status' => 'pending',
        ]);

        Sanctum::actingAs($admin);
        $response = $this->postJson("/api/admin/deposits/{$deposit->id}/confirm");

        $response->assertOk()
            ->assertJsonPath('status', 'confirmed');

        $this->assertTrue(bccomp((string) $member->fresh()->invest_balance, '100', 2) === 0);
        $this->assertDatabaseHas('deposits', [
            'id' => $deposit->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_legacy_two_step_deposit_flow_still_works(): void
    {
        $member = User::factory()->create(['is_active' => true]);
        Sanctum::actingAs($member);

        $createResponse = $this->postJson('/api/deposits', [
            'network' => 'TRC20',
            'amount' => 5000,
        ]);

        $createResponse->assertCreated()
            ->assertJsonPath('status', 'pending');

        $depositId = $createResponse->json('id');

        $txidResponse = $this->postJson("/api/deposits/{$depositId}/txid", [
            'txid' => 'TX-ON-CHAIN-12345678',
        ]);

        $txidResponse->assertOk()
            ->assertJsonPath('txid', 'TX-ON-CHAIN-12345678');
    }
}
