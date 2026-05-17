<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Currency Pricing Configuration
    |--------------------------------------------------------------------------
    |
    | All prices for portraits and clocks per currency.
    | Tier1 = normal price
    | Tier2 = bulk price (applies when bulk_threshold reached)
    |
    */

    'currencies' => [

        'KES' => [
            'symbol' => 'KSh',

            'portraits' => [
                'tier1' => 250,
                'tier2' => 190,
            ],

            'clocks' => [
                'tier1' => 700,
                'tier2' => 500,
            ],

            'delivery' => 300,
        ],

        'UGX' => [
            'symbol' => 'UGX',

            'portraits' => [
                'tier1' => 28000,
                'tier2' => 15000,
            ],

            'clocks' => [
                'tier1' => 50000,
                'tier2' => 38000,
            ],

            'delivery' => 10000,
        ],

        'TZS' => [
            'symbol' => 'TSh',

            'portraits' => [
                'tier1' => 5000,
                'tier2' => 4000,
            ],

            'clocks' => [
                'tier1' => 45000,
                'tier2' => 32000,
            ],

            'delivery' => 3000,
        ],

        'RWF' => [
            'symbol' => 'FRw',

            'portraits' => [
                'tier1' => 2500,
                'tier2' => 2000,
            ],

            'clocks' => [
                'tier1' => 20000,
                'tier2' => 12000,
            ],

            'delivery' => 1500,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Business Rules
    |--------------------------------------------------------------------------
    */

    'bulk_threshold' => 5,

];