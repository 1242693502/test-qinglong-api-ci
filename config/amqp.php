<?php

return [
    //rabbitmq
    'rabbitmq' => [
        'host'     => env('RABBITMQ_HOST'),
        'port'     => env('RABBITMQ_PORT'),
        'user'     => env('RABBITMQ_USER'),
        'password' => env('RABBITMQ_PASSWORD'),
        'vhost'    => env('RABBITMQ_VHOST'),
    ],
];