<?php

declare(strict_types=1);

namespace App\Repositories;

final class PurchaseRepository extends Repository
{
    public function findByUserAndPaper(int $userId, int $paperId): ?array
    {
        return $this->db->fetch(
            'SELECT * FROM purchases WHERE user_id = :user_id AND paper_id = :paper_id LIMIT 1',
            ['user_id' => $userId, 'paper_id' => $paperId]
        );
    }

    public function hasCompletedPurchase(int $userId, int $paperId): bool
    {
        $row = $this->db->fetch(
            'SELECT id FROM purchases
             WHERE user_id = :user_id AND paper_id = :paper_id AND status = :status
             LIMIT 1',
            [
                'user_id' => $userId,
                'paper_id' => $paperId,
                'status' => 'completed',
            ]
        );

        return $row !== null;
    }

    public function create(array $data): int
    {
        return $this->db->insert('purchases', $data);
    }

    public function update(int $id, array $data): int
    {
        return $this->db->update('purchases', $data, 'id = :id', ['id' => $id]);
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetch('SELECT * FROM purchases WHERE id = :id LIMIT 1', [
            'id' => $id,
        ]);
    }

    public function forUser(int $userId): array
    {
        return $this->db->fetchAll(
            'SELECT pu.*, p.title, p.slug, p.authors, p.is_free, p.file_name, c.name AS category_name
             FROM purchases pu
             INNER JOIN papers p ON p.id = pu.paper_id
             INNER JOIN categories c ON c.id = p.category_id
             WHERE pu.user_id = :user_id AND pu.status = :status
             ORDER BY pu.purchased_at DESC',
            ['user_id' => $userId, 'status' => 'completed']
        );
    }

    public function countCompleted(): int
    {
        $row = $this->db->fetch(
            'SELECT COUNT(*) AS total FROM purchases WHERE status = :status',
            ['status' => 'completed']
        );

        return (int) ($row['total'] ?? 0);
    }

    public function revenueTotal(): float
    {
        $row = $this->db->fetch(
            'SELECT COALESCE(SUM(amount), 0) AS total FROM purchases WHERE status = :status',
            ['status' => 'completed']
        );

        return (float) ($row['total'] ?? 0);
    }
}
