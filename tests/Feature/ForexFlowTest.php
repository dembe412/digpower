<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ForexFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_or_authenticated_user_can_fetch_forex_rates(): void
    {
        $response = $this->getJson('/api/forex/rates');

        $response->assertOk()
            ->assertJsonStructure([
                'USD_UGX',
                'EUR_UGX',
                'rates' => [
                    'USD' => ['currency', 'rate', 'formatted'],
                    'EUR' => ['currency', 'rate', 'formatted'],
                ],
            ]);
    }

    public function test_user_can_convert_ugx_to_usd(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
            'invest_balance' => 40000,
            'usd_balance' => 0,
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/forex/convert', [
            'from' => 'UGX',
            'to' => 'USD',
            'amount' => 37500,
        ]);

        $response->assertOk()
            ->assertJsonPath('from', 'UGX')
            ->assertJsonPath('to', 'USD');

        $user->refresh();
        $this->assertEquals(2500, (float) $user->invest_balance);
        $this->assertGreaterThan(0, (float) $user->usd_balance);
    }

    public function test_user_can_convert_usd_back_to_ugx(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
            'invest_balance' => 0,
            'usd_balance' => 10,
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/forex/convert', [
            'from' => 'USD',
            'to' => 'UGX',
            'amount' => 10,
        ]);

        $response->assertOk()
            ->assertJsonPath('from', 'USD')
            ->assertJsonPath('to', 'UGX');

        $user->refresh();
        $this->assertEquals(0, (float) $user->usd_balance);
        $this->assertGreaterThan(30000, (float) $user->invest_balance);
    }

    public function test_conversion_fails_on_insufficient_funds(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
            'invest_balance' => 1000,
            'usd_balance' => 0,
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/forex/convert', [
            'from' => 'UGX',
            'to' => 'USD',
            'amount' => 50000,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('amount');
    }
}
