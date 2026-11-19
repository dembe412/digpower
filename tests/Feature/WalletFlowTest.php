<?php

namespace Tests\Feature;

use App\Models\Deposit;
use App\Models\User;
use App\Models\YieldBatch;
use App\Services\DepositService;
use App\Services\WalletService;
use App\Services\YieldService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_deposit_confirm_credits_once_and_rewards_referrer(): void
    {
        $this->seed(DatabaseSeeder::class);
        $ref = User::factory()->create(['invite_code' => '111111']);
        $u = User::factory()->create(['invite_code' => '222222', 'referrer_id' => $ref->id]);
        $d = Deposit::create(['user_id' => $u->id, 'network' => 'TRC20', 'amount' => 100, 'txid' => str_repeat('a', 20)]);

        $svc = app(DepositService::class);
        $svc->confirm($d);
        $svc->confirm($d); // idempotent

        $this->assertEquals('100.000000', $u->fresh()->invest_balance);
        $this->assertEquals('10.000000', $ref->fresh()->commission_balance);
    }

    public function test_yield_distribution_is_pro_rata_and_locked_principal_not_withdrawable(): void
    {
        $this->seed(DatabaseSeeder::class);
        $a = User::factory()->create(['invite_code' => '333333']);
        $b = User::factory()->create(['invite_code' => '444444']);
        foreach ([[$a, 100], [$b, 300]] as [$u, $amt]) {
            app(DepositService::class)->confirm(Deposit::create(['user_id' => $u->id, 'network' => 'TRC20', 'amount' => $amt, 'txid' => bin2hex(random_bytes(10))]));
        }
        $batch = YieldBatch::create(['period_date' => today(), 'gross_revenue' => 40, 'fee_pct' => 10, 'proof_url' => 'https://example.com/p']);
        app(YieldService::class)->distribute($batch);

        $wallet = app(WalletService::class);
        $this->assertEquals('9.000000', app(YieldService::class)->claim($a, $wallet));   // 36 * 1/4
        $this->assertEquals('27.000000', app(YieldService::class)->claim($b, $wallet));  // 36 * 3/4
        $this->assertEquals('9.000000', $wallet->withdrawable($a->fresh(), 'invest'));   // principal stays locked
    }
}
