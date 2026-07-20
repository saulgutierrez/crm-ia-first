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
     * Find active (non-deleted) clients with owner info, optionally filtered by owner.
     */
    public function findAllActive(int $page = 1, int $perPage = 15, ?int $ownerId = null): array
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT c.*, u.name AS owner_name
            FROM {$this->table} c
            LEFT JOIN users u ON u.id = c.owner_id
            WHERE c.deleted_at IS NULL";
        $params = [];

        if ($ownerId !== null) {
            $sql .= " AND c.owner_id = :owner_id";
            $params['owner_id'] = $ownerId;
        }

        $sql .= " ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        foreach ($params as $key => $val) {
            $stmt->bindValue(":{$key}", $val);
        }
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
     * Search clients by name, email, or industry, optionally filtered by owner.
     */
    public function search(string $term, int $page = 1, int $perPage = 15, ?int $ownerId = null): array
    {
        $offset = ($page - 1) * $perPage;
        $like = '%' . $term . '%';

        $stmt = $this->db->prepare("
            SELECT c.*, u.name AS owner_name
            FROM {$this->table} c
            LEFT JOIN users u ON u.id = c.owner_id
            WHERE c.deleted_at IS NULL
            AND (c.company_name LIKE :term OR c.email LIKE :term OR c.industry LIKE :term)
            " . ($ownerId !== null ? "AND c.owner_id = :owner_id" : "") . "
            ORDER BY c.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue('term', $like, \PDO::PARAM_STR);
        $stmt->bindValue('limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        if ($ownerId !== null) {
            $stmt->bindValue('owner_id', $ownerId, \PDO::PARAM_INT);
        }
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Count active clients matching a search term, optionally filtered by owner.
     */
    public function countSearch(string $term, ?int $ownerId = null): int
    {
        $like = '%' . $term . '%';
        $sql = "SELECT COUNT(*) AS total
            FROM {$this->table}
            WHERE deleted_at IS NULL
            AND (company_name LIKE :term OR email LIKE :term OR industry LIKE :term)";
        $params = ['term' => $like];

        if ($ownerId !== null) {
            $sql .= " AND owner_id = :owner_id";
            $params['owner_id'] = $ownerId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetch()->total;
    }

    /**
     * Count active (non-deleted) clients, optionally filtered by owner.
     */
    public function countActive(?int $ownerId = null): int
    {
        $where = 'deleted_at IS NULL';
        if ($ownerId !== null) {
            $where .= ' AND owner_id = ' . (int) $ownerId;
        }
        return $this->count($where);
    }
}
