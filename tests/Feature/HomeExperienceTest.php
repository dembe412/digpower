<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_displays_the_digipowerx_member_experience(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('DigiPowerX')
            ->assertSee('Home')
            ->assertSee('Sign in');
    }

    public function test_member_api_requires_authentication(): void
    {
        $this->getJson('/api/me')->assertUnauthorized();
    }

    public function test_public_home_shows_only_the_five_newest_completed_withdrawals_with_masked_identities(): void
    {
        $users = User::factory()->count(6)->create(['show_in_public_log' => false]);
        $users[0]->update(['avatar_url' => 'https://cdn.example.test/avatar.png']);
        foreach ($users as $index => $user) {
            Withdrawal::create([
                'user_id' => $user->id,
                'wallet' => 'commission',
                'network' => 'TRC20',
                'address' => 'T'.str_repeat((string) ($index + 1), 33),
                'amount' => $index + 10,
                'fee' => 0,
                'net_amount' => $index + 10,
                'status' => 'completed',
                'created_at' => now()->subMinutes($index),
                'updated_at' => now()->subMinutes($index),
            ]);
        }
        Withdrawal::create([
            'user_id' => $users[0]->id,
            'wallet' => 'commission',
            'network' => 'TRC20',
            'address' => 'T'.str_repeat('9', 33),
            'amount' => 999,
            'fee' => 0,
            'net_amount' => 999,
            'status' => 'pending',
        ]);

        $response = $this->getJson('/api/home-public')->assertOk();

        $response->assertJsonCount(5, 'withdraw_log')
            ->assertJsonPath('withdraw_log.0.amount', '10.000000')
            ->assertJsonPath('withdraw_log.4.amount', '14.000000')
            ->assertJsonPath('withdraw_log.0.avatar_url', 'https://cdn.example.test/avatar.png')
            ->assertJsonPath('withdraw_log.1.avatar_url', null);
    }

    public function test_homepage_payload_includes_forex_rates_and_top_miners(): void
    {
        $response = $this->getJson('/api/home-public')->assertOk();

        $response->assertJsonStructure([
            'forex' => [
                'USD_UGX',
                'EUR_UGX',
                'EUR_USD',
                'rates' => [
                    'USD' => ['currency', 'rate', 'formatted', 'change_pct'],
                    'EUR' => ['currency', 'rate', 'formatted', 'change_pct'],
                ],
            ],
            'top_miners' => [
                '*' => [
                    'rank',
                    'user',
                    'machine',
                    'hashrate',
                    'daily_output',
                    'total_mined',
                    'status',
                ],
            ],
            'prices',
        ]);

        $this->assertNotEmpty($response->json('top_miners'));
        $this->assertNotEmpty($response->json('prices'));
    }
}
