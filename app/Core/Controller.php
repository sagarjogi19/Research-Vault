<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = [], ?string $layout = 'layouts/main'): void
    {
        View::render($view, $data, $layout);
    }

    protected function redirect(string $path): never
    {
        redirect($path);
    }

    protected function back(string $fallback = '/'): never
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? null;
        redirect($referer ?: $fallback);
    }

    protected function withOld(array $data): void
    {
        $_SESSION['_old'] = $data;
    }

    protected function clearOld(): void
    {
        unset($_SESSION['_old']);
    }

    protected function validateCsrf(): void
    {
        $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!verify_csrf(is_string($token) ? $token : null)) {
            http_response_code(419);
            flash('error', 'Invalid security token. Please try again.');
            $this->back();
        }
    }

    protected function requireAuth(): array
    {
        $user = auth_user();
        if ($user === null) {
            flash('error', 'Please log in to continue.');
            $this->redirect('/login');
        }

        return $user;
    }
}
