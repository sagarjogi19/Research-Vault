<?php

declare(strict_types=1);

namespace App\Middleware;

final class GuestMiddleware extends Middleware
{
    public function handle(): void
    {
        if (is_authenticated()) {
            redirect(is_admin() ? '/admin' : '/dashboard');
        }
    }
}
