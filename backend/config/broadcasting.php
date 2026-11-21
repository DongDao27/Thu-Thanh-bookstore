<?php

return [
    // Local/tests never contact Pusher without explicit operator configuration.
    'default' => env('BROADCAST_CONNECTION', 'null'),
    'connections' => [
        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER', 'ap1'),
                'useTLS' => true,
            ],
            'client_options' => ['connect_timeout' => 3, 'timeout' => 5],
        ],
        'null' => ['driver' => 'null'],
        'log' => ['driver' => 'log'],
    ],
];
