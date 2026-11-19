<?php

namespace App\Modules\StakeRounds\Services;

/**
 * Provably-fair simulated USD/UGX index. Fully deterministic from (server_seed, client_seed, round_id, tick),
 * integer arithmetic only, so anyone can re-compute it after the server seed is revealed.
 *
 *   step_bp(k) = (uint32(HMAC_SHA256(server_seed, "client_seed:round_id:k")[0..3]) mod (2V+1)) - V
 *   index(k)   = index(k-1) + trunc(index(k-1) * step_bp(k) / 10000),   index(0) = 1_000_000
 *
 * USD side multiplier = index / 1e6 ; UGX side multiplier = 1e6 / index   (both in ppm below).
 */
class RateIndex
{
    public const BASE = 1_000_000;

    private array $memo = [];

    public function index(string $serverSeed, string $clientSeed, int $roundId, int $volBp, int $tick): int
    {
        $key = $serverSeed.'|'.$clientSeed.'|'.$roundId.'|'.$volBp;
        $series = &$this->memo[$key];
        $series ??= [self::BASE];

        for ($k = count($series); $k <= $tick; $k++) {
            $h = hash_hmac('sha256', "{$clientSeed}:{$roundId}:{$k}", $serverSeed, true);
            $r = unpack('N', substr($h, 0, 4))[1];
            $step = ($r % (2 * $volBp + 1)) - $volBp;
            $prev = $series[$k - 1];
            $series[$k] = max(1000, $prev + intdiv($prev * $step, 10000));
        }

        return $series[$tick];
    }

    /** @return array{usd:int, ugx:int} multipliers in ppm (1_000_000 = 1.00x) */
    public function multipliers(string $serverSeed, string $clientSeed, int $roundId, int $volBp, int $tick): array
    {
        $i = $this->index($serverSeed, $clientSeed, $roundId, $volBp, $tick);

        return ['usd' => $i, 'ugx' => intdiv(self::BASE * self::BASE, $i)];
    }

    public function series(string $serverSeed, string $clientSeed, int $roundId, int $volBp, int $toTick): array
    {
        $this->index($serverSeed, $clientSeed, $roundId, $volBp, $toTick);

        return array_slice($this->memo[$serverSeed.'|'.$clientSeed.'|'.$roundId.'|'.$volBp], 0, $toTick + 1);
    }
}
