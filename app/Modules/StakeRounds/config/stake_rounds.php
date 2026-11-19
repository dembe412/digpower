<?php

use App\Modules\StakeRounds\Adapters\PlatformWalletGateway;

return [
    'enabled' => (bool) env('STAKE_ROUNDS_ENABLED', true),
    'name' => env('STAKE_ROUNDS_NAME', 'Stake Rounds'),

    // ---- Integration points (swap these to plug into an existing platform)
    'wallet' => env('STAKE_WALLET', 'invest'),                       // wallet that stakes are taken from / paid into
    'wallet_gateway' => PlatformWalletGateway::class,
    'admin_middleware' => ['admin'],                                 // e.g. ['admin'] or ['admin', 'super.admin']
    'route_prefixes' => array_filter(explode(',', env('STAKE_ROUTE_PREFIXES', 'stake'))), // 'stake,mining' => Mining tab opens this module

    'open_seconds' => (int) env('STAKE_OPEN_SECONDS', 15),   // staking window before a round starts
    'max_run_seconds' => (int) env('STAKE_MAX_RUN_SECONDS', 120),
    'tick_ms' => (int) env('STAKE_TICK_MS', 500),            // cash-outs are batched per tick (anti-bot fairness)

    // The round ends when total cash-outs reach this share of the pool. The remainder is the house take.
    'payout_cap_pct' => (string) env('STAKE_PAYOUT_CAP_PCT', '90'),

    'min_players' => (int) env('STAKE_MIN_PLAYERS', 2),      // fewer players => round voided, stakes refunded
    'min_stake' => (string) env('STAKE_MIN', '1'),
    'max_stake' => (string) env('STAKE_MAX', '500'),
    'default_daily_limit' => (string) env('STAKE_DAILY_LIMIT', '100'),
    'limit_increase_delay_hours' => 24,
    'min_age' => 18,

    'rate' => [
        // 'simulated' = provably fair pseudo-random index (NOT the real exchange rate).
        'provider' => env('STAKE_RATE_PROVIDER', 'simulated'),
        'volatility_bp' => (int) env('STAKE_VOLATILITY_BP', 60), // max +/- move per tick, in basis points
    ],

    'notice' => 'Stake Rounds is a game of chance. You can lose your entire stake. '
        .'The rate shown is a simulated index, not the real USD/UGX exchange rate. 18+ only.',
];
