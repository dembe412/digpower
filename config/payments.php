<?php

return [
    'methods' => [
        'mtn' => [
            'key' => 'mtn',
            'name' => 'MTN Mobile Money',
            'number' => env('PAYMENT_MTN_NUMBER', '0770000000'),
            'account_name' => env('PAYMENT_MTN_NAME', 'DigiPowerX'),
            'currency' => 'UGX',
            'instructions' => 'Send exact amount via MTN Mobile Money (*165#), then enter your transaction ID.',
        ],
        'airtel' => [
            'key' => 'airtel',
            'name' => 'Airtel Money',
            'number' => env('PAYMENT_AIRTEL_NUMBER', '0750000000'),
            'account_name' => env('PAYMENT_AIRTEL_NAME', 'DigiPowerX'),
            'currency' => 'UGX',
            'instructions' => 'Send exact amount via Airtel Money (*185#), then enter your transaction ID.',
        ],
        'trc20' => [
            'key' => 'trc20',
            'name' => 'USDT (TRC20)',
            'number' => env('DEPOSIT_ADDR_TRC20', 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t'),
            'account_name' => 'DigiPowerX USDT TRC20 Wallet',
            'currency' => 'USDT',
            'instructions' => 'Send exact USDT on the Tron (TRC20) network, then paste your transaction hash (TXID).',
        ],
    ],
];
