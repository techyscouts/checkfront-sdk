<?php

declare(strict_types=1);

return [
    'auth' => [
        'type' => 'basic',
        'basic' => [
            'api_key' => env('CHECKFRONT_API_KEY'),
            'api_secret' => env('CHECKFRONT_API_SECRET'),
        ],
        'oauth2' => [
            'access_token' => env('CHECKFRONT_OAUTH2_ACCESS_TOKEN'),
            'refresh_token' => env('CHECKFRONT_OAUTH2_REFRESH_TOKEN'),
            'expires_at' => env('CHECKFRONT_OAUTH2_EXPIRES_AT'),
        ],
    ],
    'host' => env('CHECKFRONT_HOST'),
];
