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

    'agency' => [
        'name' => env('AGENCY_NAME'),
        'email' => env('AGENCY_EMAIL'),
    ],

    'flyjinnah_api' => [
        'authenticate' => env('FLYJINNAH_API_AUTHENTICATE'),
        'search' => env('FLYJINNAH_API_SEARCH'),
        'flight_details' => env('FLYJINNAH_API_FLIGHT_DETAILS'),
        'username' => env('FLYJINNAH_API_USERNAME'),
        'password' => env('FLYJINNAH_API_PASSWORD'),
        'agent_code' => env('FLYJINNAH_AGENT_CODE'),
    ],

    'pia_api' => [
        'url' => env('PIA_API_URL'),
        'username' => env('PIA_API_USERNAME'),
        'password' => env('PIA_API_PASSWORD'),
        'email' => env('PIA_API_EMAIL'),
    ],

    'emirates_api' => [
        'url' => env('EMIRATES_API_URL'),
        'user' => env('EMIRATES_USER'),
        'password' => env('EMIRATES_PASSWORD'),
        'agency_id' => env('EMIRATES_AGENCY_ID'),
        'subscription_key' => env('EMIRATES_SUBSCRIPTION_KEY'),
        'pcc' => env('EMIRATES_PCC')
    ],

];
