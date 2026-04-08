<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Smstools API Credentials
    |--------------------------------------------------------------------------
    |
    | Your API client ID and secret from the Smstools dashboard.
    | Navigate to Advanced → API authentication to retrieve these values.
    |
    */
    'client_id' => env('SMSTOOLS_CLIENT_ID'),

    'client_secret' => env('SMSTOOLS_CLIENT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the Smstools Gateway API. Override only if you are
    | using a proxied or sandboxed endpoint.
    |
    */
    'base_url' => env('SMSTOOLS_BASE_URL', 'https://api.smsgatewayapi.com/v1'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Timeout
    |--------------------------------------------------------------------------
    |
    | Number of seconds to wait for an API response before timing out.
    |
    */
    'timeout' => (int) env('SMSTOOLS_TIMEOUT', 30),
];
