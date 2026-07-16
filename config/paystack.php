<?php

return [
    'public_key' => env('PAYSTACK_PUBLIC_KEY', ''),
    'secret_key' => env('PAYSTACK_SECRET_KEY', ''),
    'callback_url' => env('APP_URL', 'http://localhost') . '/payment/callback',
    'currency' => env('PAYSTACK_CURRENCY', 'NGN'),
];
