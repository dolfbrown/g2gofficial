<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'cybersource' => [
        'auth' => 'http_signature',
        'merchant' => env('SYBERSOURCE_MERCHANT_ID'),
        'key' => env('SYBERSOURCE_API_KEY'),
        'secret' => env('SYBERSOURCE_SECRET'),
        'sandbox' => env('SYBERSOURCE_SANDBOX', TRUE) ? 'SANDBOX' : 'PRODUCTION',
    ],

    'instamojo' => [
        'key' => env('INSTAMOJO_API_KEY'),
        'token' => env('INSTAMOJO_AUTH_TOKEN'),
        'sandbox' => env('INSTAMOJO_SANDBOX', TRUE),
    ],

    'paystack' => [
        'secret' => env('PAYSTACK_SECRET'),
    ],

    'authorizenet' => [
        'id' => env('AUTHORIZENET_API_LOGIN_ID'),
        'key' => env('AUTHORIZENET_TRANSACTION_KEY'),
        'sandbox' => env('AUTHORIZENET_SANDBOX', TRUE),
    ],

    'facebook' => [
        'client_id'     => env('FB_CLIENT_ID'),
        'client_secret' => env('FB_CLIENT_SECRET'),
        'redirect'      => env('FB_REDIRECT_URL'),
    ],

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URL'),
    ],

    'recaptcha' => [
        'key' => env('GOOGLE_RECAPTCHA_KEY'),
        'secret' => env('GOOGLE_RECAPTCHA_SECRET'),
    ],

];
