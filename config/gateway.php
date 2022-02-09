<?php


return [

    'prefix' => env('APP_VERSION'),
    'domain' => env('GATEWAY_DOMAIN', '127.0.0.1'),
    'services' => [
        'api_1' => env('GATEWAY_STORE_SERVICE', '127.0.0.1'),

        'api_2' => env('GATEWAY_ASSET_SERVICE', '127.0.0.1'),

        'api_3' => env('GATEWAY_RAJAONGKIR_SERVICE', '127.0.0.1'),
    ],

    'timeout' => env('GATEWAY_TIMEOUT', 60.0),

    "connect_timeout" => 60.0,

    "connection" => [
        "asset" => [
            "api_url" => env("ASSET_GATEWAY_SERVICE", "http://localhost:9494/api/v1")
        ],
        "ongkir" => [
            "api_url" => env("ONGKIR_GATEWAY_SERVICE", "http://localhost:9797/api/v3"),
        ],
    ]

];