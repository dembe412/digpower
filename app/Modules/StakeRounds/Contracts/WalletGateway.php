<?php

namespace App\Modules\StakeRounds\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * The ONLY way the module touches money. Implement this against your platform's wallet/ledger and
 * point config('stake_rounds.wallet_gateway') at it. Every method must be atomic and write a ledger row.
 */
interface WalletGateway
{
    /** Balance the user may freely spend right now (exclude locked/pending funds), as a 6-decimal string. */
    public function spendable($user, string $wallet): string;

    /** Remove funds. Must throw \RuntimeException if the balance is insufficient. */
    public function debit($user, string $wallet, string $amount, string $type, ?Model $ref = null, array $meta = []): mixed;

    public function credit($user, string $wallet, string $amount, string $type, ?Model $ref = null, array $meta = []): mixed;
}
