<?php

return [
    'prism' => [
        'api_key' => env('PRISM_API_KEY'),
        'base_url' => env('PRISM_BASE_URL', 'https://api.prism.echolabs.dev'),
        'default_model' => env('PRISM_DEFAULT_MODEL', 'gpt-4'),
    ],
    
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
];
