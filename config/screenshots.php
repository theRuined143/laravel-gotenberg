<?php

return [
    // Bearer token required by every screenshot API request. Leave empty to reject everything.
    'api_token' => env('API_TOKEN'),

    'gotenberg' => [
        'url' => env('GOTENBERG_URL', 'http://gotenberg:3000'),
        'timeout' => (int) env('GOTENBERG_TIMEOUT', 30),
    ],

    'disk' => env('SCREENSHOT_DISK', 'local'),
    'directory' => 'screenshots',

    // The bundled HTTP API. Set 'enabled' to false to use only the GotenbergClient in your own code.
    'routes' => [
        'enabled' => true,
        'prefix' => 'gotenberg/screenshots',
        // null uses the package's ['api', EnsureValidApiToken]. Set an array (e.g. ['api', 'auth:sanctum'])
        // to replace it entirely with your app's own middleware, including its auth.
        'middleware' => null,
    ],
];
