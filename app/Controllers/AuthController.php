<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;

final class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $auth = new AuthService()
    ) {
    }

    public function showLogin(): void
    {
        $this->view('auth/login', [
            'title' => 'Sign In',
            'errors' => flash('errors') ?? [],
        ]);
    }

    public function login(): void
    {
        $this->validateCsrf();
        $input = [
            'email' => trim((string) input('email', '')),
            'password' => (string) input('password', ''),
        ];

        $result = $this->auth->login($input);
        if (!$result['success']) {
            $this->withOld(['email' => $input['email']]);
            flash('errors', $result['errors']);
            flash('error', 'Unable to sign in. Check your credentials.');
            $this->redirect('/login');
        }

        $this->clearOld();
        flash('success', 'Welcome back, ' . $result['user']['name'] . '!');
        $this->redirect($result['user']['role'] === 'admin' ? '/admin' : '/dashboard');
    }

    public function showRegister(): void
    {
        $this->view('auth/register', [
            'title' => 'Create Student Account',
            'errors' => flash('errors') ?? [],
        ]);
    }

    public function register(): void
    {
        $this->validateCsrf();
        $input = [
            'name' => trim((string) input('name', '')),
            'email' => trim((string) input('email', '')),
            'password' => (string) input('password', ''),
            'password_confirmation' => (string) input('password_confirmation', ''),
        ];

        $result = $this->auth->register($input);
        if (!$result['success']) {
            $this->withOld([
                'name' => $input['name'],
                'email' => $input['email'],
            ]);
            flash('errors', $result['errors']);
            flash('error', 'Please fix the highlighted errors.');
            $this->redirect('/register');
        }

        $this->clearOld();
        flash('success', 'Account created successfully. Welcome to ResearchVault!');
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        $this->validateCsrf();
        $this->auth->logout();
        flash('success', 'You have been signed out.');
        $this->redirect('/login');
    }
}
