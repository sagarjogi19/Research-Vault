<?php

declare(strict_types=1);

namespace App\Repositories;

final class PaperRepository extends Repository
{
    public function findById(int $id): ?array
    {
        return $this->db->fetch(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug, u.name AS uploader_name
             FROM papers p
             INNER JOIN categories c ON c.id = p.category_id
             INNER JOIN users u ON u.id = p.uploaded_by
             WHERE p.id = :id
             LIMIT 1',
            ['id' => $id]
        );
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->db->fetch(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug, u.name AS uploader_name
             FROM papers p
             INNER JOIN categories c ON c.id = p.category_id
             INNER JOIN users u ON u.id = p.uploaded_by
             WHERE p.slug = :slug
             LIMIT 1',
            ['slug' => $slug]
        );
    }

    public function search(array $filters = [], int $page = 1, int $perPage = 12): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'p.status = :status';
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['q'])) {
            $where[] = '(p.title LIKE :q OR p.abstract LIKE :q2 OR p.authors LIKE :q3 OR p.keywords LIKE :q4)';
            $term = '%' . $filters['q'] . '%';
            $params['q'] = $term;
            $params['q2'] = $term;
            $params['q3'] = $term;
            $params['q4'] = $term;
        }

        if (!empty($filters['category_id'])) {
            $where[] = 'p.category_id = :category_id';
            $params['category_id'] = (int) $filters['category_id'];
        }

        if (isset($filters['is_free']) && $filters['is_free'] !== '') {
            $where[] = 'p.is_free = :is_free';
            $params['is_free'] = (int) $filters['is_free'];
        }

        if (!empty($filters['year'])) {
            $where[] = 'p.publication_year = :year';
            $params['year'] = (int) $filters['year'];
        }

        $whereSql = $where === [] ? '1=1' : implode(' AND ', $where);
        $offset = max(0, ($page - 1) * $perPage);

        $countRow = $this->db->fetch(
            "SELECT COUNT(*) AS total FROM papers p WHERE {$whereSql}",
            $params
        );

        $items = $this->db->fetchAll(
            "SELECT p.*, c.name AS category_name
             FROM papers p
             INNER JOIN categories c ON c.id = p.category_id
             WHERE {$whereSql}
             ORDER BY p.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'items' => $items,
            'total' => (int) ($countRow['total'] ?? 0),
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => max(1, (int) ceil(($countRow['total'] ?? 0) / $perPage)),
        ];
    }

    public function create(array $data): int
    {
        return $this->db->insert('papers', $data);
    }

    public function update(int $id, array $data): int
    {
        return $this->db->update('papers', $data, 'id = :id', ['id' => $id]);
    }

    public function delete(int $id): int
    {
        return $this->db->delete('papers', 'id = :id', ['id' => $id]);
    }

    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = 'SELECT id FROM papers WHERE slug = :slug';
        $params = ['slug' => $slug];

        if ($excludeId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $excludeId;
        }

        return $this->db->fetch($sql . ' LIMIT 1', $params) !== null;
    }

    public function incrementDownloads(int $id): void
    {
        $this->db->query('UPDATE papers SET download_count = download_count + 1 WHERE id = :id', [
            'id' => $id,
        ]);
    }

    public function countAll(?string $status = null): int
    {
        if ($status) {
            $row = $this->db->fetch('SELECT COUNT(*) AS total FROM papers WHERE status = :status', [
                'status' => $status,
            ]);
        } else {
            $row = $this->db->fetch('SELECT COUNT(*) AS total FROM papers');
        }

        return (int) ($row['total'] ?? 0);
    }

    public function recent(int $limit = 6): array
    {
        return $this->db->fetchAll(
            'SELECT p.*, c.name AS category_name
             FROM papers p
             INNER JOIN categories c ON c.id = p.category_id
             WHERE p.status = :status
             ORDER BY p.created_at DESC
             LIMIT ' . (int) $limit,
            ['status' => 'published']
        );
    }

    public function popular(int $limit = 6): array
    {
        return $this->db->fetchAll(
            'SELECT p.*, c.name AS category_name
             FROM papers p
             INNER JOIN categories c ON c.id = p.category_id
             WHERE p.status = :status
             ORDER BY p.download_count DESC, p.created_at DESC
             LIMIT ' . (int) $limit,
            ['status' => 'published']
        );
    }

    public function years(): array
    {
        $rows = $this->db->fetchAll(
            'SELECT DISTINCT publication_year FROM papers
             WHERE publication_year IS NOT NULL AND status = :status
             ORDER BY publication_year DESC',
            ['status' => 'published']
        );

        return array_column($rows, 'publication_year');
    }
}
