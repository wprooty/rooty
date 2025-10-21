<?php

return [

    // List of providers -> Loaded before bootstrap/providers.php
    'providers' => [],

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | We reuse the WordPress blog name if available, otherwise fall back
    | to the APP_NAME env variable or "Rooty".
    |
    */

    'name' => (function () {
        if (function_exists('get_bloginfo')) {
            return get_bloginfo('name') ?: env('APP_NAME', 'Rooty');
        }
        return env('APP_NAME', 'Rooty');
    })(),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Arch command line tool. You should set this to the root of
    | the application so that it's available within Arch commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | We reuse the WordPress timezone setting if available, otherwise fall
    | back to UTC. This ensures compatibility outside of WP runtime (CLI,
    | tests, etc.).
    |
    */

    'timezone' => app()->resolveTimezone(),

    /*
    |--------------------------------------------------------------------------
    | 
    |--------------------------------------------------------------------------
    |
    | 
    |
    */

    'encoding' => get_bloginfo('charset'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

];
