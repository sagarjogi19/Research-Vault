<?php

declare(strict_types=1);

namespace App\Repositories;

final class UserRepository extends Repository
{
    public function findById(int $id): ?array
    {
        return $this->db->fetch('SELECT id, name, email, role, status, created_at FROM users WHERE id = :id LIMIT 1', [
            'id' => $id,
        ]);
    }

    public function findByEmail(string $email): ?array
    {
        return $this->db->fetch('SELECT * FROM users WHERE email = :email LIMIT 1', [
            'email' => strtolower($email),
        ]);
    }

    public function create(array $data): int
    {
        return $this->db->insert('users', [
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password' => $data['password'],
            'role' => $data['role'] ?? 'student',
            'status' => $data['status'] ?? 'active',
        ]);
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql = 'SELECT id FROM users WHERE email = :email';
        $params = ['email' => strtolower($email)];

        if ($excludeId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $excludeId;
        }

        return $this->db->fetch($sql . ' LIMIT 1', $params) !== null;
    }

    public function countByRole(string $role): int
    {
        $row = $this->db->fetch('SELECT COUNT(*) AS total FROM users WHERE role = :role', [
            'role' => $role,
        ]);

        return (int) ($row['total'] ?? 0);
    }

    public function recentStudents(int $limit = 5): array
    {
        return $this->db->fetchAll(
            'SELECT id, name, email, created_at FROM users WHERE role = :role ORDER BY created_at DESC LIMIT ' . (int) $limit,
            ['role' => 'student']
        );
    }
}
