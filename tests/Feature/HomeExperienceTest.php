<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Withdrawal;
use App\Modules\StakeRounds\Models\Stake;
use App\Modules\StakeRounds\Models\StakeRound;
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

    public function test_homepage_payload_includes_forex_rates_and_empty_top_miners_without_auto_seeding(): void
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
            'top_miners',
            'prices',
        ]);

        $this->assertSame([], $response->json('top_miners'));
        $this->assertSame(0, User::count());
        $this->assertSame(0, Stake::count());
        $this->assertNotEmpty($response->json('prices'));
    }

    public function test_homepage_top_miners_returns_only_real_stake_participants(): void
    {
        $miner = User::factory()->create(['is_admin' => false, 'is_active' => true]);
        $round = StakeRound::create([
            'server_seed' => 'real-round-seed',
            'server_seed_hash' => hash('sha256', 'real-round-seed'),
            'status' => 'settled',
            'volatility_bp' => 80,
            'tick_ms' => 100,
            'pool_total' => '50.000000',
            'cap_total' => '45.000000',
            'paid_total' => '40.000000',
            'house_take' => '5.000000',
            'opens_at' => now()->subMinutes(10),
            'starts_at' => now()->subMinutes(9),
            'ended_at' => now()->subMinutes(8),
        ]);
        Stake::create([
            'stake_round_id' => $round->id,
            'user_id' => $miner->id,
            'side' => 'usd',
            'amount' => '20.000000',
            'payout' => '40.000000',
            'cashout_multiplier_ppm' => 2000000,
            'status' => 'cashed',
            'cashed_at' => now()->subMinutes(8),
        ]);

        $response = $this->getJson('/api/home-public')->assertOk();

        $response->assertJsonCount(1, 'top_miners')
            ->assertJsonPath('top_miners.0.rank', 1)
            ->assertJsonPath('top_miners.0.user', $miner->maskedIdentity())
            ->assertJsonPath('top_miners.0.machine', 'USD Stake Round · 2.00x');
    }
}
