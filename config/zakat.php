<?php

return [

    'nisap_grams' => (float) env('ZAKAT_NISAP_GRAMS', 80),

    'nisap_karat' => (int) env('ZAKAT_NISAP_KARAT', 24),

    'rate' => (float) env('ZAKAT_RATE', 0.025),

    'karat_factors' => [
        24 => 1.0,
        22 => 0.916,
        18 => 0.75,
        14 => 0.585,
    ],

    'cache' => [
        'forex_ttl' => (int) env('ZAKAT_FOREX_CACHE_TTL', 60),
        'metals_ttl' => (int) env('ZAKAT_METALS_CACHE_TTL', 90),
        'snapshot_key' => 'zakat_prices_snapshot',
    ],

];
