<?php

return [
    'name' => env('PLATFORM_NAME', 'DigiPowerX'),
    'currency' => env('PLATFORM_CURRENCY', 'UGX'),
    'frontend_url' => env('FRONTEND_URL', env('APP_URL', 'http://127.0.0.1:8000')),

    // Hashrate credited per 1 UGX of active deposit (display + pro-rata weight for yield distribution).
    'hashrate_per_usd' => (float) env('HASHRATE_PER_USD', 0.5),

    // Earnings records must be claimed within this many days or they expire.
    'claim_window_days' => (int) env('CLAIM_WINDOW_DAYS', 7),

    'withdrawal' => [
        'min' => env('WITHDRAW_MIN', '5000'),
        'fee_pct' => env('WITHDRAW_FEE_PCT', '7'),
        'networks' => ['TRC20', 'ERC20', 'BEP20'],
    ],

    'deposit' => [
        'min' => env('DEPOSIT_MIN', '5000'),
        'networks' => [
            'TRC20' => env('DEPOSIT_ADDR_TRC20'),
            'ERC20' => env('DEPOSIT_ADDR_ERC20'),
            'BEP20' => env('DEPOSIT_ADDR_BEP20'),
        ],
        'webhook_secret' => env('DEPOSIT_WEBHOOK_SECRET'),
    ],

    // Referral rewards, paid from the platform's own fee margin (never from other users' principal).
    // One entry per level (% of the referred user's confirmed deposit). Default: level 1 only.
    'referral_rates' => [
        1 => (float) env('REFERRAL_L1', 10),
        2 => (float) env('REFERRAL_L2', 5),
        3 => (float) env('REFERRAL_L3', 1),
    ],
    'invite_code_required' => (bool) env('INVITE_CODE_REQUIRED', false),

    'prices' => [
        'pairs' => ['BTC', 'ETH', 'BCH', 'BNB', 'XRP', 'ADA', 'SOL', 'DOGE', 'DOT', 'LTC', 'TRX'],
        'cache_seconds' => 30,
    ],
];
