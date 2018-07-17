<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Options
    |--------------------------------------------------------------------------
    |
    | Api client global options.
    |
    */
    'options'  => [
        // 是否开启调试模式
        'debug'              => env('API_CLIENT_DEBUG', false),

        // 所有服务是否需要签名
        'api_sign'           => false,

        // 签名所用算法
        'api_sign_algorithm' => 'hmac-sha256',

        // 超时时间
        'timeout'            => 10.0,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Options
    |--------------------------------------------------------------------------
    |
    | Api client services options.
    |
    */
    'services' => [
        'area-api'      => [
            'base_uri' => env('AREA_URL'),
        ],
        'truck-log-api' => [
            'api_sign'   => true,
            'api_key'    => env('API_CLIENT_TRUCK_LOG_KEY'),
            'api_secret' => env('API_CLIENT_TRUCK_LOG_SECRET'),
            'base_uri'   => env('API_CLIENT_TRUCK_LOG_BASE_URI'),
        ],
        'order56-api'   => [
            'api_sign'   => true,
            'api_key'    => env('API_CLIENT_ORDER_KEY'),
            'api_secret' => env('API_CLIENT_ORDER_SECRET'),
            'base_uri'   => env('API_CLIENT_ORDER_BASE_URI'),
        ],
    ],
];