<?php

declare(strict_types=1);

namespace App\Middleware;

final class AdminMiddleware extends Middleware
{
    public function handle(): void
    {
        if (!is_authenticated()) {
            flash('error', 'Please log in to continue.');
            redirect('/login');
        }

        if (!is_admin()) {
            flash('error', 'You do not have permission to access that area.');
            redirect('/');
        }
    }
}
