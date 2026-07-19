<?php

declare(strict_types=1);

namespace App\Models;

class Client
{
    public ?int $id = null;
    public string $companyName;
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $industry = null;
    public ?int $ownerId = null;
    public ?string $deletedAt = null;
    public ?string $createdAt = null;
    public ?string $updatedAt = null;

    /**
     * Create a Client model from a database row.
     */
    public static function fromRow(\stdClass $row): self
    {
        $model = new self();
        $model->id = (int) $row->id;
        $model->companyName = $row->company_name;
        $model->email = $row->email;
        $model->phone = $row->phone;
        $model->industry = $row->industry;
        $model->ownerId = $row->owner_id ? (int) $row->owner_id : null;
        $model->deletedAt = $row->deleted_at;
        $model->createdAt = $row->created_at;
        $model->updatedAt = $row->updated_at;

        return $model;
    }

    /**
     * Convert model to an array for API responses.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'company_name' => $this->companyName,
            'email' => $this->email,
            'phone' => $this->phone,
            'industry' => $this->industry,
            'owner_id' => $this->ownerId,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
