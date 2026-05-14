<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    // CORS will apply to all API routes and payment routes
    'paths' => [
        'api/*',
        'payment/*',
    ],

    // Allow all HTTP methods (GET, POST, PUT, DELETE, OPTIONS, etc.)
    'allowed_methods' => ['*'],

    // Allow requests from any origin
    'allowed_origins' => ['*'],

    // No origin patterns
    'allowed_origins_patterns' => [],

    // Allow all headers
    'allowed_headers' => ['*'],

    // No custom exposed headers
    'exposed_headers' => [],

    // Cache preflight response for 1 hour (optional)
    'max_age' => 3600,

    // Set to true only if you need cookies/auth headers across domains
    'supports_credentials' => false,

];