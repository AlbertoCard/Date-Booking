<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'stripe' => [
        'key' => env('pk_test_51RRqn4FT8eXT037JrkUBM8eCnXoE0RxVhAcWog91LJVBUwy2lE2Zchpy107cME7vYCAT8FrkEYwCAZJbDNxRxyQG00VVb0KKAf'),
        'secret' => env('sk_test_51RRqn4FT8eXT037JyN0N8t9toWuqkqbfw0Ufnvap54X1sBwaavvCqYU8rkbDblX1W6uNk3TBiz8o3ZSkfCDUfJQf00dhnRYxZ9'),
    ],


];
