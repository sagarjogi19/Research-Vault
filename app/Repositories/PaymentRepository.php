<?php

declare(strict_types=1);

namespace App\Repositories;

final class PaymentRepository extends Repository
{
    public function create(array $data): int
    {
        return $this->db->insert('payments', $data);
    }

    public function findByReference(string $reference): ?array
    {
        return $this->db->fetch('SELECT * FROM payments WHERE reference = :reference LIMIT 1', [
            'reference' => $reference,
        ]);
    }

    public function update(int $id, array $data): int
    {
        return $this->db->update('payments', $data, 'id = :id', ['id' => $id]);
    }

    public function recent(int $limit = 10): array
    {
        return $this->db->fetchAll(
            'SELECT pay.*, u.name AS user_name, u.email AS user_email, p.title AS paper_title
             FROM payments pay
             INNER JOIN users u ON u.id = pay.user_id
             INNER JOIN purchases pu ON pu.id = pay.purchase_id
             INNER JOIN papers p ON p.id = pu.paper_id
             ORDER BY pay.created_at DESC
             LIMIT ' . (int) $limit
        );
    }
}
