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
        'url' => env('EMIRATES_API_URL', 'https://ek.farelogix.com:443/sandbox-uat/oc'),
        'role' => env('EMIRATES_ROLE', 'Ticketing Agent'),
        'agency_name' => env('EMIRATES_AGENCY_NAME', 'DestinationsTravelTour-ek-dispatch.flxdm'),
        'user' => env('EMIRATES_USER', 'otadestinations'),
        'u' => env('EMIRATES_U', 'otadestinations'),
        'passwordIden' => env('EMIRATES_PASSWORD_IDEN', 'Paktg24580'),
        'agtPassword' => env('EMIRATES_PASSWORD_AGT', 'Paktg24580'),
        'agency_id' => env('EMIRATES_AGENCY_ID', '27301245'),
        'subscription_key' => env('EMIRATES_SUBSCRIPTION_KEY', 'ec71e1de4e224e82bac30f5a3c4c2803'),
        'pcc' => env('EMIRATES_PCC', 'ETXO')
    ],

];
