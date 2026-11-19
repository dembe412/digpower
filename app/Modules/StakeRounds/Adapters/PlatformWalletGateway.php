<?php

namespace App\Modules\StakeRounds\Adapters;

use App\Modules\StakeRounds\Contracts\WalletGateway;
use App\Services\WalletService;
use Illuminate\Database\Eloquent\Model;

/** Adapter for this reference platform's WalletService. Replace with one for your own wallet code. */
class PlatformWalletGateway implements WalletGateway
{
    public function __construct(private WalletService $wallet) {}

    public function spendable($user, string $wallet): string
    {
        return $this->wallet->withdrawable($user, $wallet);
    }

    public function debit($user, string $wallet, string $amount, string $type, ?Model $ref = null, array $meta = []): mixed
    {
        return $this->wallet->debit($user, $wallet, $amount, $type, $ref, $meta);
    }

    public function credit($user, string $wallet, string $amount, string $type, ?Model $ref = null, array $meta = []): mixed
    {
        return $this->wallet->credit($user, $wallet, $amount, $type, $ref, $meta);
    }
}
