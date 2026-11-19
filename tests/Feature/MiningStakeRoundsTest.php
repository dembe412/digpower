<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\StakeRounds\Models\StakeProfile;
use App\Modules\StakeRounds\Services\RoundEngine;
use App\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MiningStakeRoundsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_mining_state(): void
    {
        $this->getJson('/api/mining/current')->assertUnauthorized();
        $this->getJson('/api/mining')->assertUnauthorized();
    }

    public function test_member_can_view_current_mining_state(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/mining/current');
        $response->assertOk()
            ->assertJsonPath('name', 'Stake Rounds')
            ->assertJsonPath('adult_confirmed', false);
    }

    public function test_member_must_confirm_adult_before_staking(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        app(WalletService::class)->credit($user, 'invest', '100.000000', 'deposit');
        Sanctum::actingAs($user);

        $this->postJson('/api/mining/enter', [
            'side' => 'usd',
            'amount' => 10,
        ])->assertUnprocessable();

        $this->postJson('/api/mining/profile/confirm-adult', [
            'date_of_birth' => '1995-05-15',
            'accept' => true,
        ])->assertOk();

        $this->assertTrue(StakeProfile::where('user_id', $user->id)->first()->adult_confirmed_at !== null);
    }

    public function test_member_can_enter_round_and_wallet_is_debited(): void
    {
        $engine = app(RoundEngine::class);
        $t0 = Carbon::parse('2026-11-15 12:00:00');
        Carbon::setTestNow($t0);
        $engine->advance($t0);

        $user = User::factory()->create(['is_active' => true]);
        app(WalletService::class)->credit($user, 'invest', '200.000000', 'deposit');
        StakeProfile::create([
            'user_id' => $user->id,
            'adult_confirmed_at' => now(),
            'daily_limit' => '1000.000000',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/mining/enter', [
            'side' => 'usd',
            'amount' => 50,
        ]);

        $response->assertCreated()
            ->assertJsonPath('stake.side', 'usd')
            ->assertJsonPath('stake.status', 'active');

        $this->assertTrue(bccomp((string) $user->fresh()->invest_balance, '150', 6) === 0);
        $this->assertDatabaseHas('stakes', [
            'user_id' => $user->id,
            'side' => 'usd',
            'amount' => '50.000000',
        ]);
    }

    public function test_admin_can_access_mining_summary(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $member = User::factory()->create(['is_admin' => false, 'is_active' => true]);

        Sanctum::actingAs($member);
        $this->getJson('/api/admin/mining/summary')->assertForbidden();

        Sanctum::actingAs($admin);
        $this->getJson('/api/admin/mining/summary')
            ->assertOk()
            ->assertJsonStructure(['rounds_settled', 'rounds_void', 'total_staked', 'house_take']);
    }
}
