<?php

declare(strict_types=1);

namespace App\Repositories;

class ClientRepository extends BaseRepository
{
    protected string $table = 'clients';

    /**
     * Soft-delete a client.
     */
    public function softDelete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = :id"
        );
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Find active (non-deleted) clients with owner info.
     */
    public function findAllActive(int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;

        $stmt = $this->db->prepare("
            SELECT c.*, u.name AS owner_name
            FROM {$this->table} c
            LEFT JOIN users u ON u.id = c.owner_id
            WHERE c.deleted_at IS NULL
            ORDER BY c.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue('limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Find a client with its owner, excluding soft-deleted.
     */
    public function findWithOwner(int $id): ?\stdClass
    {
        $stmt = $this->db->prepare("
            SELECT c.*, u.name AS owner_name
            FROM {$this->table} c
            LEFT JOIN users u ON u.id = c.owner_id
            WHERE c.id = :id AND c.deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);

        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Search clients by name, email, or industry.
     */
    public function search(string $term, int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $like = '%' . $term . '%';

        $stmt = $this->db->prepare("
            SELECT c.*, u.name AS owner_name
            FROM {$this->table} c
            LEFT JOIN users u ON u.id = c.owner_id
            WHERE c.deleted_at IS NULL
            AND (c.company_name LIKE :term OR c.email LIKE :term OR c.industry LIKE :term)
            ORDER BY c.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue('term', $like, \PDO::PARAM_STR);
        $stmt->bindValue('limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Count active clients matching a search term.
     */
    public function countSearch(string $term): int
    {
        $like = '%' . $term . '%';
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM {$this->table}
            WHERE deleted_at IS NULL
            AND (company_name LIKE :term OR email LIKE :term OR industry LIKE :term)
        ");
        $stmt->execute(['term' => $like]);

        return (int) $stmt->fetch()->total;
    }

    /**
     * Count active (non-deleted) clients.
     */
    public function countActive(): int
    {
        return $this->count('deleted_at IS NULL');
    }
}
