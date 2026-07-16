<?php

declare(strict_types=1);

namespace App\Repositories;

final class CategoryRepository extends Repository
{
    public function all(): array
    {
        return $this->db->fetchAll('SELECT * FROM categories ORDER BY name ASC');
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetch('SELECT * FROM categories WHERE id = :id LIMIT 1', [
            'id' => $id,
        ]);
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->db->fetch('SELECT * FROM categories WHERE slug = :slug LIMIT 1', [
            'slug' => $slug,
        ]);
    }

    public function withPaperCounts(): array
    {
        return $this->db->fetchAll(
            'SELECT c.*, COUNT(p.id) AS paper_count
             FROM categories c
             LEFT JOIN papers p ON p.category_id = c.id AND p.status = :status
             GROUP BY c.id
             ORDER BY c.name ASC',
            ['status' => 'published']
        );
    }
}
