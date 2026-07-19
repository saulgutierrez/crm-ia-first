<?php

declare(strict_types=1);

namespace App\Models;

class User
{
    public ?int $id = null;
    public string $name;
    public string $email;
    public string $passwordHash;
    public string $role = 'agent';
    public bool $isActive = true;
    public ?string $createdAt = null;
    public ?string $updatedAt = null;

    /**
     * Create a User model from a database row.
     */
    public static function fromRow(\stdClass $row): self
    {
        $model = new self();
        $model->id = (int) $row->id;
        $model->name = $row->name;
        $model->email = $row->email;
        $model->passwordHash = $row->password_hash;
        $model->role = $row->role;
        $model->isActive = (bool) $row->is_active;
        $model->createdAt = $row->created_at;
        $model->updatedAt = $row->updated_at;

        return $model;
    }

    /**
     * Convert model to an array for API responses (excludes sensitive fields).
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'is_active' => $this->isActive,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
