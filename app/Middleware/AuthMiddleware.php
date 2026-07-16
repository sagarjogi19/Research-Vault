<?php

declare(strict_types=1);

namespace App\Middleware;

final class AuthMiddleware extends Middleware
{
    public function handle(): void
    {
        if (!is_authenticated()) {
            flash('error', 'Please log in to continue.');
            redirect('/login');
        }
    }
}
