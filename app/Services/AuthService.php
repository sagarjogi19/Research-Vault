<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use App\Core\Validator;
use App\Repositories\UserRepository;

final class AuthService
{
    public function __construct(
        private readonly UserRepository $users = new UserRepository()
    ) {
    }

    public function register(array $input): array
    {
        $validator = Validator::make($input)
            ->required('name', 'Full name')
            ->max('name', 120, 'Full name')
            ->required('email')
            ->email('email')
            ->required('password')
            ->min('password', 8)
            ->confirmed('password');

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->firstErrors()];
        }

        if ($this->users->emailExists($input['email'])) {
            return ['success' => false, 'errors' => ['email' => 'This email is already registered.']];
        }

        $userId = $this->users->create([
            'name' => trim($input['name']),
            'email' => trim($input['email']),
            'password' => password_hash($input['password'], PASSWORD_DEFAULT),
            'role' => 'student',
        ]);

        $user = $this->users->findById($userId);
        $this->loginUser($user);

        return ['success' => true, 'user' => $user];
    }

    public function login(array $input): array
    {
        $validator = Validator::make($input)
            ->required('email')
            ->email('email')
            ->required('password');

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->firstErrors()];
        }

        $user = $this->users->findByEmail(trim($input['email']));
        if ($user === null || !password_verify($input['password'], $user['password'])) {
            return ['success' => false, 'errors' => ['email' => 'Invalid email or password.']];
        }

        if ($user['status'] !== 'active') {
            return ['success' => false, 'errors' => ['email' => 'Your account is inactive. Contact support.']];
        }

        unset($user['password']);
        $this->loginUser($user);

        return ['success' => true, 'user' => $user];
    }

    public function logout(): void
    {
        Session::destroy();
        Session::start();
    }

    private function loginUser(array $user): void
    {
        Session::regenerate();
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
    }
}
