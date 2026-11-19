<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ForexService
{
    /** Standard fallback rates if external API is unreachable */
    public const DEFAULT_RATES = [
        'USD_UGX' => 3750.0,
        'EUR_UGX' => 4050.0,
        'EUR_USD' => 1.08,
    ];

    public function rates(): array
    {
        return Cache::remember('forex_rates', 300, function () {
            $overrides = Setting::get('forex_rates', []);
            $live = $this->fetchLive();

            $usdToUgx = (float) ($overrides['USD_UGX'] ?? $live['USD_UGX'] ?? self::DEFAULT_RATES['USD_UGX']);
            $eurToUgx = (float) ($overrides['EUR_UGX'] ?? $live['EUR_UGX'] ?? self::DEFAULT_RATES['EUR_UGX']);
            $eurToUsd = (float) ($overrides['EUR_USD'] ?? ($usdToUgx > 0 ? $eurToUgx / $usdToUgx : self::DEFAULT_RATES['EUR_USD']));

            return [
                'USD_UGX' => round($usdToUgx, 2),
                'EUR_UGX' => round($eurToUgx, 2),
                'EUR_USD' => round($eurToUsd, 4),
                'UGX_USD' => round(1 / max(1, $usdToUgx), 6),
                'UGX_EUR' => round(1 / max(1, $eurToUgx), 6),
                'rates' => [
                    'USD' => [
                        'currency' => 'USD',
                        'symbol' => '$',
                        'rate' => round($usdToUgx, 2),
                        'formatted' => '1 USD ≈ '.number_format($usdToUgx, 0).' UGX',
                        'change_pct' => 0.25,
                    ],
                    'EUR' => [
                        'currency' => 'EUR',
                        'symbol' => '€',
                        'rate' => round($eurToUgx, 2),
                        'formatted' => '1 EUR ≈ '.number_format($eurToUgx, 0).' UGX',
                        'change_pct' => 0.18,
                    ],
                ],
                'updated_at' => now()->toIso8601String(),
            ];
        });
    }

    public function rate(string $from, string $to): float
    {
        $from = strtoupper(trim($from));
        $to = strtoupper(trim($to));

        if ($from === $to) {
            return 1.0;
        }

        $rates = $this->rates();

        if ($from === 'USD' && $to === 'UGX') {
            return (float) $rates['USD_UGX'];
        }
        if ($from === 'UGX' && $to === 'USD') {
            return (float) $rates['UGX_USD'];
        }
        if ($from === 'EUR' && $to === 'UGX') {
            return (float) $rates['EUR_UGX'];
        }
        if ($from === 'UGX' && $to === 'EUR') {
            return (float) $rates['UGX_EUR'];
        }
        if ($from === 'EUR' && $to === 'USD') {
            return (float) $rates['EUR_USD'];
        }
        if ($from === 'USD' && $to === 'EUR') {
            return round(1 / max(0.0001, (float) $rates['EUR_USD']), 4);
        }

        throw new RuntimeException("Unsupported conversion from {$from} to {$to}");
    }

    public function convert(User $user, string $from, string $to, float $amount): array
    {
        $from = strtoupper(trim($from));
        $to = strtoupper(trim($to));

        if ($from === $to) {
            throw new RuntimeException('Cannot convert to the same currency.');
        }

        if ($amount <= 0) {
            throw new RuntimeException('Conversion amount must be greater than zero.');
        }

        $rate = $this->rate($from, $to);
        $converted = round($amount * $rate, 6);

        return DB::transaction(function () use ($user, $from, $to, $amount, $rate, $converted) {
            $u = User::whereKey($user->id)->lockForUpdate()->firstOrFail();

            $fromCol = match ($from) {
                'UGX' => 'invest_balance',
                'USD' => 'usd_balance',
                'EUR' => 'eur_balance',
                default => throw new RuntimeException("Invalid source currency {$from}"),
            };

            $toCol = match ($to) {
                'UGX' => 'invest_balance',
                'USD' => 'usd_balance',
                'EUR' => 'eur_balance',
                default => throw new RuntimeException("Invalid target currency {$to}"),
            };

            $currentFrom = (float) $u->{$fromCol};
            if ($currentFrom < $amount) {
                throw new RuntimeException("Insufficient {$from} balance. You have ".number_format($currentFrom, 2)." {$from}.");
            }

            $u->{$fromCol} = bcsub((string) $u->{$fromCol}, (string) $amount, 6);
            $u->{$toCol} = bcadd((string) $u->{$toCol}, (string) $converted, 6);
            $u->save();

            Transaction::create([
                'user_id' => $u->id,
                'wallet' => 'invest',
                'type' => 'forex_conversion',
                'amount' => $from === 'UGX' ? bcmul('-1', (string) $amount, 6) : (string) $converted,
                'balance_after' => $u->invest_balance,
                'meta' => [
                    'from' => $from,
                    'to' => $to,
                    'from_amount' => $amount,
                    'to_amount' => $converted,
                    'rate' => $rate,
                ],
            ]);

            return [
                'from' => $from,
                'to' => $to,
                'amount' => $amount,
                'rate' => $rate,
                'converted_amount' => $converted,
                'balances' => [
                    'ugx' => (string) $u->invest_balance,
                    'usd' => (string) $u->usd_balance,
                    'eur' => (string) $u->eur_balance,
                ],
            ];
        });
    }

    private function fetchLive(): array
    {
        try {
            $resp = Http::withoutVerifying()->timeout(3)->get('https://open.er-api.com/v6/latest/USD');
            if ($resp->ok()) {
                $rates = $resp->json('rates');
                if (isset($rates['UGX'], $rates['EUR'])) {
                    $usdToUgx = (float) $rates['UGX'];
                    $eurToUsd = 1 / max(0.0001, (float) $rates['EUR']);
                    $eurToUgx = $eurToUsd * $usdToUgx;

                    return [
                        'USD_UGX' => $usdToUgx,
                        'EUR_UGX' => $eurToUgx,
                        'EUR_USD' => $eurToUsd,
                    ];
                }
            }
        } catch (\Throwable) {
            // Fallback handled
        }

        return self::DEFAULT_RATES;
    }
}
