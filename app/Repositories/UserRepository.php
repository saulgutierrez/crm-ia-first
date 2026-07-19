<?php

declare(strict_types=1);

namespace App\Repositories;

class UserRepository extends BaseRepository
{
    protected string $table = 'users';

    /**
     * Find a user by their email address.
     */
    public function findByEmail(string $email): ?\stdClass
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1"
        );
        $stmt->execute(['email' => $email]);

        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Get all active agents.
     */
    public function findAllAgents(): array
    {
        $stmt = $this->db->prepare(
            "SELECT id, name, email FROM {$this->table} WHERE is_active = 1 AND role = 'agent' ORDER BY name ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get all active users (for dropdowns).
     */
    public function findAllActive(): array
    {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, role FROM {$this->table} WHERE is_active = 1 ORDER BY name ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
