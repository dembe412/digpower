<?php

namespace Database\Seeders;

use App\Models\Deposit;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\YieldBatch;
use App\Services\DepositService;
use App\Services\WalletService;
use App\Services\YieldService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoScenarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demo = User::updateOrCreate(['email' => 'demo@digipowerx.test'], [
            'name' => 'Demo client',
            'password' => Hash::make('DemoPass123!'),
            'invite_code' => 'DEMO01',
            'is_active' => true,
            'is_admin' => false,
            'archived_at' => null,
            'terms_accepted_at' => now(),
        ]);

        $referral = User::updateOrCreate(['email' => 'referral@digipowerx.test'], [
            'name' => 'Demo referral',
            'password' => Hash::make('DemoPass123!'),
            'invite_code' => 'DEMO02',
            'referrer_id' => $demo->id,
            'is_active' => true,
            'is_admin' => false,
            'archived_at' => null,
            'terms_accepted_at' => now(),
        ]);

        $depositService = app(DepositService::class);
        $this->confirmDeposit($demo, '250.00', '0x-demo-client-250', $depositService);
        $this->confirmDeposit($referral, '1000.00', '0x-demo-referral-1000', $depositService);

        $batch = YieldBatch::whereDate('period_date', now()->toDateString())->first();
        if (! $batch) {
            $batch = YieldBatch::create([
                'period_date' => now()->toDateString(),
                'gross_revenue' => '125.00',
                'fee_pct' => '10.00',
                'proof_url' => 'https://example.com/demo-revenue-evidence',
                'created_by' => User::where('is_admin', true)->value('id'),
            ]);
        }
        if ($batch->status === 'draft') {
            app(YieldService::class)->distribute($batch);
        }

        $wallet = app(WalletService::class);
        if (! Withdrawal::where('user_id', $demo->id)->where('note', 'Demo scenario withdrawal')->exists()
            && (float) $demo->fresh()->commission_balance >= 10) {
            $amount = '10.000000';
            $wallet->debit($demo, 'commission', $amount, 'withdrawal');
            Withdrawal::create([
                'user_id' => $demo->id,
                'wallet' => 'commission',
                'network' => 'TRC20',
                'address' => 'TDemoWalletAddressForPreviewOnly',
                'amount' => $amount,
                'fee' => '0.100000',
                'net_amount' => '9.900000',
                'status' => 'completed',
                'txid' => 'demo-withdrawal-10',
                'reviewed_by' => User::where('is_admin', true)->value('id'),
                'note' => 'Demo scenario withdrawal',
            ]);
        }
    }

    private function confirmDeposit(User $user, string $amount, string $txid, DepositService $service): void
    {
        $deposit = Deposit::firstOrCreate(['txid' => $txid], [
            'user_id' => $user->id,
            'network' => 'TRC20',
            'amount' => $amount,
            'address' => 'TDemoDepositAddressForPreviewOnly',
        ]);
        if ($deposit->status === 'pending') {
            $service->confirm($deposit, User::where('is_admin', true)->value('id'));
        }
    }
}
