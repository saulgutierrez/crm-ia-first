<?php

declare(strict_types=1);

namespace App\Repositories;

abstract class BaseRepository
{
    protected \PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find a record by its primary key.
     */
    public function find(int $id): ?\stdClass
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Get all records with optional pagination.
     */
    public function findAll(int $page = 1, int $perPage = 15, string $orderBy = 'created_at', string $orderDir = 'DESC'): array
    {
        $offset = ($page - 1) * $perPage;
        $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';

        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} ORDER BY {$orderBy} {$orderDir} LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue('limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Count total records.
     */
    public function count(string $where = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table}";
        if ($where) {
            $sql .= " WHERE {$where}";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetch()->total;
    }

    /**
     * Insert a record and return the last insert ID.
     */
    public function create(array $data): string
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})"
        );
        $stmt->execute($data);

        return $this->db->lastInsertId();
    }

    /**
     * Update a record by its primary key.
     */
    public function update(int $id, array $data): bool
    {
        $sets = '';
        foreach (array_keys($data) as $column) {
            $sets .= "{$column} = :{$column}, ";
        }
        $sets = rtrim($sets, ', ');

        $data[$this->primaryKey] = $id;

        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET {$sets} WHERE {$this->primaryKey} = :{$this->primaryKey}"
        );

        return $stmt->execute($data);
    }

    /**
     * Delete a record by its primary key.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id"
        );

        return $stmt->execute(['id' => $id]);
    }

    /**
     * Execute a raw query with parameters.
     */
    public function raw(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Check if a record exists by a given column/value pair.
     */
    public function exists(string $column, mixed $value, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table} WHERE {$column} = :value";
        $params = ['value' => $value];

        if ($excludeId !== null) {
            $sql .= " AND {$this->primaryKey} != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetch()->total > 0;
    }
}
