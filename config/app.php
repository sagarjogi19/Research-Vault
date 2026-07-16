<?php

return [
    'name' => env('APP_NAME', 'ResearchVault'),
    'env' => env('APP_ENV', 'local'),
    'debug' => filter_var(env('APP_DEBUG', true), FILTER_VALIDATE_BOOLEAN),
    'url' => rtrim(env('APP_URL', 'http://localhost'), '/'),
    'timezone' => env('APP_TIMEZONE', 'Africa/Lagos'),
    'currency' => env('APP_CURRENCY', 'NGN'),
    'currency_symbol' => env('APP_CURRENCY_SYMBOL', '₦'),
];
