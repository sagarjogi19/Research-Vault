<?php

declare(strict_types=1);

namespace App\Repositories;

final class DownloadRepository extends Repository
{
    public function create(array $data): int
    {
        return $this->db->insert('downloads', $data);
    }

    public function forUser(int $userId, int $limit = 20): array
    {
        return $this->db->fetchAll(
            'SELECT d.*, p.title, p.slug, p.authors
             FROM downloads d
             INNER JOIN papers p ON p.id = d.paper_id
             WHERE d.user_id = :user_id
             ORDER BY d.downloaded_at DESC
             LIMIT ' . (int) $limit,
            ['user_id' => $userId]
        );
    }

    public function countAll(): int
    {
        $row = $this->db->fetch('SELECT COUNT(*) AS total FROM downloads');
        return (int) ($row['total'] ?? 0);
    }

    public function countForUser(int $userId): int
    {
        $row = $this->db->fetch(
            'SELECT COUNT(*) AS total FROM downloads WHERE user_id = :user_id',
            ['user_id' => $userId]
        );

        return (int) ($row['total'] ?? 0);
    }
}
